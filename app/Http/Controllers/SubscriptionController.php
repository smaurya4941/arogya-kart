<?php

namespace App\Http\Controllers;

use App\Models\Coupon;
use App\Models\Invoice;
use App\Models\Pharmacy;
use App\Models\Plan;
use App\Models\Subscription;
use App\Services\BillingService;
use App\Services\PlatformSettings;
use App\Services\RazorpayService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class SubscriptionController extends Controller
{
    public function __construct(
        private readonly RazorpayService $razorpay,
        private readonly BillingService $billing,
        private readonly PlatformSettings $settings,
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
            'couponsEnabled'      => $this->settings->bool('feature_coupons'),
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
            'coupon_code'   => ['nullable', 'string', 'max:40'],
        ]);

        $plan     = Plan::active()->findOrFail($validated['plan_id']);
        $pharmacy = auth()->user()->pharmacy;
        $cycle    = $validated['billing_cycle'];

        // Validate the coupon up front (throws a friendly error back to the billing
        // page if invalid) and discount the amount the gateway will charge.
        $coupon = $this->validateCoupon($request->input('coupon_code'));
        $amount = $plan->priceFor($cycle);
        if ($coupon) {
            $amount = round($amount - $coupon->discountFor($amount), 2);
        }

        if (! $this->razorpay->isConfigured()) {
            // Dev fallback — no gateway keys present. Activate immediately with a
            // synthetic transaction id so the rest of the app can be exercised.
            $this->billing->activatePaidSubscription(
                $pharmacy, $plan, $cycle, 'dev_' . uniqid(), null, $coupon
            );

            return redirect()
                ->route('admin.subscription.index')
                ->with('success', "Subscribed to {$plan->name} (dev mode — no payment gateway configured).");
        }

        $order = $this->razorpay->createOrder(
            $amount,
            receipt: 'sub_' . $pharmacy->id . '_' . now()->timestamp,
            notes: array_filter([
                'pharmacy_id' => $pharmacy->id,
                'plan_id'     => $plan->id,
                'cycle'       => $cycle,
                'coupon'      => $coupon?->code,
            ]),
        );

        return view('pharmacy.subscription.checkout', [
            'order'      => $order,
            'plan'       => $plan,
            'cycle'      => $cycle,
            'amount'     => $amount,
            'couponCode' => $coupon?->code,
            'razorKey'   => $this->razorpay->publishableKey(),
            'pharmacy'   => $pharmacy,
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
            'coupon_code'         => ['nullable', 'string', 'max:40'],
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
            // Payment is already captured — resolve leniently so a coupon that
            // lapsed in the seconds since checkout still honours the quoted price.
            $this->resolveCoupon($validated['coupon_code'] ?? null),
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
                    $pharmacy, $plan, $cycle, $entity['id'], $entity['order_id'] ?? null,
                    $this->resolveCoupon($notes['coupon'] ?? null)
                );
            } else {
                Log::warning('Razorpay webhook missing pharmacy/plan notes', ['notes' => $notes]);
            }
        }

        // Refunds settle asynchronously; these events carry the real outcome. Also
        // catches refunds started from the Razorpay dashboard (no local record yet).
        if (in_array($event, ['refund.created', 'refund.processed', 'refund.failed'], true)) {
            $entity = $payload['payload']['refund']['entity'] ?? [];

            if (! empty($entity['id'])) {
                $invoice = $this->billing->reconcileRefund($entity);

                if (! $invoice) {
                    Log::warning('Razorpay refund webhook: no matching invoice', [
                        'event'      => $event,
                        'refund_id'  => $entity['id'] ?? null,
                        'payment_id' => $entity['payment_id'] ?? null,
                    ]);
                }
            }
        }

        return response()->json(['status' => 'ok']);
    }

    /*
    |--------------------------------------------------------------------------
    | Coupons
    |--------------------------------------------------------------------------
    */

    /**
     * Strict, pre-payment validation used by subscribe(): rejects the request with
     * a friendly error when a code is supplied but the feature is off, or the code
     * is unknown / expired / depleted. Returns null when no code was entered.
     */
    private function validateCoupon(?string $code): ?Coupon
    {
        $code = trim((string) $code);

        if ($code === '') {
            return null;
        }

        if (! $this->settings->bool('feature_coupons')) {
            throw ValidationException::withMessages(['coupon_code' => 'Coupons are not available right now.']);
        }

        $coupon = Coupon::findByCode($code);

        if (! $coupon || ! $coupon->isRedeemable()) {
            throw ValidationException::withMessages(['coupon_code' => 'This coupon code is invalid or has expired.']);
        }

        return $coupon;
    }

    /**
     * Lenient, post-payment resolution used by callback()/webhook(): the money is
     * already captured, so we look the code up without re-validating redeemability
     * (never throwing). Returns null when the feature is off or the code is unknown.
     */
    private function resolveCoupon(?string $code): ?Coupon
    {
        $code = trim((string) $code);

        if ($code === '' || ! $this->settings->bool('feature_coupons')) {
            return null;
        }

        return Coupon::findByCode($code);
    }
}
