<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Plan;
use Illuminate\Support\Str;

class PlanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $plans = [
            [
                'name' => 'Starter',
                'description' => 'Perfect for small pharmacies just getting started.',
                'price_monthly' => 999.00,
                'price_yearly' => 9990.00,
                'max_users' => 2,
                'max_branches' => 1,
                'api_access' => false,
                'features' => json_encode(['Inventory Management', 'Billing & Sales', 'Basic Reports']),
            ],
            [
                'name' => 'Professional',
                'description' => 'Ideal for growing pharmacies with medium traffic.',
                'price_monthly' => 2499.00,
                'price_yearly' => 24990.00,
                'max_users' => 5,
                'max_branches' => 2,
                'api_access' => false,
                'features' => json_encode(['Everything in Starter', 'Advanced Reports', 'Low Stock Alerts', 'Expiry Alerts']),
            ],
            [
                'name' => 'Enterprise',
                'description' => 'For large pharmacy chains requiring full control.',
                'price_monthly' => 4999.00,
                'price_yearly' => 49990.00,
                'max_users' => 20,
                'max_branches' => 10,
                'api_access' => true,
                'features' => json_encode(['Everything in Professional', 'API Access', 'Custom Roles', 'Dedicated Support']),
            ]
        ];

        foreach ($plans as $plan) {
            Plan::firstOrCreate([
                'slug' => Str::slug($plan['name'])
            ], $plan);
        }
    }
}
