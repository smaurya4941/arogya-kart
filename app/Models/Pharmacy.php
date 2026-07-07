<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pharmacy extends Model
{
    protected $fillable = [
        'name',
        'owner_name',
        'license',
        'drug_license_number',
        'gst',
        'pan_number',
        'email',
        'phone',
        'address',
        'city',
        'state',
        'pincode',
        'logo_path',
        'invoice_header',
        'footer_text',
        'status',
    ];

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }

    public function productBatches()
    {
        return $this->hasMany(ProductBatch::class);
    }

    public function categories()
    {
        return $this->hasMany(Category::class);
    }
}
