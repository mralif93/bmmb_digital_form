<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Cache;

class TimezoneHelper
{
    /**
     * Get the system timezone from settings.
     * Falls back to config/app.php timezone or UTC.
     */
    public static function getSystemTimezone(): string
    {
        $settings = Cache::get('system_settings', []);
        return $settings['timezone'] ?? config('app.timezone', 'UTC');
    }

    /**
     * Convert a Carbon instance to system timezone.
     * Returns null if date is null.
     */
    public static function toSystemTimezone($date)
    {
        if (!$date) {
            return null;
        }

        $timezone = self::getSystemTimezone();
        return $date->copy()->setTimezone($timezone);
    }

    /**
     * Alias for toSystemTimezone() for backward compatibility
     */
    public static function convert($date)
    {
        return self::toSystemTimezone($date);
    }
}
