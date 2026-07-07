<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\BelongsToPharmacy;

class PurchaseInvoice extends Model
{
    use BelongsToPharmacy;

    protected $fillable = [
        'pharmacy_id',
        'supplier_id',
        'purchase_order_id',
        'invoice_number',
        'supplier_invoice_number',
        'purchase_date',
        'payment_terms',
        'notes',
        'total_amount',
    ];

    protected $casts = [
        'purchase_date' => 'date',
        'total_amount' => 'decimal:2',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function items()
    {
        return $this->hasMany(PurchaseInvoiceItem::class);
    }
}
