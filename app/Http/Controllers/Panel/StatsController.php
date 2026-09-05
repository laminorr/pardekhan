<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use App\Services\ActivitySimulation\ActivitySimulationManager;
use Throwable;

class StatsController extends Controller
{
    /**
     * آمارِ زندهٔ لحظهٔ جاری برای داشبوردِ عضو: فقط دو عددِ «آنلاین» و «در حال دیدنِ فیلم هفته».
     * منبعِ حقیقت، موتورِ شبیه‌سازِ سمتِ سرور است (نه Math.random سمتِ کلاینت).
     *
     * در صورتِ هر خطای داخلی، ['ok'=>false] با کدِ ۲۰۰ برمی‌گردد تا ویجت آخرین
     * مقدارش را نگه دارد و هرگز با ۵۰۰ خراب نشود.
     */
    public function live()
    {
        try {
            $dto = app(ActivitySimulationManager::class)->current();

            return response()->json([
                'ok'       => true,
                'online'   => $dto->online,
                'watching' => $dto->watchingWeeklyMovie,
            ]);
        } catch (Throwable $e) {
            return response()->json(['ok' => false]);
        }
    }
}
