<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Subscription plans are a GLOBAL catalogue owned by the platform, not by any
 * single tenant. It therefore deliberately does NOT use BelongsToPharmacy — the
 * plans table has no pharmacy_id column, so scoping it would raise a SQL error
 * for every pharmacy admin who opened the billing page.
 */
class Plan extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'price_monthly',
        'price_yearly',
        'max_users',
        'max_branches',
        'api_access',
        'features',
        'is_active',
    ];

    protected $casts = [
        'price_monthly' => 'decimal:2',
        'price_yearly'  => 'decimal:2',
        'max_users'     => 'integer',
        'max_branches'  => 'integer',
        'api_access'    => 'boolean',
        'is_active'     => 'boolean',
        'features'      => 'array',
    ];

    public function subscriptions()
    {
        return $this->hasMany(Subscription::class);
    }

    /** Price for a given billing cycle, in the platform's base currency (INR). */
    public function priceFor(string $billingCycle): float
    {
        return (float) ($billingCycle === 'yearly' ? $this->price_yearly : $this->price_monthly);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
