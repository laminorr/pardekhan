<?php

namespace App\Console\Commands;

use App\Models\WeeklyMovieAssignment;
use App\Services\WeeklyMovie\WeeklyMovieAssigner;
use App\Services\WeeklyMovie\WeeklyMovieWeekResolver;
use Illuminate\Console\Command;

class AssignWeeklyMovie extends Command
{
    protected $signature = 'pardekhan:assign-weekly-movie';

    protected $description = 'انتخابِ خودکارِ فیلمِ هفتهٔ جاری (در صورتِ نبودِ تخصیصِ فعال)';

    public function handle(): int
    {
        if (! config('weekly_movie.auto_enabled')) {
            $this->info('غیرفعال');

            return self::SUCCESS;
        }

        $week = (new WeeklyMovieWeekResolver)->currentWeek();

        // پیش از تخصیص بررسی کن تا خروجی دقیق گزارش شود (آیا از قبل تخصیصِ فعال بود؟).
        $hadActive = WeeklyMovieAssignment::active()
            ->forWeek($week['start']->toDateString())
            ->exists();

        $assignment = app(WeeklyMovieAssigner::class)->ensureAssignedFor($week['start']);

        if (! $assignment) {
            $this->warn('هیچ فیلمِ فعالی برای انتخاب وجود ندارد.');

            return self::SUCCESS;
        }

        $title = $assignment->film?->title ?? "#{$assignment->film_id}";

        if ($hadActive) {
            $this->info("تخصیصِ فعال از قبل وجود داشت: «{$title}» (بدون تغییر)");
        } else {
            $this->info("فیلمِ هفته به‌صورتِ خودکار انتخاب شد: «{$title}»");
        }

        return self::SUCCESS;
    }
}
