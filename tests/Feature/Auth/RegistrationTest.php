<?php

namespace Tests\Feature\Auth;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_registration_screen_can_be_rendered(): void
    {
        $response = $this->get('/register');

        $response->assertStatus(200);
    }

    public function test_new_users_can_register(): void
    {
        $response = $this->post('/register', [
            'name' => 'Test User',
            'pharmacy_name' => 'Test Pharmacy',
            'phone' => '9876543210',
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect('/admin/dashboard');
    }

    public function test_registration_provisions_a_pharmacy_and_owner_admin(): void
    {
        $this->post('/register', [
            'name' => 'Priya Sharma',
            'pharmacy_name' => 'Sharma Medical Store',
            'email' => 'priya@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $user = \App\Models\User::where('email', 'priya@example.com')->firstOrFail();

        // The blocker: a registered user must own a real pharmacy and be its admin.
        $this->assertNotNull($user->pharmacy_id);
        $this->assertSame(\App\Enums\UserRole::ADMIN, $user->role);
        $this->assertDatabaseHas('pharmacies', [
            'id' => $user->pharmacy_id,
            'name' => 'Sharma Medical Store',
            'owner_name' => 'Priya Sharma',
        ]);
    }

    public function test_registration_requires_a_pharmacy_name(): void
    {
        $response = $this->post('/register', [
            'name' => 'No Pharmacy',
            'email' => 'nopharmacy@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $response->assertSessionHasErrors('pharmacy_name');
        $this->assertGuest();
        $this->assertDatabaseMissing('users', ['email' => 'nopharmacy@example.com']);
    }
}
