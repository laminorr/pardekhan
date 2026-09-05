<?php

namespace App\Services\ActivitySimulation;

/**
 * منحنیِ پایهٔ ۲۴ ساعته که به‌صورتِ هموار (smootherstep) از میانِ لنگرها می‌گذرد.
 *
 * لنگرها نقاطِ کنترل‌اند (نه سطل‌های ثابت). بینِ دو لنگرِ متوالی با
 * f(t)=6t⁵−15t⁴+10t³ درون‌یابی می‌شود (شیبِ نزدیکِ صفر روی لنگرها، بدونِ شکست و
 * بدونِ overshoot). قطعهٔ شب → نیمه‌شبِ روزِ بعد از مرزِ ۲۴:۰۰ عبور می‌کند تا منحنی
 * پیوسته و متناوب بماند.
 */
final class BaselineCurve
{
    /** @var list<array{minute:int,value:float}> صعودی بر حسبِ دقیقهٔ روز */
    private array $points = [];

    /**
     * @param  array<int,int|float>  $anchors  نگاشتِ [دقیقهٔ روز => مقدار]
     */
    public function __construct(array $anchors)
    {
        ksort($anchors);
        foreach ($anchors as $minute => $value) {
            $this->points[] = ['minute' => (int) $minute, 'value' => (float) $value];
        }
    }

    /** تابعِ هموارسازِ درجه‌پنج، پایدارشده در بازهٔ [0,1]. */
    public static function smootherstep(float $t): float
    {
        if ($t <= 0.0) {
            return 0.0;
        }
        if ($t >= 1.0) {
            return 1.0;
        }

        return $t * $t * $t * ($t * ($t * 6.0 - 15.0) + 10.0);
    }

    /** مقدارِ پایه در دقیقهٔ روز (۰..۱۴۳۹). */
    public function valueAt(int $minute): float
    {
        $n = count($this->points);
        if ($n === 0) {
            return 0.0;
        }
        if ($n === 1) {
            return $this->points[0]['value'];
        }

        for ($i = 0; $i < $n; $i++) {
            $a = $this->points[$i];
            $b = $this->points[($i + 1) % $n];

            $start = $a['minute'];
            $end   = $b['minute'];
            if ($end <= $start) {
                $end += 1440; // قطعهٔ عبوری از نیمه‌شب
            }

            $m = $minute;
            if ($m < $start) {
                $m += 1440;
            }

            if ($m >= $start && $m <= $end) {
                $span = $end - $start;
                $t    = $span === 0 ? 0.0 : ($m - $start) / $span;

                return $a['value'] + ($b['value'] - $a['value']) * self::smootherstep($t);
            }
        }

        // نباید رخ دهد چون قطعه‌ها تمامِ ۱۴۴۰ دقیقه را می‌پوشانند.
        return $this->points[0]['value'];
    }
}
