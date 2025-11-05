<?php

namespace App\Traits;

use Illuminate\Support\Facades\Cache;

trait UsesSystemTimezone
{
    /**
     * Get the system timezone from settings.
     * Falls back to UTC if not set.
     */
    protected function getSystemTimezone(): string
    {
        $settings = Cache::get('system_settings', []);
        return $settings['timezone'] ?? config('app.timezone', 'UTC');
    }

    /**
     * Get a Carbon instance with the system timezone.
     */
    protected function nowInSystemTimezone()
    {
        return now()->setTimezone($this->getSystemTimezone());
    }
}
