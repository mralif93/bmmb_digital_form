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
     * 
     * @return \Illuminate\Support\Carbon|null
     */
    public static function toSystemTimezone($date)
    {
        if (!$date) {
            return null;
        }

        $timezone = self::getSystemTimezone();

        // If it's already a Carbon instance
        if ($date instanceof \Carbon\Carbon || $date instanceof \Illuminate\Support\Carbon) {
            return $date->copy()->setTimezone($timezone);
        }

        // If it's a string, try to parse it
        if (is_string($date)) {
            try {
                return \Illuminate\Support\Carbon::parse($date)->setTimezone($timezone);
            } catch (\Exception $e) {
                return null;
            }
        }

        return null; // Return null for other types/failures so caller can fallback
    }

    /**
     * Alias for toSystemTimezone() for backward compatibility
     */
    public static function convert($date)
    {
        return self::toSystemTimezone($date);
    }
}
