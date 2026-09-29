<?php

namespace Tests\Feature;

use App\Models\Machine;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class AdminMachinePriceTest extends TestCase
{
    use RefreshDatabase;

    public static function localizedPrices(): array
    {
        return [
            'plain integer' => ['1200', 1200.00],
            'dot decimal' => ['1200.50', 1200.50],
            'comma decimal' => ['1200,50', 1200.50],
            'european full' => ['1.200,50', 1200.50],
            'spaced thousands' => ['1 200,50', 1200.50],
        ];
    }

    #[DataProvider('localizedPrices')]
    public function test_store_accepts_localized_price_formats(string $input, float $expected): void
    {
        $response = $this->actingAs($this->adminUser())
            ->post(route('admin.machines.store'), [
                'name' => 'Price '.$input,
                'status' => 'available',
                'price' => $input,
            ]);

        $response->assertSessionHasNoErrors();
        $response->assertRedirect(route('admin.machines.index'));

        $machine = Machine::query()->where('name', 'Price '.$input)->firstOrFail();
        $this->assertEqualsWithDelta($expected, (float) $machine->price, 0.001);
    }

    public function test_store_rejects_invalid_price(): void
    {
        $response = $this->from(route('admin.machines.create'))
            ->actingAs($this->adminUser())
            ->post(route('admin.machines.store'), [
                'name' => 'Invalid price machine',
                'status' => 'available',
                'price' => 'not-a-number',
            ]);

        $response->assertSessionHasErrors('price');
        $this->assertDatabaseMissing('machines', ['name' => 'Invalid price machine']);
    }

    public function test_update_accepts_localized_price(): void
    {
        $machine = Machine::create(['name' => 'Editable', 'status' => 'available', 'price' => 10]);

        $this->actingAs($this->adminUser())
            ->put(route('admin.machines.update', $machine), [
                'name' => 'Editable',
                'status' => 'available',
                'price' => '2.500,99',
            ])
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('admin.machines.index'));

        $this->assertEqualsWithDelta(2500.99, (float) $machine->fresh()->price, 0.001);
    }

    public function test_empty_price_is_allowed_and_stored_as_null(): void
    {
        $this->actingAs($this->adminUser())
            ->post(route('admin.machines.store'), [
                'name' => 'No price machine',
                'status' => 'available',
                'price' => '',
            ])
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('admin.machines.index'));

        $machine = Machine::query()->where('name', 'No price machine')->firstOrFail();
        $this->assertNull($machine->price);
    }

    private function adminUser(): User
    {
        return User::allowAdminPromotion(fn () => User::factory()->create(['is_admin' => true]));
    }
}
