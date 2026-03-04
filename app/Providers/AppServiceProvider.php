<?php

namespace App\Providers;

use App\Models\Setting;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        View::composer(['layouts.site', 'site.contact'], function ($view) {
            $defaults = [
                'business_name' => 'MaquiVeloso',
                'phone' => '',
                'email' => '',
                'location' => '',
                'contact_phone' => '',
                'contact_email' => '',
                'contact_address' => '',
                'contact_whatsapp' => '',
                'contact_hours' => '',
            ];

            $settings = $defaults;

            try {
                if (Schema::hasTable('settings')) {
                    $settings = Setting::getSiteSettings($defaults);
                }
            } catch (\Throwable) {
                // Keep defaults when DB/table is unavailable.
            }

            $view->with('siteSettings', $settings);
        });
    }
}
