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

// Schedule MAP user sync daily at 6 AM (Malaysia time)
// This ensures user roles and branches are kept in sync with MAP
Schedule::command('map:sync-from-db')
    ->dailyAt('06:00')
    ->timezone('Asia/Kuala_Lumpur')
    ->withoutOverlapping()
    ->onOneServer()
    ->appendOutputTo(storage_path('logs/map-user-sync.log'))
    ->emailOutputOnFailure(config('mail.admin_email', 'admin@example.com'));

// Schedule MAP branches/states/regions sync daily at 5 AM (before user sync)
Schedule::command('map:sync-branches --all')
    ->dailyAt('05:00')
    ->timezone('Asia/Kuala_Lumpur')
    ->withoutOverlapping()
    ->onOneServer()
    ->appendOutputTo(storage_path('logs/map-branch-sync.log'));

// Optional: Run a lighter sync every 4 hours for branch changes
Schedule::command('map:sync-from-db')
    ->everyFourHours()
    ->timezone('Asia/Kuala_Lumpur')
    ->withoutOverlapping()
    ->onOneServer()
    ->appendOutputTo(storage_path('logs/map-user-sync.log'));
