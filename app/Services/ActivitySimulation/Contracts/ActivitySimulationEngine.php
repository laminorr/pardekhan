<?php

namespace App\Services\ActivitySimulation\Contracts;

use Carbon\CarbonImmutable;
use Carbon\CarbonInterface;

/**
 * موتورِ شبیه‌سازیِ فعالیت برای یک متریک (online یا watching).
 *
 * این موتور فقط از مقادیرِ لنگرِ ادمین + config + یک seedِ قطعی استفاده می‌کند و
 * هرگز به دادهٔ واقعیِ Presence / Analytics / Session دست نمی‌زند.
 */
interface ActivitySimulationEngine
{
    /** مقدارِ متریک در دقیقهٔ جاری (به وقتِ تهران). */
    public function getCurrentValue(CarbonInterface $time): int;

    /** مسیرِ کاملِ ۱۴۴۰-دقیقه‌ایِ یک روز (اندیس = دقیقهٔ روز، ۰..۱۴۳۹). */
    public function getTrajectory(CarbonImmutable $date): array;

    /** بازتولید و کش‌کردنِ مسیرِ یک روز (کش را نادیده می‌گیرد). */
    public function regenerate(CarbonImmutable $date): void;
}
