<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\AdminUserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AdminSinglePolicyTest extends TestCase
{
    use RefreshDatabase;

    public function test_seeder_creates_single_default_admin_account(): void
    {
        $rogue = User::factory()->create([
            'email' => 'rogue@example.com',
        ]);

        DB::table('users')
            ->where('id', $rogue->id)
            ->update(['is_admin' => true]);

        $this->seed(AdminUserSeeder::class);

        $admin = User::query()->where('email', 'admin@maquiveloso.com')->first();

        $this->assertNotNull($admin);
        $this->assertTrue((bool) $admin->is_admin);
        $this->assertTrue(Hash::check('password', (string) $admin->password));
        $this->assertSame(1, User::query()->where('is_admin', true)->count());
        $this->assertFalse((bool) $rogue->fresh()->is_admin);
    }

    public function test_non_admin_user_cannot_be_promoted_to_admin_outside_seeder_context(): void
    {
        $this->seed(AdminUserSeeder::class);

        $user = User::factory()->create([
            'email' => 'user@example.com',
        ]);

        $user->forceFill(['is_admin' => true])->save();

        $this->assertFalse((bool) $user->fresh()->is_admin);
        $this->assertSame(1, User::query()->where('is_admin', true)->count());
    }
}
