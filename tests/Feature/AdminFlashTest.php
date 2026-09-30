<?php

namespace Tests\Feature;

use App\Models\Machine;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AdminFlashTest extends TestCase
{
    use RefreshDatabase;

    public function test_creating_machine_shows_success_flash(): void
    {
        $this->actingAs($this->adminUser())
            ->followingRedirects()
            ->post(route('admin.machines.store'), [
                'name' => 'Nova Máquina Teste',
                'status' => 'available',
            ])
            ->assertOk()
            ->assertSee('Máquina criada com sucesso.');
    }

    public function test_updating_machine_shows_success_flash(): void
    {
        $machine = Machine::create(['name' => 'Original', 'status' => 'available']);

        $this->actingAs($this->adminUser())
            ->followingRedirects()
            ->put(route('admin.machines.update', $machine), [
                'name' => 'Atualizada',
                'status' => 'available',
            ])
            ->assertOk()
            ->assertSee('Máquina atualizada com sucesso.');
    }

    public function test_deleting_machine_shows_success_flash(): void
    {
        Storage::fake('public');

        $machine = Machine::create(['name' => 'Para apagar', 'status' => 'available']);

        $this->actingAs($this->adminUser())
            ->followingRedirects()
            ->delete(route('admin.machines.destroy', $machine))
            ->assertOk()
            ->assertSee('Máquina removida com sucesso.');
    }

    public function test_updating_settings_shows_success_flash(): void
    {
        $this->actingAs($this->adminUser())
            ->from(route('admin.settings'))
            ->followingRedirects()
            ->post(route('admin.settings.update'), [
                'business_name' => 'MaquiVeloso',
                'contact_phone' => '912345678',
            ])
            ->assertOk()
            ->assertSee('Definições guardadas com sucesso.');
    }

    private function adminUser(): User
    {
        return User::allowAdminPromotion(fn () => User::factory()->create(['is_admin' => true]));
    }
}
