<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\BelongsToPharmacy;

class Supplier extends Model
{
    use BelongsToPharmacy;

    protected $fillable = [
        'pharmacy_id',
        'name',
        'company_name',
        'contact_person',
        'phone',
        'email',
        'gst_number',
        'address',
        'city',
        'state',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function purchaseInvoices()
    {
        return $this->hasMany(PurchaseInvoice::class);
    }

    public function ledgers()
    {
        return $this->hasMany(SupplierLedger::class);
    }

    /*
    |--------------------------------------------------------------------------
    | Helpers
    |--------------------------------------------------------------------------
    */

    // Outstanding payable = running balance of the latest ledger entry.
    public function getBalanceAttribute(): float
    {
        return (float) ($this->ledgers()->latest('id')->value('balance') ?? 0);
    }
}
