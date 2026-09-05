<?php

namespace Tests\Feature;

use App\Services\ActivitySimulation\OnlineActivityEngine;
use App\Services\ActivitySimulation\WeeklyMovieWatchingEngine;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

/**
 * تستِ طلایی (snapshot): برای یک تاریخ/config/seedِ ثابت، خروجیِ موتور باید
 * دقیقاً با fixture یکی باشد. اگر خروجی *ناخواسته* تغییر کند این تست می‌شکند.
 *
 * برای تغییرِ عمدی: خروجی را بازتولید کن و نسخهٔ config را بالا ببر:
 *   php tests/Fixtures/regenerate-golden.php
 */
class ActivitySimulationGoldenTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        // بدونِ ردیفِ Setting ⇒ موتور از پیش‌فرض‌های config استفاده می‌کند (مثلِ fixture).
        Schema::create('settings', function (Blueprint $t) {
            $t->id();
            $t->string('key')->unique();
            $t->text('value')->nullable();
            $t->timestamps();
        });

        config([
            'activity_simulation.secret'                => 'golden-fixed-secret',
            'activity_simulation.configuration_version' => 'v1',
        ]);
    }

    protected function tearDown(): void
    {
        Schema::dropIfExists('settings');
        parent::tearDown();
    }

    public function test_trajectory_matches_golden_snapshot(): void
    {
        $fixture = json_decode(file_get_contents(__DIR__.'/../Fixtures/activity_sim_golden.json'), true);

        $online   = (new OnlineActivityEngine)->computeTrajectory($fixture['meta']['date']);
        $watching = (new WeeklyMovieWatchingEngine)->computeTrajectory($fixture['meta']['date']);

        $this->assertSame($fixture['online'], $online,
            'خروجیِ online با snapshot فرق دارد؛ اگر عمدی است fixture را بازتولید کن.');
        $this->assertSame($fixture['watching'], $watching,
            'خروجیِ watching با snapshot فرق دارد؛ اگر عمدی است fixture را بازتولید کن.');
    }
}
