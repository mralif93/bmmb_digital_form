<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Schedule QR code regeneration every hour
Schedule::command('qr-codes:regenerate')
    ->daily()
    ->withoutOverlapping()
    ->onOneServer()
    ->appendOutputTo(storage_path('logs/qr-codes-regeneration.log'));

// Retrieve system settings
$settings = \Illuminate\Support\Facades\Cache::get('system_settings', []);

// Schedule MAP branches/states/regions sync daily at 5 AM (before user sync)
Schedule::command('map:sync-branches --all')
    ->dailyAt('05:00')
    ->timezone('Asia/Kuala_Lumpur')
    ->withoutOverlapping()
    ->onOneServer()
    ->appendOutputTo(storage_path('logs/map-branch-sync.log'));

// Schedule MAP user sync based on settings
if ($settings['map_sync_enabled'] ?? true) {
    $frequency = $settings['map_sync_frequency'] ?? 'daily';
    $time = $settings['map_sync_time'] ?? '06:00';

    $command = Schedule::command('map:sync-from-db')
        ->timezone('Asia/Kuala_Lumpur')
        ->withoutOverlapping()
        ->onOneServer()
        ->appendOutputTo(storage_path('logs/map-user-sync.log'))
        ->emailOutputOnFailure(config('mail.admin_email', 'admin@example.com'));

    switch ($frequency) {
        case 'every_15_minutes':
            $command->everyFifteenMinutes();
            break;
        case 'every_30_minutes':
            $command->everyThirtyMinutes();
            break;
        case 'hourly':
            $command->hourly();
            break;
        case 'every_4_hours':
            $command->everyFourHours();
            break;
        case 'daily':
        default:
            $command->dailyAt($time);
            break;
    }
}
