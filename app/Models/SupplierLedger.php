<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\BelongsToPharmacy;

class SupplierLedger extends Model
{
    use BelongsToPharmacy;

    protected $fillable = [
        'pharmacy_id',
        'supplier_id',
        'type',
        'amount',
        'balance',
        'reference_id',
        'notes',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'balance' => 'decimal:2',
    ];

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }
}
