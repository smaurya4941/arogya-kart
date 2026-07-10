<?php

namespace App\Models;

use App\Traits\BelongsToPharmacy;
use Illuminate\Database\Eloquent\Model;

class Unit extends Model
{
    use BelongsToPharmacy;

    protected $fillable = [
        'pharmacy_id',
        'name',
        'short_name',
    ];
}
