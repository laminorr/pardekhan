<?php

namespace App\Console\Commands;

use App\Services\ActivitySimulation\OnlineActivityEngine;
use App\Services\ActivitySimulation\WeeklyMovieWatchingEngine;
use Carbon\CarbonImmutable;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

/**
 * پیش‌نمایش/QA: ۷ روزِ متوالی را برای هر دو متریک تولید و برای رسم نمودار ذخیره
 * می‌کند. این تولیدِ محصولی نیست — فقط برای بازبینیِ چشمیِ همواری و شکافِ online≫watching.
 */
class ActivitySimulateWeek extends Command
{
    protected $signature = 'pardekhan:activity-simulate-week
        {--start= : تاریخِ شروع (Y-m-d، به وقتِ تهران)؛ پیش‌فرض: امروز}';

    protected $description = 'تولیدِ ۷ روزِ شبیه‌سازی‌شده برای هر دو متریک (پیش‌نمایش/QA)';

    public function handle(
        OnlineActivityEngine $online,
        WeeklyMovieWatchingEngine $watching,
    ): int {
        $tz    = config('activity_simulation.timezone');
        $start = $this->option('start')
            ? CarbonImmutable::createFromFormat('Y-m-d', $this->option('start'), $tz)->startOfDay()
            : CarbonImmutable::now($tz)->startOfDay();

        $dir = storage_path('app/activity-sim');
        File::ensureDirectoryExists($dir);

        $csvRows   = ['minute,online,watching'];
        $summary   = [];
        $weekMinute = 0;

        for ($day = 0; $day < 7; $day++) {
            $date    = $start->addDays($day);
            $dateStr = $date->format('Y-m-d');

            $onlineTraj   = $online->computeTrajectory($dateStr);
            $watchingTraj = $watching->computeTrajectory($dateStr);

            File::put(
                "{$dir}/day-".($day + 1).'.json',
                json_encode([
                    'date'     => $dateStr,
                    'online'   => $onlineTraj,
                    'watching' => $watchingTraj,
                ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE),
            );

            for ($m = 0; $m < 1440; $m++) {
                $csvRows[] = "{$weekMinute},{$onlineTraj[$m]},{$watchingTraj[$m]}";
                $weekMinute++;
            }

            $summary[] = [
                'date'        => $dateStr,
                'online_min'  => min($onlineTraj),
                'online_max'  => max($onlineTraj),
                'watch_min'   => min($watchingTraj),
                'watch_max'   => max($watchingTraj),
            ];
        }

        File::put("{$dir}/activity-week.csv", implode("\n", $csvRows)."\n");

        $this->info("۷ روز در {$dir} نوشته شد (day-1.json … day-7.json + activity-week.csv).");
        $this->table(
            ['تاریخ', 'online کمینه', 'online بیشینه', 'watching کمینه', 'watching بیشینه'],
            array_map(fn ($r) => [$r['date'], $r['online_min'], $r['online_max'], $r['watch_min'], $r['watch_max']], $summary),
        );

        return self::SUCCESS;
    }
}
