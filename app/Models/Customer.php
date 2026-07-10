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
        'outstanding_balance',
    ];

    protected $casts = [
        'dob' => 'date',
        'outstanding_balance' => 'decimal:2',
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

    public function ledgers()
    {
        return $this->hasMany(CustomerLedger::class);
    }
}
