<?php

namespace App\Providers;

use App\Models\Setting;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        View::composer('layouts.site', function ($view) {
            $view->with('siteSettings', [
                'business_name' => Setting::get('business_name', 'MaquiVeloso'),
                'phone' => Setting::get('phone', ''),
                'email' => Setting::get('email', ''),
                'location' => Setting::get('location', ''),
            ]);
        });
    }
}
