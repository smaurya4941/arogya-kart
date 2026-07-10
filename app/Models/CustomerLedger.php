<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\BelongsToPharmacy;

class CustomerLedger extends Model
{
    use BelongsToPharmacy;

    protected $fillable = [
        'pharmacy_id',
        'customer_id',
        'type', // sale, payment, refund, opening_balance
        'amount',
        'reference',
        'date',
        'description',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'date' => 'date',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }
}
