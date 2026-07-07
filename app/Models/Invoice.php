<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\BelongsToPharmacy;

class Invoice extends Model
{
    use BelongsToPharmacy;
    //
}
