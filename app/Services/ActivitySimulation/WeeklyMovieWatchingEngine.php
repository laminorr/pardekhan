<?php

namespace App\Services\ActivitySimulation;

/**
 * موتورِ متریکِ «در حالِ دیدنِ فیلمِ هفته». آرام‌تر و پایین‌تر از online؛
 * تفاوت فقط نامِ متریک + override‌های config است.
 */
final class WeeklyMovieWatchingEngine extends AbstractActivityEngine
{
    protected function metric(): string
    {
        return 'watching';
    }
}
