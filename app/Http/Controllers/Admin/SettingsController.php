<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Traits\LogsAuditTrail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

class SettingsController extends Controller
{
    use LogsAuditTrail;
    /**
     * Display the settings page.
     */
    public function index()
    {
        $settings = $this->getSettings();
        return view('admin.settings', compact('settings'));
    }

    /**
     * Update system settings.
     */
    public function update(Request $request)
    {
        $validated = $request->validate([
            'app_name' => 'nullable|string|max:255',
            'app_url' => 'nullable|url|max:255',
            'app_description' => 'nullable|string|max:1000',
            'timezone' => 'nullable|string',
            'language' => 'nullable|string',
            'date_format' => 'nullable|string',
            'time_format' => 'nullable|string',
            'qr_code_expiration_minutes' => 'nullable|integer|min:1|max:10080', // 1 minute to 7 days (10080 minutes)
            'enable_registration' => 'nullable|boolean',
            'enable_offline_mode' => 'nullable|boolean',
            'enable_email_notifications' => 'nullable|boolean',
            'enable_analytics' => 'nullable|boolean',
            'default_theme' => 'nullable|string|in:light,dark,auto',
            'primary_color' => ['nullable', 'string', 'regex:/^#([0-9A-Fa-f]{3}|[0-9A-Fa-f]{6})$/'],
            'map_sync_enabled' => 'nullable|boolean',
            'map_sync_frequency' => 'nullable|string|in:daily,hourly,every_4_hours,every_30_minutes,every_15_minutes',
            'map_sync_time' => 'nullable|date_format:H:i',
        ]);

        // Get current settings
        $oldSettings = $this->getSettings();

        // Update settings
        $settings = array_merge($oldSettings, $validated);

        // Save to cache
        Cache::forever('system_settings', $settings);

        // If offline mode is being disabled, unregister service worker
        if (!$settings['enable_offline_mode']) {
            Cache::put('unregister_sw', true, 3600);
        }

        // Log audit trail
        $this->logAuditTrail(
            action: 'update',
            description: 'Updated system settings',
            modelType: null,
            modelId: null,
            oldValues: $oldSettings,
            newValues: $settings
        );

        return redirect()->route('admin.settings')
            ->with('success', 'Settings updated successfully.');
    }

    /**
     * Get system settings.
     */
    private function getSettings()
    {
        // First check if settings exist in cache
        $settings = Cache::get('system_settings');

        // If not in cache, return defaults
        if (!$settings) {
            return [
                'app_name' => 'BMMB Digital Forms',
                'app_url' => url('/'),
                'app_description' => 'Digital form management system for BMMB',
                'timezone' => 'UTC',
                'language' => 'en',
                'date_format' => 'Y-m-d',
                'time_format' => 'H:i',
                'qr_code_expiration_minutes' => 60, // Default 60 minutes (1 hour)
                'enable_registration' => true,
                'enable_offline_mode' => true,
                'enable_email_notifications' => true,
                'enable_analytics' => false,
                'default_theme' => 'light',
                'primary_color' => '#FE8000', // Default orange color
                'map_sync_enabled' => true,
                'map_sync_frequency' => 'daily',
                'map_sync_time' => '06:00',
            ];
        }

        // Merge with defaults to ensure all keys exist
        return array_merge([
            'app_name' => 'BMMB Digital Forms',
            'app_url' => url('/'),
            'app_description' => 'Digital form management system for BMMB',
            'timezone' => 'UTC',
            'language' => 'en',
            'date_format' => 'Y-m-d',
            'time_format' => 'H:i',
            'qr_code_expiration_minutes' => 60,
            'enable_registration' => true,
            'enable_offline_mode' => true,
            'enable_email_notifications' => true,
            'enable_analytics' => false,
            'default_theme' => 'light',
            'primary_color' => '#FE8000',
            'map_sync_enabled' => true,
            'map_sync_frequency' => 'daily',
            'map_sync_time' => '06:00',
        ], $settings);
    }
}

