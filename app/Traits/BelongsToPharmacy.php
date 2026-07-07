<?php

namespace App\Traits;

use App\Models\Pharmacy;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

trait BelongsToPharmacy
{
    /**
     * Boot the trait to automatically scope queries by pharmacy.
     */
    protected static function bootBelongsToPharmacy(): void
    {
        static::addGlobalScope('pharmacy', function (Builder $builder) {
            // If the user is authenticated and is NOT a super admin, scope the query to their pharmacy.
            if (auth()->check()) {
                $user = auth()->user();
                if (! $user->hasRole('Super Admin') && $user->pharmacy_id) {
                    $builder->where('pharmacy_id', $user->pharmacy_id);
                }
            }
        });

        static::creating(function ($model) {
            // Automatically assign the user's pharmacy_id when creating models
            if (auth()->check()) {
                $user = auth()->user();
                if (! $user->hasRole('Super Admin') && $user->pharmacy_id && ! $model->pharmacy_id) {
                    $model->pharmacy_id = $user->pharmacy_id;
                }
            }
        });
    }

    /**
     * Relationship to the pharmacy.
     */
    public function pharmacy(): BelongsTo
    {
        return $this->belongsTo(Pharmacy::class);
    }
}
