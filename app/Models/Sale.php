<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\BelongsToPharmacy;

class Sale extends Model
{
    use BelongsToPharmacy;

    protected $fillable = [
        'pharmacy_id',
        'customer_id',
        'user_id',
        'invoice_number',
        'sale_date',
        'payment_method',
        'subtotal',
        'tax_amount',
        'discount_amount',
        'total_amount',
        'paid_amount',
        'due_amount',
        'payment_status',
        'doctor_name',
        'doctor_registration_number',
        'notes',
    ];

    protected $casts = [
        'sale_date' => 'datetime',
        'subtotal' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'paid_amount' => 'decimal:2',
        'due_amount' => 'decimal:2',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    // The cashier who rang up the sale.
    public function cashier()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function items()
    {
        return $this->hasMany(SaleItem::class);
    }

    public function returns()
    {
        return $this->hasMany(SaleReturn::class);
    }

    /** True when at least one line still has un-returned quantity. */
    public function hasReturnableItems(): bool
    {
        return $this->items->contains(fn (SaleItem $item) => $item->returnableQuantity() > 0);
    }

    /** Total value refunded against this sale so far. */
    public function totalRefunded(): float
    {
        return (float) $this->returns->sum('total_amount');
    }

    /*
    |--------------------------------------------------------------------------
    | Presentation Helpers
    |--------------------------------------------------------------------------
    */

    public function paymentStatusBadge(): string
    {
        return match ($this->payment_status) {
            'paid' => 'bg-emerald-100 text-emerald-700',
            'partial' => 'bg-amber-100 text-amber-700',
            default => 'bg-rose-100 text-rose-700',
        };
    }
}
