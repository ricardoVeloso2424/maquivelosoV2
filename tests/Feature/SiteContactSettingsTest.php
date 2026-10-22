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
        Setting::set('contact_whatsapp', '351960000000');
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

    public function test_contact_page_builds_navigation_links_from_configured_address(): void
    {
        Setting::set('contact_address', "Rua Central 123\nBraga");
        $destination = rawurlencode('Rua Central 123, Braga');

        $this->get(route('site.contact'))
            ->assertOk()
            ->assertSee('Como chegar')
            ->assertSee('href="https://www.google.com/maps/dir/?api=1&amp;destination=' . $destination . '"', false)
            ->assertSee('href="https://waze.com/ul?q=' . $destination . '&amp;navigate=yes"', false)
            ->assertSee('rel="noopener noreferrer"', false);
    }

    public function test_contact_page_hides_location_box_without_address(): void
    {
        Setting::set('contact_phone', '+351 960 000 000');

        $this->get(route('site.contact'))
            ->assertOk()
            ->assertSee('+351 960 000 000')
            ->assertDontSee('Como chegar')
            ->assertDontSee('google.com/maps', false)
            ->assertDontSee('waze.com', false);
    }

    public function test_contact_page_falls_back_to_legacy_location_setting(): void
    {
        Setting::set('location', 'Braga, Portugal');

        $this->get(route('site.contact'))
            ->assertOk()
            ->assertSee('Braga, Portugal')
            ->assertSee('destination=' . rawurlencode('Braga, Portugal'), false);
    }

    public function test_contact_page_hides_whatsapp_cta_when_setting_is_missing(): void
    {
        $this->get(route('site.contact'))
            ->assertOk()
            ->assertDontSee('Falar no WhatsApp')
            ->assertDontSee('wa.me/', false);
    }
}
