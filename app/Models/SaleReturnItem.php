<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

// Scoped through its parent SaleReturn (no pharmacy_id column), mirroring SaleItem.
class SaleReturnItem extends Model
{
    protected $fillable = [
        'sale_return_id',
        'sale_item_id',
        'product_id',
        'product_batch_id',
        'quantity',
        'unit_price',
        'tax_percentage',
        'total',
    ];

    protected $casts = [
        'quantity'        => 'integer',
        'unit_price'      => 'decimal:2',
        'tax_percentage'  => 'decimal:2',
        'total'           => 'decimal:2',
    ];

    public function saleReturn()
    {
        return $this->belongsTo(SaleReturn::class);
    }

    public function saleItem()
    {
        return $this->belongsTo(SaleItem::class);
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
