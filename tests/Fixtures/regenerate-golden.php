<?php

/**
 * بازتولیدِ snapshotِ طلاییِ موتورِ شبیه‌سازیِ فعالیت.
 *
 * فقط زمانی اجرا کن که خروجیِ موتور را *به‌صمد* تغییر داده‌ای و می‌خواهی fixture را
 * به‌روزرسانی کنی (در این حالت باید configuration_version را هم بالا ببری).
 *
 *   php tests/Fixtures/regenerate-golden.php
 *
 * تستِ طلایی (tests/Feature/ActivitySimulationGoldenTest.php) دقیقاً همین seed،
 * تاریخ و config را بازمی‌سازد و با این فایل مقایسه می‌کند.
 */

require __DIR__.'/../../vendor/autoload.php';

$app = require __DIR__.'/../../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

config([
    'activity_simulation.secret'                => 'golden-fixed-secret',
    'activity_simulation.configuration_version' => 'v1',
    'database.default'                          => 'sqlite',
    'database.connections.sqlite.database'      => ':memory:',
]);

Illuminate\Support\Facades\Schema::create('settings', function ($t) {
    $t->id();
    $t->string('key')->unique();
    $t->text('value')->nullable();
    $t->timestamps();
});

$online   = (new App\Services\ActivitySimulation\OnlineActivityEngine())->computeTrajectory('2026-09-05');
$watching = (new App\Services\ActivitySimulation\WeeklyMovieWatchingEngine())->computeTrajectory('2026-09-05');

$fixture = [
    'meta' => [
        'date'                  => '2026-09-05',
        'secret'                => 'golden-fixed-secret',
        'configuration_version' => 'v1',
        'note'                  => 'Uses config defaults (no Setting rows). Regenerate with: php tests/Fixtures/regenerate-golden.php',
    ],
    'online'   => $online,
    'watching' => $watching,
];

file_put_contents(
    __DIR__.'/activity_sim_golden.json',
    json_encode($fixture, JSON_UNESCAPED_UNICODE)."\n",
);

echo "golden regenerated.\n";
