<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    private const ADMIN_EMAIL = 'ddfbae@gmail.com';

    /**
     * Seed a default admin user for local development.
     */
    public function run(): void
    {
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
