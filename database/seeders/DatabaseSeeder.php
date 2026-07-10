<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     *
     * Order matters: Spatie roles/permissions and the plan catalogue must exist
     * before any user or subscription references them.
     */
    public function run(): void
    {
        $this->call([
            RolePermissionSeeder::class,  // roles + permissions
            PlanSeeder::class,            // global subscription catalogue
            SuperAdminSeeder::class,      // platform owner (no tenant)
            AdminSeeder::class,           // demo pharmacy + owner
        ]);
    }
}
