<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * A discount code applied to subscription billing. Discounts are computed against
 * a rupee amount; percentage coupons never reduce a bill below zero.
 */
class Coupon extends Model
{
    public const TYPE_PERCENT = 'percent';
    public const TYPE_FIXED   = 'fixed';

    protected $fillable = [
        'code', 'description', 'type', 'value',
        'max_redemptions', 'redeemed_count', 'expires_at', 'is_active',
    ];

    protected $casts = [
        'value'           => 'decimal:2',
        'max_redemptions' => 'integer',
        'redeemed_count'  => 'integer',
        'expires_at'      => 'datetime',
        'is_active'       => 'boolean',
    ];

    /** Case-insensitive lookup by code. */
    public static function findByCode(string $code): ?self
    {
        return static::whereRaw('LOWER(code) = ?', [mb_strtolower(trim($code))])->first();
    }

    /** Redeemable right now: active, not expired, and redemptions left. */
    public function isRedeemable(): bool
    {
        if (! $this->is_active) {
            return false;
        }

        if ($this->expires_at && $this->expires_at->isPast()) {
            return false;
        }

        if ($this->max_redemptions !== null && $this->redeemed_count >= $this->max_redemptions) {
            return false;
        }

        return true;
    }

    /** The discount, in rupees, this coupon applies to a given amount. */
    public function discountFor(float $amount): float
    {
        $discount = $this->type === self::TYPE_PERCENT
            ? $amount * ((float) $this->value / 100)
            : (float) $this->value;

        return round(min($discount, $amount), 2);
    }

    /** Atomically record a redemption. */
    public function redeem(): void
    {
        $this->increment('redeemed_count');
    }
}
