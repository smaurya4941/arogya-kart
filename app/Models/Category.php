<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;
    use \App\Traits\BelongsToPharmacy;

    protected $fillable = [
        'pharmacy_id',
        'name',
        'slug',
    ];

    public function products()
    {
        return $this->hasMany(Product::class);
    }
}
