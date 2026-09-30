<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    private const ADMIN_EMAIL = 'admin@maquiveloso.com';

    /**
     * Seed a default admin user for local development.
     *
     * This account uses a well-known development password and must never run in
     * production. Production administrators are created with `php artisan admin:create`.
     */
    public function run(): void
    {
        if (app()->environment('production')) {
            $this->command?->warn('AdminUserSeeder skipped in production. Use `php artisan admin:create`.');

            return;
        }

        User::allowAdminPromotion(function (): void {
            $existingPassword = User::query()
                ->where('email', self::ADMIN_EMAIL)
                ->value('password');

            // Keep one fixed admin account and do not overwrite the password if it already exists.
            $admin = User::query()->updateOrCreate(
                ['email' => self::ADMIN_EMAIL],
                [
                    'name' => 'Admin',
                    'password' => $existingPassword ?? Hash::make('password'),
                ]
            );

            // Ensure the managed admin account is always admin.
            if (! $admin->is_admin) {
                $admin->forceFill(['is_admin' => true])->save();
            }
        });

        // Enforce a single-admin policy by demoting any other account.
        User::query()
            ->where('email', '!=', self::ADMIN_EMAIL)
            ->where('is_admin', true)
            ->update(['is_admin' => false]);
    }
}
