<?php

namespace App\Models;

use App\Traits\BelongsToPharmacy;
use Illuminate\Database\Eloquent\Model;

class SaleReturn extends Model
{
    use BelongsToPharmacy;

    protected $fillable = [
        'pharmacy_id',
        'sale_id',
        'user_id',
        'return_number',
        'reason',
        'refund_method',
        'subtotal',
        'tax_amount',
        'total_amount',
    ];

    protected $casts = [
        'subtotal'     => 'decimal:2',
        'tax_amount'   => 'decimal:2',
        'total_amount' => 'decimal:2',
    ];

    public function sale()
    {
        return $this->belongsTo(Sale::class);
    }

    public function items()
    {
        return $this->hasMany(SaleReturnItem::class);
    }

    /** The staff member who processed the return. */
    public function processor()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
