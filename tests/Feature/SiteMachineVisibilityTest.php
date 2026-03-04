<?php

namespace Tests\Feature;

use App\Models\Machine;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SiteMachineVisibilityTest extends TestCase
{
    use RefreshDatabase;

    public function test_available_machine_can_be_viewed_publicly(): void
    {
        $machine = Machine::create([
            'name' => 'Available machine',
            'status' => 'available',
        ]);

        $this->get(route('site.machine.show', $machine))
            ->assertOk();
    }

    public function test_non_available_machine_returns_404_publicly(): void
    {
        $machine = Machine::create([
            'name' => 'Sold machine',
            'status' => 'sold',
        ]);

        $this->get(route('site.machine.show', $machine))
            ->assertNotFound();
    }

    public function test_catalog_can_filter_available_machines_by_max_price(): void
    {
        Machine::create(['name' => 'Machine Cheap', 'status' => 'available', 'price' => 100]);
        Machine::create(['name' => 'Machine Expensive', 'status' => 'available', 'price' => 900]);
        Machine::create(['name' => 'Machine Sold', 'status' => 'sold', 'price' => 50]);

        $this->get('/catalogo?price=200')
            ->assertOk()
            ->assertSee('Machine Cheap')
            ->assertDontSee('Machine Expensive')
            ->assertDontSee('Machine Sold');
    }

    public function test_invalid_catalog_price_filter_is_ignored(): void
    {
        Machine::create(['name' => 'Machine Alpha', 'status' => 'available', 'price' => 100]);
        Machine::create(['name' => 'Machine Beta', 'status' => 'available', 'price' => 900]);
        Machine::create(['name' => 'Machine Gamma Sold', 'status' => 'sold', 'price' => 50]);

        $this->get('/catalogo?price=invalid')
            ->assertOk()
            ->assertSee('Machine Alpha')
            ->assertSee('Machine Beta')
            ->assertDontSee('Machine Gamma Sold');
    }

    public function test_catalog_price_filter_accepts_localized_numeric_formats(): void
    {
        Machine::create(['name' => 'Machine Localized Cheap', 'status' => 'available', 'price' => 1000]);
        Machine::create(['name' => 'Machine Localized Expensive', 'status' => 'available', 'price' => 1500]);

        $filters = ['1.200,00', '1200,00', '1 200,00', '1200.00'];

        foreach ($filters as $filter) {
            $this->get('/catalogo?price=' . urlencode($filter))
                ->assertOk()
                ->assertSee('Machine Localized Cheap')
                ->assertDontSee('Machine Localized Expensive');
        }
    }
}
