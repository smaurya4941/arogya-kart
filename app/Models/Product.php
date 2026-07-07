<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasFactory;
    use SoftDeletes;
    use \App\Traits\BelongsToPharmacy;

    protected $fillable = [
        'pharmacy_id',
        'category_id',
        'name',
        'sku',
        'barcode',
        'description',
        'drug_type',
        'image_path',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function batches()
    {
        return $this->hasMany(ProductBatch::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    /*
    |--------------------------------------------------------------------------
    | Computed Attributes
    |--------------------------------------------------------------------------
    */

    // Total available stock (dynamic calculation)
    public function getTotalStockAttribute(): int
    {
        return $this->batches()->sum('quantity');
    }

    // Expiring batches (next 30 days)
    public function expiringBatches()
    {
        return $this->batches()
            ->whereDate('expiry_date', '<=', now()->addDays(30))
            ->where('quantity', '>', 0);
    }

    // Only available batches (quantity > 0)
    public function availableBatches()
    {
        return $this->batches()
            ->where('quantity', '>', 0)
            ->orderBy('expiry_date'); // FEFO ready
    }

    /*
    |--------------------------------------------------------------------------
    | Helper Methods
    |--------------------------------------------------------------------------
    */

    public function isOutOfStock(): bool
    {
        return $this->total_stock <= 0;
    }
}
