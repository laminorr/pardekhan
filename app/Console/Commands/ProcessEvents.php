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

        // ۳. یادآوری دورهمی (۲۴ ساعت قبل)
        $this->sendReminders($now);

        // ۴. درخواست بازخورد (بعد از دورهمی)
        $this->requestFeedback($now);

        // ۵. پردازش صف پیامک (دسته‌ای)
        $sent = app(\App\Services\SmsService::class)->processBatch(50);
        if ($sent > 0) {
            $this->info("تعداد {$sent} پیامک ارسال شد");
        }

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

    // یادآوری دورهمی ۲۴ ساعت قبل
    private function sendReminders($now): void
    {
        $pattern = \App\Models\Setting::get('sms_pattern_reminder', '');
        if (! $pattern) return;

        $events = Event::whereIn('status', ['closed', 'active', 'full'])
            ->whereBetween('starts_at', [$now->copy()->addHours(23), $now->copy()->addHours(25)])
            ->where('reminder_sent', false)
            ->get();

        $sms = app(\App\Services\SmsService::class);
        foreach ($events as $event) {
            $regs = $event->registrations()
                ->where('attendance_status', 'registered')
                ->with('member')
                ->get();

            foreach ($regs as $reg) {
                $sms->queue($reg->member->phone, $pattern, [
                    'name'  => $reg->member->first_name,
                    'event' => $event->title,
                ], 'reminder');
            }

            $event->update(['reminder_sent' => true]);
            $this->line("یادآوری دورهمی «{$event->title}» به صف اضافه شد");
        }
    }

    // درخواست بازخورد بعد از دورهمی
    private function requestFeedback($now): void
    {
        $pattern = \App\Models\Setting::get('sms_pattern_feedback', '');
        if (! $pattern) return;

        $events = Event::where('status', 'completed')
            ->where('feedback_requested', false)
            ->where('starts_at', '<', $now)
            ->where('starts_at', '>', $now->copy()->subHours(48))
            ->get();

        $sms = app(\App\Services\SmsService::class);
        foreach ($events as $event) {
            $regs = $event->registrations()
                ->where('attendance_status', 'attended')
                ->with('member')
                ->get();

            foreach ($regs as $reg) {
                $sms->queue($reg->member->phone, $pattern, [
                    'name'  => $reg->member->first_name,
                    'event' => $event->title,
                ], 'feedback');
            }

            $event->update(['feedback_requested' => true]);
            $this->line("درخواست بازخورد دورهمی «{$event->title}» به صف اضافه شد");
        }
    }

}
