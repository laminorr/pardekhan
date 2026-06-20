<?php

namespace App\Console\Commands;

use App\Models\Event;
use App\Services\MessagingService;
use Illuminate\Console\Command;

class ProcessEvents extends Command
{
    protected $signature = 'pardekhan:process-events';
    protected $description = 'پردازش خودکار دورهمی‌ها (بستن ثبت‌نام، حد نصاب، اطلاع‌رسانی)';

    public function handle(): int
    {
        $now = now();
        $this->info('شروع پردازش: ' . $now->format('Y-m-d H:i'));

        // ۱. بستن ثبت‌نام ۱۲ ساعت قبل + بررسی حد نصاب
        $this->closeRegistrations($now);

        // ۲. علامت‌زدن دورهمی‌های تمام‌شده
        $this->completeEvents($now);

        $this->info('پردازش تمام شد.');
        return self::SUCCESS;
    }

    // بستن ثبت‌نام دورهمی‌هایی که کمتر از ۱۲ ساعت تا شروعشون مونده
    private function closeRegistrations($now): void
    {
        $events = Event::where('status', 'active')
            ->where('starts_at', '>', $now)
            ->where('starts_at', '<=', $now->copy()->addHours(12))
            ->get();

        foreach ($events as $event) {
            $confirmed = $event->confirmedCount();

            if ($confirmed < $event->min_quorum) {
                // به حد نصاب نرسیده — نیاز به تصمیم مدیر
                $event->update(['status' => 'needs_decision']);
                $this->warn("دورهمی «{$event->title}» به حد نصاب نرسید (نیاز به تصمیم)");
            } else {
                // ثبت‌نام بسته میشه
                $event->update(['status' => 'closed']);
                $this->line("ثبت‌نام دورهمی «{$event->title}» بسته شد");
            }
        }
    }

    // دورهمی‌های گذشته رو completed کن
    private function completeEvents($now): void
    {
        $events = Event::whereIn('status', ['active', 'full', 'closed'])
            ->where('starts_at', '<', $now)
            ->get();

        foreach ($events as $event) {
            $event->update(['status' => 'completed']);

            // غایب‌ها رو مشخص کن (ثبت‌نام کرده ولی حاضر نشده) + امتیاز منفی
            $absentees = $event->registrations()
                ->where('attendance_status', 'registered')
                ->with('member')
                ->get();

            foreach ($absentees as $reg) {
                $reg->update(['attendance_status' => 'absent']);
                app(\App\Services\ScoreService::class)->addByKey($reg->member, 'absence');
            }

            $this->line("دورهمی «{$event->title}» تمام‌شده علامت خورد ({$absentees->count()} غایب)");
        }
    }
}
