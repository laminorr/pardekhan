<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// پردازش خودکار دورهمی‌ها — هر ۱۵ دقیقه
Schedule::command('pardekhan:process-events')
    ->everyFifteenMinutes()
    ->withoutOverlapping();
