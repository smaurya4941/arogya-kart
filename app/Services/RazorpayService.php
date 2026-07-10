<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use RuntimeException;

/**
 * Thin Razorpay client built on Laravel's HTTP layer + hash_hmac, so the SaaS
 * has no hard dependency on the razorpay/razorpay SDK. It covers exactly what the
 * subscription checkout needs:
 *   - create an Order (server-side, the amount the browser cannot tamper with)
 *   - verify the signature returned by Checkout.js after payment
 *   - verify the signature on server-to-server webhooks
 *
 * All amounts are handled in the smallest currency unit (paise for INR), which is
 * what the Razorpay API expects.
 */
class RazorpayService
{
    private const API_BASE = 'https://api.razorpay.com/v1';

    public function __construct(
        private readonly ?string $key = null,
        private readonly ?string $secret = null,
        private readonly ?string $webhookSecret = null,
    ) {
    }

    public static function make(): self
    {
        return new self(
            config('services.razorpay.key'),
            config('services.razorpay.secret'),
            config('services.razorpay.webhook_secret'),
        );
    }

    public function isConfigured(): bool
    {
        return ! empty($this->key) && ! empty($this->secret);
    }

    public function publishableKey(): ?string
    {
        return $this->key;
    }

    /**
     * Create a Razorpay order.
     *
     * @param  float  $amount   Amount in rupees (converted to paise internally).
     * @param  array<string,mixed>  $notes  Metadata echoed back on the payment.
     * @return array<string,mixed> The order payload (contains `id`, `amount`, ...).
     */
    public function createOrder(float $amount, string $receipt, array $notes = []): array
    {
        $this->assertConfigured();

        $response = Http::withBasicAuth($this->key, $this->secret)
            ->asJson()
            ->post(self::API_BASE . '/orders', [
                'amount'          => (int) round($amount * 100), // paise
                'currency'        => config('saas.currency', 'INR'),
                'receipt'         => $receipt,
                'payment_capture' => 1,
                'notes'           => $notes,
            ]);

        if ($response->failed()) {
            throw new RuntimeException('Razorpay order creation failed: ' . $response->body());
        }

        return $response->json();
    }

    /**
     * Verify the signature Checkout.js returns to the browser after a successful
     * payment. The signature is HMAC-SHA256(order_id|payment_id, key_secret).
     */
    public function verifyPaymentSignature(string $orderId, string $paymentId, string $signature): bool
    {
        $this->assertConfigured();

        $expected = hash_hmac('sha256', $orderId . '|' . $paymentId, $this->secret);

        return hash_equals($expected, $signature);
    }

    /**
     * Verify a webhook payload against the X-Razorpay-Signature header using the
     * dedicated webhook secret (distinct from the API secret).
     */
    public function verifyWebhookSignature(string $rawBody, string $signature): bool
    {
        if (empty($this->webhookSecret)) {
            return false;
        }

        $expected = hash_hmac('sha256', $rawBody, $this->webhookSecret);

        return hash_equals($expected, $signature);
    }

    private function assertConfigured(): void
    {
        if (! $this->isConfigured()) {
            throw new RuntimeException('Razorpay is not configured. Set RAZORPAY_KEY and RAZORPAY_SECRET in your environment.');
        }
    }
}
