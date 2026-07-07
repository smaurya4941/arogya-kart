<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\BelongsToPharmacy;

class Customer extends Model
{
    use BelongsToPharmacy;

    protected $fillable = [
        'pharmacy_id',
        'name',
        'phone',
        'email',
        'gender',
        'dob',
        'address',
    ];

    protected $casts = [
        'dob' => 'date',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function sales()
    {
        return $this->hasMany(Sale::class);
    }
}
