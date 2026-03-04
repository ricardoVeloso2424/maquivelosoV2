<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_non_admin_cannot_access_admin_dashboard(): void
    {
        $user = User::factory()->create(['is_admin' => false]);

        $this->actingAs($user)
            ->get('/admin')
            ->assertForbidden();
    }

    public function test_admin_can_access_admin_dashboard(): void
    {
        $user = User::factory()->create(['is_admin' => true]);

        $this->actingAs($user)
            ->get('/admin')
            ->assertOk();
    }

    public function test_unverified_admin_is_redirected_to_verification_notice(): void
    {
        $user = User::factory()->unverified()->create(['is_admin' => true]);

        $this->actingAs($user)
            ->get('/admin')
            ->assertRedirect(route('verification.notice', absolute: false));
    }
}
