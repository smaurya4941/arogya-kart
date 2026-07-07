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
}
