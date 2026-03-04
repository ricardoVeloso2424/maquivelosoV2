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
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(route('dashboard', absolute: false));
    }

    public function test_newly_registered_user_can_access_authenticated_pages(): void
    {
        $this->post('/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $this->assertAuthenticated();
        $this->get('/profile')->assertOk();
    }

    public function test_registration_is_rate_limited(): void
    {
        $payload = [
            'name' => 'Rate Limited User',
            'email' => 'invalid-email',
            'password' => 'password',
            'password_confirmation' => 'does-not-match',
        ];

        for ($i = 0; $i < 6; $i++) {
            $this->from('/register')
                ->post('/register', $payload)
                ->assertStatus(302);
        }

        $this->from('/register')
            ->post('/register', $payload)
            ->assertStatus(429);
    }

    public function test_registration_can_be_disabled_via_configuration(): void
    {
        config(['auth.registration_enabled' => false]);

        $this->get('/register')->assertNotFound();

        $this->post('/register', [
            'name' => 'Disabled Register',
            'email' => 'disabled@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ])->assertNotFound();
    }
}
