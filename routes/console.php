<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// ── ONYX Scheduled Tasks ──────────────────────────────────────────────────────

// Send hearing reminders daily at 07:00 EAT (UTC+3 = 04:00 UTC)
// Reminds officers for hearings today, in 1 day, 3 days, and 7 days
Schedule::command('onyx:hearing-reminders')
    ->dailyAt('04:00')
    ->timezone('Africa/Nairobi')
    ->withoutOverlapping()
    ->appendOutputTo(storage_path('logs/hearing-reminders.log'));
