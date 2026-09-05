<?php

namespace App\Services\ActivitySimulation;

use Carbon\CarbonImmutable;
use Carbon\CarbonInterface;

/**
 * هماهنگ‌کنندهٔ دو موتور. مقدارِ دقیقهٔ جاریِ هر متریک را می‌خواند و گاردِ نهاییِ
 * نسبت/سلامت را اعمال می‌کند. این تنها جایی است که دو متریک با هم تعامل دارند؛
 * موتورها هیچ حالتِ مشترکی ندارند.
 */
class ActivitySimulationManager
{
    public function __construct(
        private readonly OnlineActivityEngine $online,
        private readonly WeeklyMovieWatchingEngine $watching,
    ) {
    }

    /** آمارِ لحظهٔ جاری (یا لحظهٔ داده‌شده) پس از گاردِ نسبت. */
    public function current(?CarbonInterface $time = null): ActivityStatsDTO
    {
        $tz  = config('activity_simulation.timezone');
        $now = $time !== null
            ? CarbonImmutable::createFromInterface($time)->setTimezone($tz)
            : CarbonImmutable::now($tz);

        $online   = $this->online->getCurrentValue($now);
        $watching = $this->watching->getCurrentValue($now);

        $watching = $this->applyRatioGuard($online, $watching);

        return new ActivityStatsDTO($online, $watching, $now);
    }

    /**
     * گاردِ نسبت: watching باید معنادار پایین‌تر از online بماند. اگر از سقفِ
     * مجاز عبور کند، به زیرِ آن کشیده می‌شود (اما هرگز زیرِ کفِ خودش).
     */
    public function applyRatioGuard(int $online, int $watching): int
    {
        $fraction = (float) config('activity_simulation.engine.ratio_guard.watching_max_fraction_of_online');
        $floor    = $this->watching->floor();

        if ($watching >= $fraction * $online) {
            $cap      = (int) floor($fraction * $online) - 1;
            $watching = max($floor, $cap);
        }

        return $watching;
    }
}
