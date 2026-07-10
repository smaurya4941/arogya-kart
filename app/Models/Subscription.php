<?php

namespace App\Models;

use App\Traits\BelongsToPharmacy;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Subscription extends Model
{
    use BelongsToPharmacy;
    use SoftDeletes;

    public const STATUS_TRIAL     = 'trial';
    public const STATUS_ACTIVE    = 'active';
    public const STATUS_EXPIRED   = 'expired';
    public const STATUS_CANCELLED = 'cancelled';
    public const STATUS_SUSPENDED = 'suspended';

    protected $fillable = [
        'pharmacy_id',
        'plan_id',
        'status',
        'billing_cycle',
        'starts_at',
        'ends_at',
        'trial_ends_at',
        'razorpay_id',
        'razorpay_plan_id',
        'quantity',
    ];

    protected $casts = [
        'starts_at'     => 'datetime',
        'ends_at'       => 'datetime',
        'trial_ends_at' => 'datetime',
        'quantity'      => 'integer',
    ];

    public function plan()
    {
        return $this->belongsTo(Plan::class);
    }

    public function invoices()
    {
        return $this->hasMany(Invoice::class);
    }

    /**
     * True while this subscription still grants access — either a live trial or a
     * paid period that has not lapsed. This is the single source of truth used by
     * EnsureSubscriptionActive and the billing UI, so gating logic never drifts.
     */
    public function isValid(): bool
    {
        return match ($this->status) {
            self::STATUS_TRIAL  => $this->trial_ends_at !== null && $this->trial_ends_at->isFuture(),
            self::STATUS_ACTIVE => $this->ends_at === null || $this->ends_at->isFuture(),
            default             => false,
        };
    }

    public function onTrial(): bool
    {
        return $this->status === self::STATUS_TRIAL
            && $this->trial_ends_at !== null
            && $this->trial_ends_at->isFuture();
    }

    /** The date access is lost if nothing is renewed. */
    public function currentPeriodEnd(): ?\Illuminate\Support\Carbon
    {
        return $this->status === self::STATUS_TRIAL ? $this->trial_ends_at : $this->ends_at;
    }

    public function daysRemaining(): int
    {
        $end = $this->currentPeriodEnd();

        return $end && $end->isFuture() ? (int) now()->diffInDays($end) : 0;
    }
}
