<?php

namespace App\Traits;

use App\Enums\UserRole;
use App\Models\Pharmacy;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Row-level multi-tenancy. Any model using this trait is automatically:
 *   1. Filtered to the current user's pharmacy on every read.
 *   2. Stamped with that pharmacy_id on every create.
 *
 * The single exception is the platform owner (UserRole::SUPER_ADMIN), who
 * operates across all tenants and is therefore never scoped. Standardising the
 * bypass on the UserRole enum (rather than a Spatie role name) keeps it in lock-
 * step with RoleMiddleware and the dashboard router, which are enum-driven too.
 */
trait BelongsToPharmacy
{
    protected static function bootBelongsToPharmacy(): void
    {
        static::addGlobalScope('pharmacy', function (Builder $builder) {
            $user = auth()->user();

            if ($user && ! static::isCrossTenantUser($user) && $user->pharmacy_id) {
                $builder->where($builder->getModel()->getTable() . '.pharmacy_id', $user->pharmacy_id);
            }
        });

        static::creating(function ($model) {
            $user = auth()->user();

            if ($user && ! static::isCrossTenantUser($user) && $user->pharmacy_id && ! $model->pharmacy_id) {
                $model->pharmacy_id = $user->pharmacy_id;
            }
        });
    }

    /**
     * True when the given user may see/write data across all pharmacies.
     * Tolerant of the role arriving as the enum (cast) or a raw string.
     */
    protected static function isCrossTenantUser(User $user): bool
    {
        $role = $user->role instanceof UserRole
            ? $user->role
            : (is_string($user->role) ? UserRole::tryFrom($user->role) : null);

        return $role === UserRole::SUPER_ADMIN;
    }

    public function pharmacy(): BelongsTo
    {
        return $this->belongsTo(Pharmacy::class);
    }
}
