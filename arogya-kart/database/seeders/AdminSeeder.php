<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use App\Enums\UserRole;
class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //creating a default admin user
        User::create([
            'name' => 'Admin',
            'email' => 'admin@arogyakart.com',
            'password' => Hash::make('1'),
            'role' => UserRole::ADMIN,
        ]);
    }
}
