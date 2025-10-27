<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

class SettingsController extends Controller
{
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
            'enable_registration' => 'nullable|boolean',
            'enable_offline_mode' => 'nullable|boolean',
            'enable_email_notifications' => 'nullable|boolean',
            'enable_analytics' => 'nullable|boolean',
        ]);

        // Get current settings
        $settings = $this->getSettings();

        // Update settings
        $settings = array_merge($settings, $validated);

        // Save to cache
        Cache::forever('system_settings', $settings);

        // If offline mode is being disabled, unregister service worker
        if (!$settings['enable_offline_mode']) {
            Cache::put('unregister_sw', true, 3600);
        }

        return redirect()->route('admin.settings')
            ->with('success', 'Settings updated successfully.');
    }

    /**
     * Get system settings.
     */
    private function getSettings()
    {
        return Cache::remember('system_settings', 3600, function () {
            return [
                'app_name' => 'BMMB Digital Forms',
                'app_url' => url('/'),
                'app_description' => 'Digital form management system for BMMB',
                'timezone' => 'UTC',
                'language' => 'en',
                'enable_registration' => true,
                'enable_offline_mode' => true,
                'enable_email_notifications' => true,
                'enable_analytics' => false,
            ];
        });
    }
}

