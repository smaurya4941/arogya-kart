<?php

namespace Tests;

use App\Models\Pharmacy;
use App\Models\Plan;
use App\Models\Subscription;
use Database\Seeders\PlanSeeder;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    /**
     * Seed the platform's roles & permissions.
     *
     * Authenticated views (e.g. layouts/app.blade.php) call @can('create sale');
     * Spatie throws PermissionDoesNotExist if the permission was never created.
     * Any test that renders an authenticated page must seed these first — exactly
     * as a real deployment does by running the RolePermissionSeeder.
     */
    protected function seedRolesAndPermissions(): void
    {
        $this->seed(RolePermissionSeeder::class);
    }

    /**
     * Provision an active tenant the way public registration does: an active
     * pharmacy carrying a live trial subscription, so the subscription paywall
     * (EnsureSubscriptionActive) lets its users through.
     */
    protected function createActivePharmacy(string $name = 'Test Pharmacy'): Pharmacy
    {
        $this->seed(PlanSeeder::class);

        $pharmacy = Pharmacy::create([
            'name' => $name,
            'owner_name' => 'Owner',
            'status' => Pharmacy::STATUS_ACTIVE,
        ]);

        Subscription::create([
            'pharmacy_id'   => $pharmacy->id,
            'plan_id'       => Plan::active()->orderBy('price_monthly')->value('id'),
            'status'        => Subscription::STATUS_TRIAL,
            'billing_cycle' => 'monthly',
            'starts_at'     => now(),
            'trial_ends_at' => now()->addDays((int) config('saas.trial_days', 14)),
            'ends_at'       => now()->addDays((int) config('saas.trial_days', 14)),
        ]);

        return $pharmacy;
    }
}
