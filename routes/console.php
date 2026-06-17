<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// ===== QUEUE & NOTIFICATION SCHEDULE =====

// Send abandoned cart reminder setiap hari jam 09:00
Schedule::command('app:send-abandoned-cart')
    ->dailyAt('09:00')
    ->name('send-abandoned-cart')
    ->description('Send abandoned cart reminders to customers');

// Send payment reminder setiap hari jam 10:00
Schedule::command('app:send-payment-reminder')
    ->dailyAt('10:00')
    ->name('send-payment-reminder')
    ->description('Send payment reminders for unpaid orders');

// Queue worker monitor (optional - untuk production)
// Schedule::command('queue:restart')
//     ->everyFifteenMinutes()
//     ->name('queue-restart')
//     ->description('Restart queue worker gracefully');
