<?php

namespace App\Services\WeeklyMovie;

use Carbon\Carbon;

/**
 * محاسبهٔ بازهٔ هفته برای «فیلم هفته».
 *
 * هفته از شنبه ۰۰:۰۰:۰۰ تا جمعهٔ ۲۳:۵۹:۵۹ به وقت تهران است و
 * مستقل از تایم‌زون مرورگر یا تنظیمات کاربر محاسبه می‌شود.
 */
class WeeklyMovieWeekResolver
{
    private const TZ = 'Asia/Tehran';

    /** بازهٔ هفتهٔ جاری: ['start' => شنبه ۰۰:۰۰، 'end' => جمعه ۲۳:۵۹:۵۹]. */
    public function currentWeek(): array
    {
        return $this->weekFor(Carbon::now(self::TZ));
    }

    /** بازهٔ هفته‌ای که تاریخ داده‌شده در آن قرار دارد (شنبه → جمعه، تهران). */
    public function weekFor(Carbon $date): array
    {
        // به وقت تهران ببر تا مرز روز درست حساب شود
        $local = $date->copy()->setTimezone(self::TZ);

        // شنبهٔ همین هفته: از روز فعلی به عقب تا رسیدن به شنبه
        $start = $local->copy()->startOfDay();
        while ($start->dayOfWeek !== Carbon::SATURDAY) {
            $start->subDay();
        }

        $end = $start->copy()->addDays(6)->endOfDay(); // جمعهٔ ۲۳:۵۹:۵۹

        return [
            'start' => $start,
            'end'   => $end,
        ];
    }
}
