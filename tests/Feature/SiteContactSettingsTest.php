<?php

namespace Tests\Feature;

use App\Models\Setting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SiteContactSettingsTest extends TestCase
{
    use RefreshDatabase;

    public function test_contact_page_renders_contact_settings_from_backoffice(): void
    {
        Setting::set('contact_phone', '+351 960 000 000');
        Setting::set('contact_email', 'contacto@maquiveloso.pt');
        Setting::set('contact_address', "Rua Central 123\nBraga");
        Setting::set('contact_whatsapp', '+351960000000');
        Setting::set('contact_hours', 'Seg-Sex: 09:00-18:00');

        $this->get(route('site.contact'))
            ->assertOk()
            ->assertSee('+351 960 000 000')
            ->assertSee('contacto@maquiveloso.pt')
            ->assertSee('Rua Central 123')
            ->assertSee('Braga')
            ->assertSee('Seg-Sex: 09:00-18:00')
            ->assertSee('href="tel:+351960000000"', false)
            ->assertSee('href="mailto:contacto@maquiveloso.pt"', false)
            ->assertSee('href="https://wa.me/351960000000"', false);
    }
}
