<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\BelongsToPharmacy;

class PurchaseReturn extends Model
{
    use BelongsToPharmacy;

    protected $fillable = [
        'pharmacy_id',
        'purchase_invoice_id',
        'supplier_id',
        'return_number',
        'return_date',
        'reason',
        'total_amount',
        'status',
    ];

    protected $casts = [
        'return_date' => 'date',
        'total_amount' => 'decimal:2',
    ];

    public function invoice()
    {
        return $this->belongsTo(PurchaseInvoice::class, 'purchase_invoice_id');
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function items()
    {
        return $this->hasMany(PurchaseReturnItem::class);
    }
}
