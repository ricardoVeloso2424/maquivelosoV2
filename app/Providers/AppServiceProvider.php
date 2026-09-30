<?php

namespace App\Providers;

use App\Models\Setting;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        View::composer(['layouts.site', 'site.*'], function ($view) {
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

            // getSiteSettings caches "forever", so on a warm cache there is no
            // per-request DB query. The try/catch keeps the public site working
            // before migrations run or when the DB/cache is briefly unavailable,
            // without paying for a Schema::hasTable() check on every request.
            try {
                $settings = Setting::getSiteSettings($defaults);
            } catch (\Throwable) {
                $settings = $defaults;
            }

            $view->with('siteSettings', $settings);
        });
    }
}
