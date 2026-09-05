<?php

namespace App\Services\WeeklyMovie;

use App\Models\DailyFilm;
use App\Models\WeeklyMovieAssignment;
use Carbon\Carbon;
use Illuminate\Contracts\Cache\LockTimeoutException;
use Illuminate\Support\Facades\Cache;

/**
 * انتخاب و تخصیصِ خودکارِ «فیلمِ هفته».
 *
 * دو مسئولیت:
 *  - selectFilmFor()   : انتخابِ یک فیلمِ مناسب برای یک هفته (بدون ذخیره).
 *  - ensureAssignedFor(): در صورتِ نبودِ تخصیصِ فعال، یک تخصیصِ خودکار می‌سازد.
 *
 * قواعد:
 *  - «دستی همیشه برنده است»: انتخابِ خودکار هرگز جایگزینِ تخصیصِ فعالِ موجود
 *    (دستی یا خودکار) نمی‌شود؛ فقط هفته‌ای را که تخصیصِ فعال ندارد پر می‌کند.
 *  - امنیتِ همزمانی: با Cache::lock تضمین می‌شود که در هر هفته حداکثر یک تخصیصِ
 *    خودکارِ فعال ساخته شود، حتی با چند فراخوانیِ همزمان.
 */
class WeeklyMovieAssigner
{
    /**
     * انتخابِ یک فیلمِ مناسب برای هفته‌ای که با $weekStart مشخص می‌شود.
     *
     * اولویت‌ها:
     *  ۱) فیلم‌هایی که هرگز استفاده نشده‌اند (assignments_count == 0) — تصادفی.
     *  ۲) کم‌استفاده‌شده‌ترین‌ها با رعایتِ فاصله: last_used اکیداً قدیمی‌تر از
     *     ($weekStart منهای repeat_gap_weeks هفته) — از میانِ lru_window تای اول
     *     (به ترتیبِ قدیمی‌ترین) یکی تصادفی.
     *  Fallback) اگر فیلترِ فاصله چیزی نگذاشت، فاصله را نادیده بگیر و از میانِ
     *     کم‌استفاده‌شده‌ترین‌ها یکی انتخاب کن تا هفته خالی نماند.
     *
     * @return DailyFilm|null  null اگر هیچ فیلمِ فعالی وجود نداشته باشد.
     */
    public function selectFilmFor(Carbon $weekStart): ?DailyFilm
    {
        // یک کوئریِ کارآمد: شمارشِ تخصیص‌ها + آخرین هفتهٔ استفاده به‌صورتِ subquery.
        $films = DailyFilm::query()
            ->where('is_active', true)
            ->select('daily_films.*')
            ->withCount('assignments')
            ->addSelect(['last_used_at' => WeeklyMovieAssignment::query()
                ->selectRaw('MAX(week_start)')
                ->whereColumn('film_id', 'daily_films.id')])
            ->get();

        if ($films->isEmpty()) {
            return null;
        }

        // اولویت ۱ — هرگز استفاده‌نشده.
        $neverUsed = $films->filter(
            fn (DailyFilm $film) => (int) $film->assignments_count === 0
        );
        if ($neverUsed->isNotEmpty()) {
            return $neverUsed->random();
        }

        // از این‌جا به بعد همهٔ فیلم‌ها حداقل یک‌بار استفاده شده‌اند.
        $gapWeeks = (int) config('weekly_movie.repeat_gap_weeks', 4);
        $window   = max(1, (int) config('weekly_movie.lru_window', 3));
        $cutoff   = $weekStart->copy()->subWeeks($gapWeeks);

        // مرتب‌سازی صعودی بر اساسِ آخرین استفاده (قدیمی‌ترین = کم‌استفاده‌شده‌ترین).
        $used = $films
            ->sortBy(fn (DailyFilm $film) => optional($film->last_used_at)->getTimestamp() ?? 0)
            ->values();

        // اولویت ۲ — واجدِ شرایط با رعایتِ فاصله.
        $eligible = $used->filter(
            fn (DailyFilm $film) => $film->last_used_at && $film->last_used_at->lt($cutoff)
        )->values();

        // Fallback: اگر همه در بازهٔ فاصله بودند، از کلِ کم‌استفاده‌شده‌ترین‌ها.
        $pool = $eligible->isNotEmpty() ? $eligible : $used;

        return $pool->take($window)->random();
    }

    /**
     * در صورتِ نبودِ تخصیصِ فعال برای این هفته، یک تخصیصِ خودکار می‌سازد.
     * Idempotent و امن در برابرِ همزمانی: در هر هفته حداکثر یک تخصیصِ فعال.
     */
    public function ensureAssignedFor(Carbon $weekStart): ?WeeklyMovieAssignment
    {
        // نرمال‌سازی به شنبهٔ کانونیِ همان هفته (برای کلیدِ قفل و بررسیِ موجودبودن).
        $start = (new WeeklyMovieWeekResolver)->weekFor($weekStart)['start'];

        $lock = Cache::lock('wm-assign-'.$start->toDateString(), 10);

        try {
            // تا ۵ ثانیه منتظرِ قفل بمان تا فراخوانیِ همزمان تخصیصِ دوگانه نسازد.
            return $lock->block(5, fn () => $this->assignWithinLock($start));
        } catch (LockTimeoutException $e) {
            // قفل به‌دست نیامد؛ فراخوانیِ همزمانِ دیگری در حالِ تخصیص است.
            // فقط تخصیصِ فعالِ موجود را برگردان (یا null).
            return $this->existingActiveFor($start);
        }
    }

    /** منطقِ تخصیص که همیشه درونِ قفل اجرا می‌شود. */
    protected function assignWithinLock(Carbon $start): ?WeeklyMovieAssignment
    {
        // اگر تخصیصِ فعالی هست (دستی یا خودکار) → دست‌نخورده برگردان (اولویتِ دستی).
        if ($existing = $this->existingActiveFor($start)) {
            return $existing;
        }

        $film = $this->selectFilmFor($start);
        if (! $film) {
            return null;
        }

        // assignment_source باید صریحاً 'automatic' باشد (مدل پیش‌فرضِ خالی را 'manual'
        // می‌گذارد). هوکِ saving مقدارِ week_end را پر می‌کند.
        return WeeklyMovieAssignment::create([
            'film_id'           => $film->id,
            'week_start'        => $start->toDateString(),
            'status'            => 'active',
            'assignment_source' => 'automatic',
            'created_by'        => null,
        ]);
    }

    /** تخصیصِ فعالِ همین هفته (یا null). */
    protected function existingActiveFor(Carbon $start): ?WeeklyMovieAssignment
    {
        return WeeklyMovieAssignment::query()
            ->active()
            ->forWeek($start->toDateString())
            ->first();
    }
}
