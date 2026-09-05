<?php

namespace Tests\Feature;

use App\Models\Setting;
use App\Services\ActivitySimulation\ActivitySimulationManager;
use App\Services\ActivitySimulation\BaselineCurve;
use App\Services\ActivitySimulation\OnlineActivityEngine;
use App\Services\ActivitySimulation\WeeklyMovieWatchingEngine;
use Carbon\CarbonImmutable;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

/**
 * تستِ یکپارچهٔ Manager + گاردِ نسبت + fallbackِ کش.
 */
class ActivitySimulationManagerTest extends TestCase
{
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
            'activity_simulation.secret'                => 'phase2-manager-secret',
            'activity_simulation.configuration_version' => 'v1',
        ]);

        $keys     = config('activity_simulation.setting_keys');
        $defaults = config('activity_simulation.defaults');
        foreach (['online', 'watching'] as $metric) {
            foreach ($defaults[$metric] as $slot => $value) {
                Setting::set($keys[$metric][$slot], $value);
            }
        }
        Setting::set($keys['tolerance'], $defaults['tolerance']);
    }

    protected function tearDown(): void
    {
        Schema::dropIfExists('settings');
        parent::tearDown();
    }

    private function manager(): ActivitySimulationManager
    {
        return new ActivitySimulationManager(new OnlineActivityEngine, new WeeklyMovieWatchingEngine);
    }

    public function test_current_returns_dto_with_watching_below_online(): void
    {
        $dto = $this->manager()->current(CarbonImmutable::parse('2026-09-05 21:00', 'Asia/Tehran'));

        $this->assertIsInt($dto->online);
        $this->assertIsInt($dto->watchingWeeklyMovie);
        $this->assertLessThan($dto->online, $dto->watchingWeeklyMovie, 'watching باید کمتر از online باشد.');
    }

    public function test_watching_stays_below_ratio_across_full_day(): void
    {
        $online   = new OnlineActivityEngine;
        $watching = new WeeklyMovieWatchingEngine;
        $manager  = $this->manager();
        $fraction = (float) config('activity_simulation.engine.ratio_guard.watching_max_fraction_of_online');

        $oTraj = $online->computeTrajectory('2026-09-05');
        $wTraj = $watching->computeTrajectory('2026-09-05');

        for ($m = 0; $m < 1440; $m++) {
            $guarded = $manager->applyRatioGuard($oTraj[$m], $wTraj[$m]);
            $this->assertLessThan($oTraj[$m], $guarded, "دقیقهٔ {$m}: watching باید < online بماند.");
            // اگر گارد فعال شده باشد، باید زیرِ کسرِ مجاز باشد (مگر به‌خاطرِ کفِ خودش).
            if ($guarded > $watching->floor()) {
                $this->assertLessThan($fraction * $oTraj[$m], $guarded);
            }
        }
    }

    public function test_ratio_guard_pulls_down_when_watching_too_high(): void
    {
        $manager = $this->manager();

        // watching مصنوعاً بالا: باید زیرِ ۰٫۵×online کشیده شود.
        $this->assertLessThan(0.5 * 100, $manager->applyRatioGuard(100, 90));
        $this->assertSame(49, $manager->applyRatioGuard(100, 90));
    }

    public function test_no_minute_exceeds_tolerance_for_both_metrics(): void
    {
        $tolerance = (float) config('activity_simulation.defaults.tolerance');
        $times     = config('activity_simulation.anchor_times');

        foreach (['online' => 1.0, 'watching' => 0.6] as $metric => $scale) {
            $vals = config("activity_simulation.defaults.$metric");
            $map  = [];
            foreach ($times as $name => $hhmm) {
                [$h, $m] = array_map('intval', explode(':', $hhmm));
                $map[$h * 60 + $m] = $vals[$name];
            }
            $curve  = new BaselineCurve($map);
            $engine = $metric === 'online' ? new OnlineActivityEngine : new WeeklyMovieWatchingEngine;
            $traj   = $engine->computeTrajectory('2026-09-05');
            $tol    = $tolerance * $scale;

            foreach ($traj as $minute => $value) {
                $this->assertLessThanOrEqual($tol + 1, abs($value - $curve->valueAt($minute)),
                    "{$metric} دقیقهٔ {$minute} خارج از دامنه.");
            }
        }
    }

    public function test_current_value_works_when_cache_fails(): void
    {
        // درایورِ کشِ خراب: هر عملیاتِ کش استثنا می‌دهد؛ نباید به فراخواننده throw شود.
        \Illuminate\Support\Facades\Cache::shouldReceive('get')->andThrow(new \RuntimeException('cache down'));
        \Illuminate\Support\Facades\Cache::shouldReceive('lock')->andThrow(new \RuntimeException('lock down'));

        $value = (new OnlineActivityEngine)->getCurrentValue(CarbonImmutable::parse('2026-09-05 12:00', 'Asia/Tehran'));

        $this->assertIsInt($value);
        $this->assertGreaterThanOrEqual(5, $value, 'حتی با کشِ خراب باید مقدارِ درست بدهد.');
    }
}
