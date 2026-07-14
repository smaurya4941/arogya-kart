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
        // The admin dashboard sits behind the subscription paywall and renders the
        // permission-gated app layout, so the admin needs a tenant on a live trial
        // plus seeded roles/permissions — the same state public registration sets up.
        $this->seedRolesAndPermissions();
        $pharmacy = $this->createActivePharmacy();

        $admin = User::factory()->create([
            'role' => UserRole::ADMIN,
            'pharmacy_id' => $pharmacy->id,
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
