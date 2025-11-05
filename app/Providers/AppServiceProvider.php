<?php

namespace App\Providers;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use App\Helpers\TimezoneHelper;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Share TimezoneHelper with all views
        View::share('timezoneHelper', new class {
            public function convert($date) {
                return TimezoneHelper::toSystemTimezone($date);
            }
        });
    }
}
