<?php

namespace App\Services\ActivitySimulation;

use App\Models\Setting;
use App\Services\ActivitySimulation\Contracts\ActivitySimulationEngine;
use Carbon\CarbonImmutable;
use Carbon\CarbonInterface;
use Illuminate\Support\Facades\Cache;

/**
 * پایهٔ مشترکِ هر دو موتور. کلِ خطِ لولهٔ ساختِ مسیر + seed + منحنیِ پایه + کش
 * این‌جاست؛ موتورهای مشخص فقط با «نامِ متریک» و override‌های config و کلیدهای
 * لنگرِ خود فرق دارند. هیچ حالتِ مشترکی بینِ موتورها وجود ندارد.
 *
 * منابعِ داده: فقط مقادیرِ لنگرِ ادمین (Setting) + config + seedِ قطعی.
 * هیچ کوئریِ دیتابیسی داخلِ حلقهٔ ۱۴۴۰-دقیقه‌ای اجرا نمی‌شود.
 */
abstract class AbstractActivityEngine implements ActivitySimulationEngine
{
    private const MINUTES_PER_DAY = 1440;

    private ?array $mergedConfig = null;

    /** نامِ متریک: online یا watching. */
    abstract protected function metric(): string;

    // ── API عمومی ─────────────────────────────────────────────────────────

    public function getCurrentValue(CarbonInterface $time): int
    {
        $local   = CarbonImmutable::createFromInterface($time)->setTimezone($this->timezone());
        $dateStr = $local->format('Y-m-d');
        $minute  = $local->hour * 60 + $local->minute;

        $trajectory = $this->cachedTrajectory($dateStr);

        return $trajectory[$minute] ?? $trajectory[0];
    }

    public function getTrajectory(CarbonImmutable $date): array
    {
        return $this->cachedTrajectory($date->setTimezone($this->timezone())->format('Y-m-d'));
    }

    public function regenerate(CarbonImmutable $date): void
    {
        $dateStr = $date->setTimezone($this->timezone())->format('Y-m-d');

        try {
            Cache::forget($this->cacheKey($dateStr));
            Cache::put($this->cacheKey($dateStr), $this->buildTrajectory($dateStr), $this->cacheTtl());
        } catch (\Throwable) {
            // کش قابلِ اعتماد نیست؛ چون مسیر قطعی است، بی‌کش هم درست بازتولید می‌شود.
        }
    }

    /** ساختِ مستقیمِ مسیر بدونِ کش (برای پیش‌نمایش/تست/QA). */
    public function computeTrajectory(string $dateStr): array
    {
        return $this->buildTrajectory($dateStr);
    }

    /** کفِ مقادیرِ این متریک (برای گاردِ نسبت در Manager). */
    public function floor(): int
    {
        return (int) $this->config()['min_floor'];
    }

    // ── کش (تنبل، با قفل، مقاوم در برابرِ خطا) ──────────────────────────────

    private function cachedTrajectory(string $dateStr): array
    {
        $key = $this->cacheKey($dateStr);

        try {
            $cached = Cache::get($key);
            if (is_array($cached) && count($cached) === self::MINUTES_PER_DAY) {
                return $cached;
            }

            $lock = Cache::lock($key.':lock', 10);
            try {
                $lock->block(5);

                // بازبینیِ دوباره پس از گرفتنِ قفل (شاید سازندهٔ دیگری کش کرده باشد).
                $cached = Cache::get($key);
                if (is_array($cached) && count($cached) === self::MINUTES_PER_DAY) {
                    return $cached;
                }

                $trajectory = $this->buildTrajectory($dateStr);
                Cache::put($key, $trajectory, $this->cacheTtl());

                return $trajectory;
            } finally {
                try {
                    $lock->release();
                } catch (\Throwable) {
                    // قفل گرفته نشده بود یا درایور از قفل پشتیبانی نمی‌کند.
                }
            }
        } catch (\Throwable) {
            // هر خطای کش/قفل ⇒ محاسبهٔ درجا. هرگز به فراخواننده throw نمی‌کنیم.
            return $this->buildTrajectory($dateStr);
        }
    }

    private function cacheKey(string $dateStr): string
    {
        $version = config('activity_simulation.configuration_version');

        return "activity_sim:{$version}:{$this->metric()}:{$dateStr}";
    }

    private function cacheTtl(): CarbonImmutable
    {
        return CarbonImmutable::now()->addHours(25);
    }

    // ── خطِ لولهٔ ساختِ مسیر ─────────────────────────────────────────────────

    /**
     * ساختِ کاملِ روز در یک گذرِ ترتیبی. هر لایه قطعی از seedِ روز است.
     *
     * @return array<int,int>  آرایهٔ ۱۴۴۰-تایی از اعدادِ صحیح
     */
    private function buildTrajectory(string $dateStr): array
    {
        $cfg   = $this->config();
        $tol   = $this->tolerance();
        $floor = (int) $cfg['min_floor'];
        $curve = new BaselineCurve($this->anchorValues());
        $rng   = $this->rngFor($dateStr, 'main');

        // (۱) دریفتِ روزانه — یک بار در ابتدای روز، در ±[min,max].
        $drift = ($cfg['daily_drift']['min']
                + $rng->nextFloat() * ($cfg['daily_drift']['max'] - $cfg['daily_drift']['min']))
            * ($rng->nextFloat() < 0.5 ? -1.0 : 1.0);

        // (۲) موجِ آهسته — جمعِ چند سینوسِ seed‌شده با پریودِ jitterشده.
        $waves      = [];
        $periods    = $cfg['slow_wave']['periods_minutes'];
        $waveTotal  = $cfg['slow_wave']['amp_tolerance_ratio'] * $tol;
        $jitterR    = $cfg['slow_wave']['period_jitter_ratio'];
        $waveCount  = max(1, count($periods));
        foreach ($periods as $period) {
            $jitter = 1.0 + ($rng->nextFloat() * 2.0 - 1.0) * $jitterR;
            $waves[] = [
                'period' => max(1.0, $period * $jitter),
                'phase'  => $rng->nextFloat() * 2.0 * M_PI,
                'amp'    => ($waveTotal / $waveCount) * (0.5 + 0.5 * $rng->nextFloat()),
            ];
        }

        // (۳)–(۸) پارامترها
        $phi       = $cfg['noise']['ar1_phi'];
        $sigma     = $cfg['noise']['sigma_tolerance_ratio'] * $tol;
        $switchP   = $cfg['regime']['switch_prob_per_min'];
        $biasMax   = $cfg['regime']['bias_tolerance_ratio'] * $tol;
        $reversion = $cfg['regime']['reversion_per_min'];
        $pauseP    = $cfg['pause']['prob_per_min'];
        $maxHold   = max(1, (int) $cfg['pause']['max_hold_minutes']);
        $burstP    = $cfg['micro_burst']['prob_per_min'];
        $burstSize = $cfg['micro_burst']['size_tolerance_ratio'] * $tol;
        $maxDelta  = $cfg['rate_limit']['max_delta_tolerance_ratio'] * $tol;
        $rampMin   = max(1, (int) $cfg['boundary']['ramp_minutes']);

        // مرزهای مشترکِ نیمه‌شب (پیوستگیِ rollover): ابتدای امروز و ابتدای فردا.
        $startBoundary = $this->boundaryValue($dateStr, $curve, $tol, $floor);
        $endBoundary   = $this->boundaryValue($this->nextDay($dateStr), $curve, $tol, $floor);

        $out  = array_fill(0, self::MINUTES_PER_DAY, 0);
        $ar   = 0.0;               // حالتِ AR(1)
        $bias = 0.0;               // بایاسِ اعمال‌شده
        $target = 0.0;             // هدفِ رژیمِ جاری
        $hold = 0;                 // دقیقه‌های باقیماندهٔ توقفِ صاف
        $prev = (float) $startBoundary; // خروجیِ دقیقهٔ قبل (شروع = مرزِ نیمه‌شب)

        for ($t = 0; $t < self::MINUTES_PER_DAY; $t++) {
            $base = $curve->valueAt($t);

            // (۱) پایهٔ روزانه با دریفت
            $value = $base * (1.0 + $drift);

            // (۲) موجِ آهسته
            $wave = 0.0;
            foreach ($waves as $w) {
                $wave += $w['amp'] * sin(2.0 * M_PI * $t / $w['period'] + $w['phase']);
            }
            $value += $wave;

            // (۳) نویزِ AR(1) — همبسته و بازگردنده به میانگین
            $ar = $phi * $ar + $sigma * $rng->gaussian();
            $value += $ar;

            // (۴) بایاسِ رژیم — گاه‌به‌گاه سوییچ می‌کند، سپس به مرور بازمی‌گردد
            if ($rng->nextFloat() < $switchP) {
                $target = ($rng->nextFloat() * 2.0 - 1.0) * $biasMax;
            }
            $bias += ($target - $bias) * $reversion;
            $target *= (1.0 - $reversion * 0.5); // بازگشتِ تدریجی به آرامش
            $value += $bias;

            // (۵) توقفِ صاف: خروجیِ قبلی را برای چند دقیقه ثابت نگه دار
            if ($hold > 0) {
                $candidate = $prev;
                $hold--;
            } else {
                $candidate = $value;

                // (۶) میکروبرست: جهشِ کوچکِ نادر
                if ($rng->nextFloat() < $burstP) {
                    $candidate += ($rng->nextFloat() < 0.5 ? -1.0 : 1.0)
                        * $burstSize * (0.5 + 0.5 * $rng->nextFloat());
                }

                // آیا از این دقیقه یک توقف شروع شود؟
                if ($rng->nextFloat() < $pauseP) {
                    $hold = 1 + (int) floor($rng->nextFloat() * $maxHold);
                }
            }

            // پیوستگیِ انتهای روز: در چند دقیقهٔ پایانی به‌سمتِ مرزِ فردا میل کن
            $fromEnd = (self::MINUTES_PER_DAY - 1) - $t;
            if ($fromEnd < $rampMin) {
                $wt = BaselineCurve::smootherstep(($rampMin - $fromEnd) / $rampMin);
                $candidate = $candidate * (1.0 - $wt) + $endBoundary * $wt;
            }

            // (۷) کلمپِ سخت به [پایه − tolerance، پایه + tolerance]
            $lo = $base - $tol;
            $hi = $base + $tol;
            if ($candidate < $lo) {
                $candidate = $lo;
            } elseif ($candidate > $hi) {
                $candidate = $hi;
            }

            // (۸) محدودکنندهٔ نرخ: |Δ| ≤ max_delta (شروعِ روز هم از مرزِ نیمه‌شب صاف است)
            $delta = $candidate - $prev;
            if ($delta > $maxDelta) {
                $candidate = $prev + $maxDelta;
            } elseif ($delta < -$maxDelta) {
                $candidate = $prev - $maxDelta;
            }

            $prev = $candidate;

            // (۹) کوانتش به عددِ صحیح + کفِ min_floor
            $q = (int) round($candidate);
            if ($q < $floor) {
                $q = $floor;
            }
            $out[$t] = $q;
        }

        return $out;
    }

    /**
     * مقدارِ مرزِ نیمه‌شبِ یک تاریخ — قطعی و مستقل از این‌که کدام روز آن را می‌سازد،
     * پس ابتدای روزِ D و انتهای روزِ D−۱ به مقدارِ یکسانی میل می‌کنند (بدونِ پرش).
     */
    private function boundaryValue(string $dateStr, BaselineCurve $curve, float $tol, int $floor): int
    {
        $rng   = $this->rngFor($dateStr, 'boundary');
        $cfg   = $this->config();
        $drift = ($rng->nextFloat() * 2.0 - 1.0) * $cfg['boundary']['drift_ratio'];

        $base  = $curve->valueAt(0);
        $value = $base * (1.0 + $drift);

        $lo = $base - $tol;
        $hi = $base + $tol;
        if ($value < $lo) {
            $value = $lo;
        } elseif ($value > $hi) {
            $value = $hi;
        }

        $q = (int) round($value);

        return $q < $floor ? $floor : $q;
    }

    // ── seed ────────────────────────────────────────────────────────────────

    /**
     * seed = HMAC-SHA256("{date}|{metric}|{version}[|stream]", secret).
     * date به‌صورتِ Y-m-d و به وقتِ تهران؛ metric ∈ {online, watching}.
     */
    private function rngFor(string $dateStr, string $stream): DeterministicRng
    {
        $version = config('activity_simulation.configuration_version');
        $secret  = (string) config('activity_simulation.secret');

        $message = "{$dateStr}|{$this->metric()}|{$version}";
        if ($stream !== 'main') {
            $message .= "|{$stream}";
        }

        return DeterministicRng::fromHexDigest(hash_hmac('sha256', $message, $secret));
    }

    // ── config و لنگرها ──────────────────────────────────────────────────────

    /** config‌ِ ادغام‌شده: shared با override‌ِ همین متریک (deep-merge). */
    protected function config(): array
    {
        if ($this->mergedConfig === null) {
            $this->mergedConfig = array_replace_recursive(
                config('activity_simulation.engine.shared'),
                config('activity_simulation.engine.'.$this->metric(), []),
            );
        }

        return $this->mergedConfig;
    }

    /** دامنهٔ مؤثرِ این متریک = tolerance ادمین × مقیاسِ متریک. */
    protected function tolerance(): float
    {
        $adminTolerance = (int) Setting::get(
            config('activity_simulation.setting_keys.tolerance'),
            config('activity_simulation.defaults.tolerance'),
        );

        return max(1.0, $adminTolerance * $this->config()['tolerance_scale']);
    }

    /**
     * پنج مقدارِ لنگر از Setting، نگاشت‌شده به دقیقهٔ روز.
     *
     * @return array<int,int>  [دقیقهٔ روز => مقدار]
     */
    protected function anchorValues(): array
    {
        $keys     = config('activity_simulation.setting_keys')[$this->metric()];
        $defaults = config('activity_simulation.defaults')[$this->metric()];
        $times    = config('activity_simulation.anchor_times');

        $out = [];
        foreach ($times as $name => $hhmm) {
            $minute       = $this->hhmmToMinute($hhmm);
            $out[$minute] = (int) Setting::get($keys[$name], $defaults[$name]);
        }

        return $out;
    }

    private function hhmmToMinute(string $hhmm): int
    {
        [$h, $m] = array_map('intval', explode(':', $hhmm));

        return $h * 60 + $m;
    }

    private function nextDay(string $dateStr): string
    {
        return CarbonImmutable::createFromFormat('Y-m-d', $dateStr, $this->timezone())
            ->addDay()
            ->format('Y-m-d');
    }

    private function timezone(): string
    {
        return config('activity_simulation.timezone');
    }
}
