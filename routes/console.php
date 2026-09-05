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

// انتخابِ خودکارِ فیلمِ هفته — هر شنبه ۰۰:۰۱ به وقتِ تهران (۶ = شنبه)
Schedule::command('pardekhan:assign-weekly-movie')
    ->weeklyOn(6, '00:01')
    ->timezone('Asia/Tehran')
    ->withoutOverlapping();
