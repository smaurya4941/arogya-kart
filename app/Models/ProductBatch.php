<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ProductBatch extends Model
{
    use HasFactory;
    use \App\Traits\BelongsToPharmacy;

    protected $fillable = [
        'pharmacy_id',
        'product_id',
        'batch_number',
        'expiry_date',
        'purchase_price',
        'mrp',
        'quantity',
    ];

    protected $casts = [
        'expiry_date' => 'date',
        'purchase_price' => 'decimal:2',
        'mrp' => 'decimal:2',
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

    /*
    |--------------------------------------------------------------------------
    | Business Helpers
    |--------------------------------------------------------------------------
    */

    public function isExpired(): bool
    {
        return $this->expiry_date->isPast();
    }

    public function isExpiringSoon(int $days = 30): bool
    {
        return $this->expiry_date->between(now(), now()->addDays($days));
    }

    public function reduceStock(int $quantity): void
    {
        if ($quantity > $this->quantity) {
            throw new \Exception("Insufficient batch stock.");
        }

        $this->decrement('quantity', $quantity);
    }

    public function profitPerUnit(): float
    {
        return $this->mrp - $this->purchase_price;
    }
}