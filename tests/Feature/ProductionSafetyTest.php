<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\AdminUserSeeder;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductionSafetyTest extends TestCase
{
    use RefreshDatabase;

    public function test_database_seeder_creates_no_users_in_production(): void
    {
        $this->app->detectEnvironment(fn () => 'production');

        // Call run() directly: going through `db:seed` would trip the framework's
        // production confirmation prompt before reaching our guard.
        app(DatabaseSeeder::class)->run();

        $this->assertSame(0, User::query()->count());
    }

    public function test_admin_seeder_is_a_no_op_in_production(): void
    {
        $this->app->detectEnvironment(fn () => 'production');

        app(AdminUserSeeder::class)->run();

        $this->assertSame(0, User::query()->count());
    }

    public function test_database_seeder_creates_dev_data_in_local_and_testing(): void
    {
        // The default test environment is "testing", which is an allowed
        // demo-data environment.
        $this->seed(DatabaseSeeder::class);

        $this->assertSame(2, User::query()->count());
        $this->assertSame(1, User::query()->where('is_admin', true)->count());
        $this->assertDatabaseHas('users', ['email' => 'admin@maquiveloso.com']);
    }

    public function test_public_registration_is_disabled_by_default(): void
    {
        // The phpunit suite sets REGISTRATION_ENABLED=true so the auth feature
        // tests can exercise the enabled flow. Re-evaluate the shipped config
        // with the variable unset to prove the default-of-record is "off".
        $previous = getenv('REGISTRATION_ENABLED');
        putenv('REGISTRATION_ENABLED');
        unset($_ENV['REGISTRATION_ENABLED'], $_SERVER['REGISTRATION_ENABLED']);

        try {
            $config = require base_path('config/auth.php');
            $this->assertFalse($config['registration_enabled']);
        } finally {
            if ($previous !== false) {
                putenv("REGISTRATION_ENABLED={$previous}");
                $_ENV['REGISTRATION_ENABLED'] = $previous;
                $_SERVER['REGISTRATION_ENABLED'] = $previous;
            }
        }
    }
}
