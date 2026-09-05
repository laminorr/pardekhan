<?php

namespace Tests\Feature;

use App\Models\Setting;
use App\Services\ActivitySimulation\BaselineCurve;
use App\Services\ActivitySimulation\DeterministicRng;
use App\Services\ActivitySimulation\OnlineActivityEngine;
use App\Services\ActivitySimulation\WeeklyMovieWatchingEngine;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

/**
 * تستِ موتورِ شبیه‌سازیِ فعالیت (فاز ۲).
 *
 * جدولِ settings این‌جا دستی ساخته می‌شود (SQLite in-memory) تا مستقل از
 * migrationهای نامرتبط باشد. موتور فقط Setting را می‌خواند؛ بقیه قطعی است.
 */
class ActivitySimulationEngineTest extends TestCase
{
    private OnlineActivityEngine $online;

    private WeeklyMovieWatchingEngine $watching;

    protected function setUp(): void
    {
        parent::setUp();

        Schema::create('settings', function (Blueprint $t) {
            $t->id();
            $t->string('key')->unique();
            $t->text('value')->nullable();
            $t->timestamps();
        });

        config([
            'activity_simulation.secret'                => 'phase2-test-secret',
            'activity_simulation.configuration_version' => 'v1',
        ]);

        $this->seedAnchors();

        $this->online   = new OnlineActivityEngine;
        $this->watching = new WeeklyMovieWatchingEngine;
    }

    protected function tearDown(): void
    {
        Schema::dropIfExists('settings');
        parent::tearDown();
    }

    private function seedAnchors(): void
    {
        $keys     = config('activity_simulation.setting_keys');
        $defaults = config('activity_simulation.defaults');

        foreach (['online', 'watching'] as $metric) {
            foreach ($defaults[$metric] as $slot => $value) {
                Setting::set($keys[$metric][$slot], $value);
            }
        }
        Setting::set($keys['tolerance'], $defaults['tolerance']);
    }

    private function onlineTolerance(): float
    {
        return (float) config('activity_simulation.defaults.tolerance'); // scale 1.0
    }

    private function onlineCurve(): BaselineCurve
    {
        $times = config('activity_simulation.anchor_times');
        $vals  = config('activity_simulation.defaults.online');
        $map   = [];
        foreach ($times as $name => $hhmm) {
            [$h, $m] = array_map('intval', explode(':', $hhmm));
            $map[$h * 60 + $m] = $vals[$name];
        }

        return new BaselineCurve($map);
    }

    // ── Smootherstep ─────────────────────────────────────────────────────────

    public function test_smootherstep_endpoints_and_monotonicity(): void
    {
        $this->assertSame(0.0, BaselineCurve::smootherstep(0.0));
        $this->assertSame(1.0, BaselineCurve::smootherstep(1.0));
        $this->assertSame(0.0, BaselineCurve::smootherstep(-3.0)); // clamped
        $this->assertSame(1.0, BaselineCurve::smootherstep(5.0));  // clamped
        $this->assertEqualsWithDelta(0.5, BaselineCurve::smootherstep(0.5), 1e-9);

        $prev = -1.0;
        for ($i = 0; $i <= 100; $i++) {
            $v = BaselineCurve::smootherstep($i / 100);
            $this->assertGreaterThanOrEqual($prev, $v, 'smootherstep باید یکنوا باشد.');
            $prev = $v;
        }
    }

    public function test_baseline_passes_through_anchors_and_is_continuous(): void
    {
        $curve = $this->onlineCurve();
        $vals  = config('activity_simulation.defaults.online');

        $this->assertEqualsWithDelta($vals['midnight'], $curve->valueAt(120), 0.001);
        $this->assertEqualsWithDelta($vals['night'], $curve->valueAt(1290), 0.001);

        // پیوستگیِ عبور از نیمه‌شب: دقیقهٔ ۱۴۳۹ و دقیقهٔ ۰ باید تقریباً برابر باشند.
        $this->assertEqualsWithDelta($curve->valueAt(1439), $curve->valueAt(0), 1.5);
    }

    // ── Determinism ──────────────────────────────────────────────────────────

    public function test_determinism_same_inputs_identical_trajectory(): void
    {
        $a = $this->online->computeTrajectory('2026-09-05');
        $b = $this->online->computeTrajectory('2026-09-05');

        $this->assertSame($a, $b, 'seedِ یکسان باید مسیرِ یکسان بدهد.');
        $this->assertCount(1440, $a);
    }

    public function test_different_day_yields_different_trajectory(): void
    {
        $a = $this->online->computeTrajectory('2026-09-05');
        $b = $this->online->computeTrajectory('2026-09-06');

        $this->assertNotSame($a, $b, 'روزِ متفاوت باید مسیرِ متفاوت بدهد.');
    }

    public function test_config_version_changes_the_stream(): void
    {
        $a = $this->online->computeTrajectory('2026-09-05');

        config(['activity_simulation.configuration_version' => 'v2']);
        $engine = new OnlineActivityEngine;
        $b      = $engine->computeTrajectory('2026-09-05');

        $this->assertNotSame($a, $b, 'تغییرِ نسخهٔ config باید جریان را عوض کند.');
    }

    // ── Clamp / rate-limit / floor ───────────────────────────────────────────

    public function test_clamp_keeps_output_within_tolerance_of_baseline(): void
    {
        $traj  = $this->online->computeTrajectory('2026-09-05');
        $curve = $this->onlineCurve();
        $tol   = $this->onlineTolerance();

        foreach ($traj as $minute => $value) {
            $base = $curve->valueAt($minute);
            // +1 برای رواداریِ کوانتشِ صحیح
            $this->assertLessThanOrEqual($tol + 1, abs($value - $base),
                "دقیقهٔ {$minute} خارج از دامنهٔ ±tolerance است.");
        }
    }

    public function test_rate_limiter_bounds_minute_deltas(): void
    {
        $traj     = $this->online->computeTrajectory('2026-09-05');
        $maxDelta = 0.25 * $this->onlineTolerance(); // 5

        for ($i = 1; $i < 1440; $i++) {
            $this->assertLessThanOrEqual($maxDelta + 1, abs($traj[$i] - $traj[$i - 1]),
                "پرشِ دقیقه‌ایِ بزرگ در {$i}.");
        }
    }

    public function test_floor_is_respected(): void
    {
        $online   = $this->online->computeTrajectory('2026-09-05');
        $watching = $this->watching->computeTrajectory('2026-09-05');

        $this->assertGreaterThanOrEqual(5, min($online), 'کفِ online باید ۵ باشد.');
        $this->assertGreaterThanOrEqual(2, min($watching), 'کفِ watching باید ۲ باشد.');
    }

    public function test_no_jump_at_day_rollover(): void
    {
        $day1     = $this->online->computeTrajectory('2026-09-05');
        $day2     = $this->online->computeTrajectory('2026-09-06');
        $maxDelta = 0.25 * $this->onlineTolerance();

        // مرزِ مشترکِ نیمه‌شب ⇒ پرشِ rollover باید در حدِ سقفِ نرخ بماند.
        $this->assertLessThanOrEqual(2 * $maxDelta + 2, abs($day1[1439] - $day2[0]),
            'در مرزِ روز نباید پرش باشد.');
    }

    // ── AR(1) noise ──────────────────────────────────────────────────────────

    public function test_ar1_noise_is_mean_reverting_and_bounded(): void
    {
        // بازسازیِ صرفِ لایهٔ AR(1) با همان پارامترها برای اثباتِ نبودِ runaway.
        $rng   = new DeterministicRng(12345, 67890, 111, 222);
        $phi   = 0.85;
        $sigma = 0.10 * $this->onlineTolerance();

        $n   = 0.0;
        $max = 0.0;
        $sum = 0.0;
        for ($t = 0; $t < 5000; $t++) {
            $n = $phi * $n + $sigma * $rng->gaussian();
            $max = max($max, abs($n));
            $sum += $n;
        }

        // انحرافِ معیارِ نظری = sigma/sqrt(1-phi^2). runaway یعنی خیلی فراتر از آن.
        $theoreticalStd = $sigma / sqrt(1 - $phi * $phi);
        $this->assertLessThan(8 * $theoreticalStd, $max, 'AR(1) نباید واگرا شود.');
        $this->assertLessThan($theoreticalStd, abs($sum / 5000), 'میانگینِ AR(1) باید نزدیکِ صفر بماند.');
    }
}
