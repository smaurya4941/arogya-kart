<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RoleMiddlewareTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_access_admin_dashboard(): void
    {
        $admin = User::factory()->create([
            'role' => UserRole::ADMIN,
        ]);

        $response = $this->actingAs($admin)->get('/admin/dashboard');

        $response->assertStatus(200);
    }

    public function test_staff_cannot_access_admin_dashboard(): void
    {
        $staff = User::factory()->create([
            'role' => UserRole::STAFF,
        ]);

        $response = $this->actingAs($staff)->get('/admin/dashboard');

        $response->assertStatus(403);
    }
}
