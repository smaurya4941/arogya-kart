<?php

namespace App\Models;

use App\Traits\BelongsToPharmacy;
use Illuminate\Database\Eloquent\Model;

/**
 * A billing invoice for a SaaS subscription payment (not to be confused with the
 * pharmacy's own sales invoices). Tenant-scoped so a pharmacy owner only ever
 * sees their own billing history.
 */
class Invoice extends Model
{
    use BelongsToPharmacy;

    public const STATUS_PENDING        = 'pending';
    public const STATUS_PAID           = 'paid';
    public const STATUS_FAILED         = 'failed';
    public const STATUS_REFUND_PENDING = 'refund_pending';
    public const STATUS_REFUNDED       = 'refunded';
    public const STATUS_VOID           = 'void';

    /** Statuses that count toward recognised revenue. */
    public const REVENUE_STATUSES = [self::STATUS_PAID];

    protected $fillable = [
        'pharmacy_id',
        'subscription_id',
        'invoice_number',
        'amount',
        'tax',
        'total',
        'status',
        'payment_method',
        'transaction_id',
        'refund_id',
        'paid_at',
        'refunded_at',
    ];

    protected $casts = [
        'amount'      => 'decimal:2',
        'tax'         => 'decimal:2',
        'total'       => 'decimal:2',
        'paid_at'     => 'datetime',
        'refunded_at' => 'datetime',
    ];

    /** A refund has been requested at the gateway but hasn't settled yet. */
    public function isRefundPending(): bool
    {
        return $this->status === self::STATUS_REFUND_PENDING;
    }

    public function subscription()
    {
        return $this->belongsTo(Subscription::class);
    }

    /** Generates the next human-friendly invoice number, e.g. INV-2026-000042. */
    public static function nextNumber(): string
    {
        // withoutGlobalScopes: numbering must be unique platform-wide, so we count
        // across all tenants rather than just the current one.
        $seq = static::withoutGlobalScopes()->count() + 1;

        return sprintf('INV-%s-%06d', now()->format('Y'), $seq);
    }
}
