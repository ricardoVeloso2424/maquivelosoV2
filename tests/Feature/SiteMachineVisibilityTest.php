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

    public function test_catalog_default_order_is_alphabetical_by_name(): void
    {
        Machine::create(['name' => 'Machine Zulu', 'status' => 'available', 'price' => 300]);
        Machine::create(['name' => 'Machine Alpha', 'status' => 'available', 'price' => 100]);
        Machine::create(['name' => 'Machine Bravo', 'status' => 'available', 'price' => 200]);

        $this->get('/catalogo')
            ->assertOk()
            ->assertSeeInOrder(['Machine Alpha', 'Machine Bravo', 'Machine Zulu']);
    }

    public function test_catalog_can_sort_by_price_ascending(): void
    {
        Machine::create(['name' => 'Machine 500', 'status' => 'available', 'price' => 500]);
        Machine::create(['name' => 'Machine 100', 'status' => 'available', 'price' => 100]);
        Machine::create(['name' => 'Machine 900', 'status' => 'available', 'price' => 900]);

        $this->get('/catalogo?sort=price&dir=asc')
            ->assertOk()
            ->assertSeeInOrder(['Machine 100', 'Machine 500', 'Machine 900']);
    }

    public function test_catalog_can_sort_by_price_descending(): void
    {
        Machine::create(['name' => 'Machine 500', 'status' => 'available', 'price' => 500]);
        Machine::create(['name' => 'Machine 100', 'status' => 'available', 'price' => 100]);
        Machine::create(['name' => 'Machine 900', 'status' => 'available', 'price' => 900]);

        $this->get('/catalogo?sort=price&dir=desc')
            ->assertOk()
            ->assertSeeInOrder(['Machine 900', 'Machine 500', 'Machine 100']);
    }

    public function test_catalog_can_filter_available_machines_by_min_price(): void
    {
        Machine::create(['name' => 'Machine Cheap', 'status' => 'available', 'price' => 100]);
        Machine::create(['name' => 'Machine Expensive', 'status' => 'available', 'price' => 900]);
        Machine::create(['name' => 'Machine Sold', 'status' => 'sold', 'price' => 50]);

        $this->get('/catalogo?price_min=200')
            ->assertOk()
            ->assertDontSee('Machine Cheap')
            ->assertSee('Machine Expensive')
            ->assertDontSee('Machine Sold');
    }

    public function test_catalog_can_filter_available_machines_by_max_price(): void
    {
        Machine::create(['name' => 'Machine Cheap', 'status' => 'available', 'price' => 100]);
        Machine::create(['name' => 'Machine Expensive', 'status' => 'available', 'price' => 900]);
        Machine::create(['name' => 'Machine Sold', 'status' => 'sold', 'price' => 50]);

        $this->get('/catalogo?price_max=200')
            ->assertOk()
            ->assertSee('Machine Cheap')
            ->assertDontSee('Machine Expensive')
            ->assertDontSee('Machine Sold');
    }

    public function test_catalog_can_filter_available_machines_by_price_range(): void
    {
        Machine::create(['name' => 'Machine 100', 'status' => 'available', 'price' => 100]);
        Machine::create(['name' => 'Machine 600', 'status' => 'available', 'price' => 600]);
        Machine::create(['name' => 'Machine 900', 'status' => 'available', 'price' => 900]);

        $this->get('/catalogo?price_min=200&price_max=800')
            ->assertOk()
            ->assertDontSee('Machine 100')
            ->assertSee('Machine 600')
            ->assertDontSee('Machine 900');
    }

    public function test_invalid_catalog_price_filter_is_ignored(): void
    {
        Machine::create(['name' => 'Machine Alpha', 'status' => 'available', 'price' => 100]);
        Machine::create(['name' => 'Machine Beta', 'status' => 'available', 'price' => 900]);
        Machine::create(['name' => 'Machine Gamma Sold', 'status' => 'sold', 'price' => 50]);

        $this->get('/catalogo?price_min=invalid&price_max=invalid')
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
            $this->get('/catalogo?price_max=' . urlencode($filter))
                ->assertOk()
                ->assertSee('Machine Localized Cheap')
                ->assertDontSee('Machine Localized Expensive');
        }
    }

    public function test_featured_available_machine_is_listed_on_homepage(): void
    {
        Machine::create(['name' => 'Homepage Featured', 'status' => 'available', 'featured' => true]);
        Machine::create(['name' => 'Homepage Not Featured', 'status' => 'available', 'featured' => false]);
        Machine::create(['name' => 'Homepage Featured Sold', 'status' => 'sold', 'featured' => true]);

        $this->get(route('site.home'))
            ->assertOk()
            ->assertSee('Homepage Featured')
            ->assertDontSee('Homepage Not Featured')
            ->assertDontSee('Homepage Featured Sold');
    }
}
