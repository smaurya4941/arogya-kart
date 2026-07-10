<?php

namespace App\Services;

use App\Models\Invoice;
use App\Models\Pharmacy;
use App\Models\Plan;
use App\Models\Subscription;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

/**
 * Turns a successful payment into an active subscription + a paid invoice.
 * Kept idempotent on the Razorpay payment id so that the browser callback and
 * the webhook — which can both fire for the same payment — never double-charge
 * or create duplicate records.
 */
class BillingService
{
    /**
     * Activate (or renew) a pharmacy's subscription after a captured payment.
     */
    public function activatePaidSubscription(
        Pharmacy $pharmacy,
        Plan $plan,
        string $billingCycle,
        string $paymentId,
        ?string $orderId = null,
    ): Subscription {
        return DB::transaction(function () use ($pharmacy, $plan, $billingCycle, $paymentId, $orderId) {
            // Idempotency: if we already recorded this payment, return as-is.
            $existing = Invoice::withoutGlobalScopes()
                ->where('transaction_id', $paymentId)
                ->first();

            if ($existing) {
                return $existing->subscription
                    ?? $pharmacy->subscriptions()->latest()->firstOrFail();
            }

            $endsAt = $this->periodEnd($billingCycle);

            // Reuse the pharmacy's latest subscription row (renewal) or start one.
            $subscription = $pharmacy->subscriptions()->latest()->first();

            if ($subscription) {
                $subscription->update([
                    'plan_id'       => $plan->id,
                    'status'        => Subscription::STATUS_ACTIVE,
                    'billing_cycle' => $billingCycle,
                    'starts_at'     => now(),
                    'ends_at'       => $endsAt,
                    'razorpay_id'   => $orderId,
                ]);
            } else {
                $subscription = $pharmacy->subscriptions()->create([
                    'plan_id'       => $plan->id,
                    'status'        => Subscription::STATUS_ACTIVE,
                    'billing_cycle' => $billingCycle,
                    'starts_at'     => now(),
                    'ends_at'       => $endsAt,
                    'razorpay_id'   => $orderId,
                ]);
            }

            $amount = $plan->priceFor($billingCycle);
            $tax    = round($amount * (float) config('saas.gst_percent', 18) / 100, 2);

            Invoice::create([
                'pharmacy_id'     => $pharmacy->id,
                'subscription_id' => $subscription->id,
                'invoice_number'  => Invoice::nextNumber(),
                'amount'          => $amount,
                'tax'             => $tax,
                'total'           => $amount + $tax,
                'status'          => Invoice::STATUS_PAID,
                'payment_method'  => 'razorpay',
                'transaction_id'  => $paymentId,
                'paid_at'         => now(),
            ]);

            return $subscription;
        });
    }

    private function periodEnd(string $billingCycle): Carbon
    {
        return match ($billingCycle) {
            'yearly'    => now()->addYear(),
            'quarterly' => now()->addMonths(3),
            default     => now()->addMonth(),
        };
    }
}
