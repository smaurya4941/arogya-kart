<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Pharmacy extends Model
{
    use SoftDeletes;

    public const STATUS_ACTIVE    = 'active';
    public const STATUS_SUSPENDED = 'suspended';

    protected $fillable = [
        'name',
        'owner_name',
        'license',
        'drug_license_number',
        'gst',
        'pan_number',
        'email',
        'phone',
        'address',
        'city',
        'state',
        'pincode',
        'logo_path',
        'invoice_header',
        'footer_text',
        'status',
    ];

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }

    public function productBatches()
    {
        return $this->hasMany(ProductBatch::class);
    }

    public function categories()
    {
        return $this->hasMany(Category::class);
    }

    public function subscriptions()
    {
        return $this->hasMany(Subscription::class);
    }

    /** The most recent subscription — trial, active, or lapsed. */
    public function currentSubscription()
    {
        return $this->hasOne(Subscription::class)->latestOfMany();
    }

    public function invoices()
    {
        return $this->hasMany(Invoice::class);
    }

    /*
    |--------------------------------------------------------------------------
    | SaaS status helpers
    |--------------------------------------------------------------------------
    */

    public function isActive(): bool
    {
        return $this->status === self::STATUS_ACTIVE;
    }

    /** True when the pharmacy has a trial or paid subscription that hasn't lapsed. */
    public function hasValidSubscription(): bool
    {
        $sub = $this->relationLoaded('currentSubscription')
            ? $this->currentSubscription
            : $this->currentSubscription()->first();

        return $sub !== null && $sub->isValid();
    }

    /** The plan behind the current subscription, or null when unsubscribed. */
    public function currentPlan(): ?Plan
    {
        return $this->currentSubscription?->plan;
    }

    /*
    |--------------------------------------------------------------------------
    | Plan-limit enforcement
    |--------------------------------------------------------------------------
    | Read by controllers/policies before provisioning extra seats or branches
    | so tenants can't exceed what their plan allows.
    */

    public function canAddUser(): bool
    {
        $plan = $this->currentPlan();

        return $plan === null ? false : $this->users()->count() < $plan->max_users;
    }

    public function hasApiAccess(): bool
    {
        return (bool) ($this->currentPlan()?->api_access);
    }
}
