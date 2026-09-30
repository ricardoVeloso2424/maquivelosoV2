<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AdminCreateCommandTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_creates_a_new_administrator(): void
    {
        $this->artisan('admin:create')
            ->expectsQuestion('Nome', 'New Admin')
            ->expectsQuestion('Email', 'New.Admin@Example.com')
            ->expectsQuestion('Password', 'StrongPass123!')
            ->expectsQuestion('Confirmar password', 'StrongPass123!')
            ->expectsOutputToContain('criado com sucesso')
            ->assertExitCode(0);

        $admin = User::query()->where('email', 'new.admin@example.com')->first();

        $this->assertNotNull($admin);
        $this->assertTrue((bool) $admin->is_admin);
        $this->assertTrue(Hash::check('StrongPass123!', (string) $admin->password));
    }

    public function test_it_promotes_an_existing_user_without_changing_their_password(): void
    {
        $user = User::factory()->create([
            'email' => 'existing@example.com',
            'password' => Hash::make('original-password'),
            'is_admin' => false,
        ]);

        $this->artisan('admin:create')
            ->expectsQuestion('Nome', 'Ignored Name')
            ->expectsQuestion('Email', 'existing@example.com')
            ->expectsQuestion('Password', 'StrongPass123!')
            ->expectsQuestion('Confirmar password', 'StrongPass123!')
            ->expectsOutputToContain('promovido a administrador')
            ->assertExitCode(0);

        $user->refresh();

        $this->assertTrue((bool) $user->is_admin);
        $this->assertTrue(Hash::check('original-password', (string) $user->password));
        $this->assertSame(1, User::query()->where('email', 'existing@example.com')->count());
    }

    public function test_it_reports_when_user_is_already_an_admin(): void
    {
        User::allowAdminPromotion(fn () => User::factory()->create([
            'email' => 'boss@example.com',
            'is_admin' => true,
        ]));

        $this->artisan('admin:create')
            ->expectsQuestion('Nome', 'Boss')
            ->expectsQuestion('Email', 'boss@example.com')
            ->expectsQuestion('Password', 'StrongPass123!')
            ->expectsQuestion('Confirmar password', 'StrongPass123!')
            ->expectsOutputToContain('já é administrador')
            ->assertExitCode(0);

        $this->assertSame(1, User::query()->where('is_admin', true)->count());
    }

    public function test_it_fails_when_password_confirmation_does_not_match(): void
    {
        $this->artisan('admin:create')
            ->expectsQuestion('Nome', 'Mismatch')
            ->expectsQuestion('Email', 'mismatch@example.com')
            ->expectsQuestion('Password', 'StrongPass123!')
            ->expectsQuestion('Confirmar password', 'different-password')
            ->assertExitCode(1);

        $this->assertDatabaseMissing('users', ['email' => 'mismatch@example.com']);
    }

    public function test_it_fails_with_invalid_email(): void
    {
        $this->artisan('admin:create')
            ->expectsQuestion('Nome', 'Bad Email')
            ->expectsQuestion('Email', 'not-an-email')
            ->expectsQuestion('Password', 'StrongPass123!')
            ->expectsQuestion('Confirmar password', 'StrongPass123!')
            ->assertExitCode(1);

        $this->assertSame(0, User::query()->count());
    }
}
