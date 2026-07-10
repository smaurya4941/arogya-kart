<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

// NOTE: sale_items has no pharmacy_id column, so it is scoped through its Sale
// rather than the BelongsToPharmacy global scope.
class SaleItem extends Model
{
    protected $fillable = [
        'sale_id',
        'product_id',
        'product_batch_id',
        'quantity',
        'unit_price',
        'mrp',
        'discount_percentage',
        'tax_percentage',
        'total',
    ];

    protected $casts = [
        'unit_price' => 'decimal:2',
        'mrp' => 'decimal:2',
        'discount_percentage' => 'decimal:2',
        'tax_percentage' => 'decimal:2',
        'total' => 'decimal:2',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function sale()
    {
        return $this->belongsTo(Sale::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function batch()
    {
        return $this->belongsTo(ProductBatch::class, 'product_batch_id');
    }

    public function returnItems()
    {
        return $this->hasMany(SaleReturnItem::class);
    }

    /** How many units of this line have already been returned. */
    public function returnedQuantity(): int
    {
        return (int) ($this->relationLoaded('returnItems')
            ? $this->returnItems->sum('quantity')
            : $this->returnItems()->sum('quantity'));
    }

    /** How many units of this line can still be returned. */
    public function returnableQuantity(): int
    {
        return max(0, (int) $this->quantity - $this->returnedQuantity());
    }

    /** Refund value of a single unit of this line (base + tax), for proration. */
    public function unitRefundValue(): float
    {
        return $this->quantity > 0 ? round((float) $this->total / (int) $this->quantity, 2) : 0.0;
    }
}
