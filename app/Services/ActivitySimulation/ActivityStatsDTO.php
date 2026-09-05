<?php

namespace App\Services\ActivitySimulation;

use Carbon\CarbonImmutable;

/**
 * خروجیِ لحظه‌ایِ شبیه‌ساز برای هر دو متریک، پس از گاردِ نسبت.
 */
final class ActivityStatsDTO
{
    public function __construct(
        public readonly int $online,
        public readonly int $watchingWeeklyMovie,
        public readonly CarbonImmutable $generatedAt,
    ) {
    }

    public function toArray(): array
    {
        return [
            'online'              => $this->online,
            'watchingWeeklyMovie' => $this->watchingWeeklyMovie,
            'generatedAt'         => $this->generatedAt->toIso8601String(),
        ];
    }
}
