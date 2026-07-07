<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Pharmacy;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use App\Enums\UserRole;

class AdminSeeder extends Seeder
{
    /**
     * Seed a demo pharmacy and its owner-admin. Every tenant user MUST have a
     * pharmacy_id (the sales/purchase tables require it), so the admin is created
     * with one — mirroring what real registration now does.
     */
    public function run(): void
    {
        $pharmacy = Pharmacy::firstOrCreate(
            ['email' => 'admin@arogyakart.com'],
            [
                'name' => 'ArogyaKart Demo Pharmacy',
                'owner_name' => 'Admin',
                'phone' => '0000000000',
                'status' => 'active',
            ]
        );

        User::updateOrCreate(
            ['email' => 'admin@arogyakart.com'],
            [
                'name' => 'Admin',
                // Override with a strong password via ADMIN_PASSWORD in real setups.
                'password' => Hash::make(env('ADMIN_PASSWORD', 'Admin@12345')),
                'role' => UserRole::ADMIN,
                'pharmacy_id' => $pharmacy->id,
                'status' => 'active',
            ]
        );
    }
}
