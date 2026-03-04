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
        View::composer('layouts.site', function ($view) {
            $defaults = [
                'business_name' => 'MaquiVeloso',
                'phone' => '',
                'email' => '',
                'location' => '',
            ];

            $settings = $defaults;

            try {
                if (Schema::hasTable('settings')) {
                    $settings = Setting::getMany($defaults);
                }
            } catch (\Throwable) {
                // Keep defaults when DB/table is unavailable.
            }

            $view->with('siteSettings', $settings);
        });
    }
}
