<?php

namespace App\Services;

use App\Models\Coupon;
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
        ?Coupon $coupon = null,
    ): Subscription {
        return DB::transaction(function () use ($pharmacy, $plan, $billingCycle, $paymentId, $orderId, $coupon) {
            // Idempotency: if we already recorded this payment, return as-is. This
            // also guarantees a coupon is only ever redeemed once per payment, even
            // when the browser callback and the webhook both fire.
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

            // Apply any coupon discount to the plan price before tax. discountFor()
            // already clamps the discount so the net amount never goes negative.
            $base     = $plan->priceFor($billingCycle);
            $discount = $coupon ? $coupon->discountFor($base) : 0.0;
            $amount   = round($base - $discount, 2);
            $tax      = round($amount * (float) config('saas.gst_percent', 18) / 100, 2);

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

            // Record the redemption once, inside the same transaction as the invoice.
            $coupon?->redeem();

            return $subscription;
        });
    }

    private function periodEnd(string $billingCycle): Carbon
    {
        return $this->periodEndFrom(now(), $billingCycle);
    }

    /** One billing period after a given start date. Used for manual/admin renewals. */
    public function periodEndFrom(Carbon $from, string $billingCycle): Carbon
    {
        return match ($billingCycle) {
            'yearly'    => $from->copy()->addYear(),
            'quarterly' => $from->copy()->addMonths(3),
            default     => $from->copy()->addMonth(),
        };
    }

    /**
     * Raise a billing invoice for a subscription outside the payment-gateway flow
     * (platform-owner action). Amount defaults to the plan's price for the cycle
     * and tax to the configured GST rate; either can be overridden. A 'paid'
     * invoice is stamped with paid_at automatically.
     *
     * @param  array<string,mixed>  $attributes
     */
    public function generateInvoice(Subscription $subscription, array $attributes = []): Invoice
    {
        $status = $attributes['status'] ?? Invoice::STATUS_PENDING;
        $amount = array_key_exists('amount', $attributes)
            ? (float) $attributes['amount']
            : (float) ($subscription->plan?->priceFor($subscription->billing_cycle) ?? 0);
        $tax = array_key_exists('tax', $attributes)
            ? (float) $attributes['tax']
            : round($amount * (float) config('saas.gst_percent', 18) / 100, 2);

        return Invoice::create([
            'pharmacy_id'     => $subscription->pharmacy_id,
            'subscription_id' => $subscription->id,
            'invoice_number'  => Invoice::nextNumber(),
            'amount'          => $amount,
            'tax'             => $tax,
            'total'           => $amount + $tax,
            'status'          => $status,
            'payment_method'  => $attributes['payment_method'] ?? 'manual',
            'transaction_id'  => $attributes['transaction_id'] ?? null,
            'paid_at'         => $status === Invoice::STATUS_PAID ? now() : null,
        ]);
    }
}
