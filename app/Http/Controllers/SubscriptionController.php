<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\Pharmacy;
use App\Models\Plan;
use App\Models\Subscription;
use App\Services\BillingService;
use App\Services\RazorpayService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class SubscriptionController extends Controller
{
    public function __construct(
        private readonly RazorpayService $razorpay,
        private readonly BillingService $billing,
    ) {
    }

    /**
     * Billing home: current subscription, the plan catalogue and invoice history.
     */
    public function index()
    {
        $pharmacy = auth()->user()->pharmacy;

        $currentSubscription = Subscription::with('plan')->latest()->first();
        $plans    = Plan::active()->orderBy('price_monthly')->get();
        $invoices = Invoice::latest()->take(12)->get();

        return view('pharmacy.subscription.index', [
            'currentSubscription' => $currentSubscription,
            'plans'               => $plans,
            'invoices'            => $invoices,
            'pharmacy'            => $pharmacy,
        ]);
    }

    /**
     * Start a subscription purchase. With Razorpay configured we create a server-
     * side order and hand the browser to Checkout.js. Without keys (local/dev) we
     * fall back to activating the plan directly so the flow stays testable.
     */
    public function subscribe(Request $request)
    {
        $validated = $request->validate([
            'plan_id'       => ['required', 'exists:plans,id'],
            'billing_cycle' => ['required', 'in:monthly,quarterly,yearly'],
        ]);

        $plan     = Plan::active()->findOrFail($validated['plan_id']);
        $pharmacy = auth()->user()->pharmacy;
        $cycle    = $validated['billing_cycle'];
        $amount   = $plan->priceFor($cycle);

        if (! $this->razorpay->isConfigured()) {
            // Dev fallback — no gateway keys present. Activate immediately with a
            // synthetic transaction id so the rest of the app can be exercised.
            $this->billing->activatePaidSubscription(
                $pharmacy, $plan, $cycle, 'dev_' . uniqid(), null
            );

            return redirect()
                ->route('admin.subscription.index')
                ->with('success', "Subscribed to {$plan->name} (dev mode — no payment gateway configured).");
        }

        $order = $this->razorpay->createOrder(
            $amount,
            receipt: 'sub_' . $pharmacy->id . '_' . now()->timestamp,
            notes: ['pharmacy_id' => $pharmacy->id, 'plan_id' => $plan->id, 'cycle' => $cycle],
        );

        return view('pharmacy.subscription.checkout', [
            'order'    => $order,
            'plan'     => $plan,
            'cycle'    => $cycle,
            'amount'   => $amount,
            'razorKey' => $this->razorpay->publishableKey(),
            'pharmacy' => $pharmacy,
        ]);
    }

    /**
     * Browser callback after Checkout.js completes. Verifies the payment signature
     * before granting access.
     */
    public function callback(Request $request)
    {
        $validated = $request->validate([
            'razorpay_order_id'   => ['required', 'string'],
            'razorpay_payment_id' => ['required', 'string'],
            'razorpay_signature'  => ['required', 'string'],
            'plan_id'             => ['required', 'exists:plans,id'],
            'billing_cycle'       => ['required', 'in:monthly,quarterly,yearly'],
        ]);

        $ok = $this->razorpay->verifyPaymentSignature(
            $validated['razorpay_order_id'],
            $validated['razorpay_payment_id'],
            $validated['razorpay_signature'],
        );

        if (! $ok) {
            return redirect()
                ->route('admin.subscription.index')
                ->with('error', 'Payment verification failed. If money was deducted it will be auto-refunded.');
        }

        $plan     = Plan::findOrFail($validated['plan_id']);
        $pharmacy = auth()->user()->pharmacy;

        $this->billing->activatePaidSubscription(
            $pharmacy,
            $plan,
            $validated['billing_cycle'],
            $validated['razorpay_payment_id'],
            $validated['razorpay_order_id'],
        );

        return redirect()
            ->route('admin.subscription.index')
            ->with('success', "Payment successful — you're now on the {$plan->name} plan.");
    }

    /**
     * Server-to-server webhook (no auth, CSRF-exempt). Razorpay is the source of
     * truth for payment state; this makes activation resilient even if the user
     * closes the browser before the callback fires.
     */
    public function webhook(Request $request)
    {
        $signature = $request->header('X-Razorpay-Signature', '');

        if (! $this->razorpay->verifyWebhookSignature($request->getContent(), $signature)) {
            return response()->json(['message' => 'Invalid signature'], 400);
        }

        $payload = $request->json()->all();
        $event   = $payload['event'] ?? null;

        if ($event === 'payment.captured') {
            $entity = $payload['payload']['payment']['entity'] ?? [];
            $notes  = $entity['notes'] ?? [];

            $pharmacy = Pharmacy::find($notes['pharmacy_id'] ?? null);
            $plan     = Plan::find($notes['plan_id'] ?? null);
            $cycle    = $notes['cycle'] ?? 'monthly';

            if ($pharmacy && $plan && ! empty($entity['id'])) {
                $this->billing->activatePaidSubscription(
                    $pharmacy, $plan, $cycle, $entity['id'], $entity['order_id'] ?? null
                );
            } else {
                Log::warning('Razorpay webhook missing pharmacy/plan notes', ['notes' => $notes]);
            }
        }

        return response()->json(['status' => 'ok']);
    }
}
