<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

/**
 * Provisions the platform owner — the one account that manages the SaaS itself.
 * Deliberately has NO pharmacy_id: a Super Admin is not a tenant and operates
 * across all pharmacies (see BelongsToPharmacy).
 */
class SuperAdminSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => env('SUPER_ADMIN_EMAIL', 'superadmin@pharmaflow.com')],
            [
                'name'              => 'Platform Owner',
                'password'          => Hash::make(env('SUPER_ADMIN_PASSWORD', 'Super@12345')),
                'role'              => UserRole::SUPER_ADMIN,
                'pharmacy_id'       => null,
                'status'            => 'active',
                'email_verified_at' => now(),
            ]
        );
    }
}
