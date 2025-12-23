<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL;

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
        // Force URL generation to use APP_URL
        // This is critical when app is served from a subdirectory like /eform
        if (config('app.url')) {
            URL::forceRootUrl(config('app.url'));
        }

        // Force HTTPS in production, staging, and SIT environments
        // OR when the request is coming through an HTTPS proxy
        $shouldForceHttps = in_array(config('app.env'), ['production', 'staging', 'sit'])
            || $this->isBehindHttpsProxy();

        if ($shouldForceHttps) {
            URL::forceScheme('https');
        }

        // Fix pagination URLs when served from subdirectory
        // Nginx strips /eform before forwarding to Laravel, so we need to tell
        // Laravel's paginator to include /eform in the current path
        if (str_contains(config('app.url'), '/eform')) {
            \Illuminate\Pagination\Paginator::currentPathResolver(function () {
                return '/eform/' . ltrim(request()->path(), '/');
            });
        }
    }

    /**
     * Check if the application is behind an HTTPS proxy
     * 
     * @return bool
     */
    private function isBehindHttpsProxy(): bool
    {
        // Check common proxy headers that indicate HTTPS
        if (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') {
            return true;
        }

        if (!empty($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https') {
            return true;
        }

        if (!empty($_SERVER['HTTP_X_FORWARDED_SSL']) && $_SERVER['HTTP_X_FORWARDED_SSL'] === 'on') {
            return true;
        }

        return false;
    }
}
