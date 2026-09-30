<?php

namespace Tests\Feature;

use App\Models\Machine;
use Tests\TestCase;

class MachinePricePresentationTest extends TestCase
{
    public function test_price_and_negotiable_shows_both(): void
    {
        $machine = new Machine(['price' => 1234, 'negotiable' => true]);

        $this->assertSame('price_negotiable', $machine->priceState());
        $this->assertSame('1.234 €', $machine->price_formatted);
    }

    public function test_only_price_shows_price(): void
    {
        $machine = new Machine(['price' => 500, 'negotiable' => false]);

        $this->assertSame('price', $machine->priceState());
        $this->assertSame('500 €', $machine->price_formatted);
    }

    public function test_only_negotiable_shows_negotiable(): void
    {
        $machine = new Machine(['price' => null, 'negotiable' => true]);

        $this->assertSame('negotiable', $machine->priceState());
        $this->assertNull($machine->price_formatted);
    }

    public function test_neither_shows_on_request(): void
    {
        $machine = new Machine(['price' => null, 'negotiable' => false]);

        $this->assertSame('on_request', $machine->priceState());
        $this->assertNull($machine->price_formatted);
    }

    public function test_status_label_comes_from_config(): void
    {
        $this->assertSame('Disponível', (new Machine(['status' => 'available']))->status_label);
        $this->assertSame('Reservada', (new Machine(['status' => 'reserved']))->status_label);
        $this->assertSame('Vendida', (new Machine(['status' => 'sold']))->status_label);
        $this->assertSame('Indisponível', (new Machine(['status' => 'inactive']))->status_label);
    }
}
