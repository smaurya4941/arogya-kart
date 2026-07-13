<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Raw key/value storage for platform-wide configuration. Reads/writes go through
 * the App\Services\PlatformSettings service (which caches and encrypts secrets) —
 * prefer that over touching this model directly.
 */
class PlatformSetting extends Model
{
    protected $primaryKey = 'key';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = ['key', 'value'];
}
