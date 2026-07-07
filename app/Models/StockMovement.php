<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\BelongsToPharmacy;

class StockMovement extends Model
{
    use BelongsToPharmacy;

    protected $fillable = [
        'pharmacy_id',
        'product_id',
        'product_batch_id',
        'user_id',
        'type',
        'quantity',
        'reference_id',
        'notes',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function batch()
    {
        return $this->belongsTo(ProductBatch::class, 'product_batch_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
