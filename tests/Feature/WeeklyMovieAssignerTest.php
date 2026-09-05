<?php

namespace Tests\Feature;

use App\Http\Controllers\Panel\FilmController;
use App\Models\DailyFilm;
use App\Models\WeeklyMovieAssignment;
use App\Services\WeeklyMovie\WeeklyMovieAssigner;
use App\Services\WeeklyMovie\WeeklyMovieWeekResolver;
use Carbon\Carbon;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Tests\TestCase;

/**
 * تستِ راستی‌آزماییِ فاز ۴ (انتخابِ خودکارِ فیلمِ هفته).
 *
 * برای مستقل‌بودن از migrationهای نامرتبط، جدول‌های موردنیاز این‌جا دستی ساخته
 * می‌شوند (SQLite in-memory). فقط منطقِ Assigner / Command / Controller آزموده می‌شود.
 */
class WeeklyMovieAssignerTest extends TestCase
{
    private Carbon $week; // شنبهٔ هفتهٔ جاری (کانونی)

    protected function setUp(): void
    {
        parent::setUp();

        Schema::create('daily_films', function (Blueprint $t) {
            $t->id();
            $t->string('title');
            $t->boolean('is_active')->default(true);
            $t->timestamps();
            $t->softDeletes();
        });

        Schema::create('weekly_movie_assignments', function (Blueprint $t) {
            $t->id();
            $t->unsignedBigInteger('film_id');
            $t->date('week_start');
            $t->date('week_end');
            $t->string('status')->default('active');
            $t->string('assignment_source')->default('manual');
            $t->unsignedBigInteger('superseded_by_id')->nullable();
            $t->unsignedBigInteger('created_by')->nullable();
            $t->timestamps();
        });

        // today() → voteResult() این جدول را می‌پرسد؛ برای نبودِ کرش لازم است.
        Schema::create('weekly_movie_user_decisions', function (Blueprint $t) {
            $t->id();
            $t->unsignedBigInteger('member_id');
            $t->unsignedBigInteger('weekly_assignment_id');
            $t->string('decision');
            $t->timestamps();
        });

        config([
            'weekly_movie.auto_enabled'     => true,
            'weekly_movie.repeat_gap_weeks' => 4,
            'weekly_movie.lru_window'       => 3,
        ]);

        // شنبهٔ کانونیِ هفتهٔ جاری از خودِ resolver.
        $this->week = (new WeeklyMovieWeekResolver)->currentWeek()['start'];
    }

    protected function tearDown(): void
    {
        Schema::dropIfExists('weekly_movie_user_decisions');
        Schema::dropIfExists('weekly_movie_assignments');
        Schema::dropIfExists('daily_films');
        parent::tearDown();
    }

    private function film(string $title, bool $active = true): DailyFilm
    {
        return DailyFilm::create(['title' => $title, 'is_active' => $active]);
    }

    /** یک تخصیصِ استفاده‌شده در «$weeksAgo هفته قبل» می‌سازد. */
    private function usedWeeksAgo(DailyFilm $film, int $weeksAgo, string $source = 'automatic'): void
    {
        WeeklyMovieAssignment::create([
            'film_id'           => $film->id,
            'week_start'        => $this->week->copy()->subWeeks($weeksAgo)->toDateString(),
            'status'            => 'superseded', // گذشته؛ فعالِ هفتهٔ جاری نیست
            'assignment_source' => $source,
        ]);
    }

    private function assigner(): WeeklyMovieAssigner
    {
        return app(WeeklyMovieAssigner::class);
    }

    public function test_never_used_priority_picks_a_never_used_film(): void
    {
        $used  = $this->film('استفاده‌شده');
        $this->usedWeeksAgo($used, 10);           // خیلی وقت پیش، ولی استفاده‌شده
        $fresh = $this->film('هرگز استفاده‌نشده'); // assignments_count == 0

        $pick = $this->assigner()->selectFilmFor($this->week);

        $this->assertNotNull($pick);
        $this->assertSame($fresh->id, $pick->id, 'باید فیلمِ هرگز-استفاده‌نشده انتخاب شود.');
    }

    public function test_gap_excludes_recent_and_allows_old_enough(): void
    {
        $recent = $this->film('۲ هفته قبل');
        $old    = $this->film('۵ هفته قبل');
        $this->usedWeeksAgo($recent, 2); // درونِ فاصلهٔ ۴ → واجدِ شرایط نیست
        $this->usedWeeksAgo($old, 5);    // بیرونِ فاصلهٔ ۴ → واجدِ شرایط

        // چند بار اجرا تا تصادفی‌بودن، نتیجهٔ نادرست را پنهان نکند.
        for ($i = 0; $i < 15; $i++) {
            $pick = $this->assigner()->selectFilmFor($this->week);
            $this->assertSame($old->id, $pick->id, 'فیلمِ ۲-هفته-قبل نباید انتخاب شود.');
        }
    }

    public function test_fallback_still_picks_when_everything_is_within_gap(): void
    {
        $a = $this->film('الف');
        $b = $this->film('ب');
        $this->usedWeeksAgo($a, 1);
        $this->usedWeeksAgo($b, 1); // هر دو درونِ فاصله → فیلترِ فاصله خالی

        $pick = $this->assigner()->selectFilmFor($this->week);

        $this->assertNotNull($pick, 'حتی وقتی همه درونِ فاصله‌اند، هفته نباید خالی بماند.');
        $this->assertContains($pick->id, [$a->id, $b->id]);
    }

    public function test_ensure_and_command_are_idempotent(): void
    {
        $this->film('تنها فیلم');

        $first  = $this->assigner()->ensureAssignedFor($this->week);
        $second = $this->assigner()->ensureAssignedFor($this->week);

        $this->assertNotNull($first);
        $this->assertSame($first->id, $second->id, 'فراخوانیِ دوباره نباید تخصیصِ جدید بسازد.');

        // اجرای دستور دو بار هم نباید تخصیصِ فعالِ دوم بسازد.
        $this->artisan('pardekhan:assign-weekly-movie')->assertSuccessful();
        $this->artisan('pardekhan:assign-weekly-movie')->assertSuccessful();

        $activeCount = WeeklyMovieAssignment::query()
            ->where('status', 'active')
            ->whereDate('week_start', $this->week->toDateString())
            ->count();

        $this->assertSame(1, $activeCount, 'باید دقیقاً یک تخصیصِ فعال در هفته باشد.');
        $this->assertSame('automatic', WeeklyMovieAssignment::find($first->id)->assignment_source);
    }

    public function test_manual_active_assignment_is_never_replaced(): void
    {
        $manualFilm = $this->film('انتخابِ دستی');
        $other      = $this->film('کاندیدای خودکار');

        // تخصیصِ دستیِ فعالِ همین هفته.
        $manual = WeeklyMovieAssignment::create([
            'film_id'           => $manualFilm->id,
            'week_start'        => $this->week->toDateString(),
            'status'            => 'active',
            'assignment_source' => 'manual',
        ]);

        $result = $this->assigner()->ensureAssignedFor($this->week);
        $this->artisan('pardekhan:assign-weekly-movie')->assertSuccessful();

        $this->assertSame($manual->id, $result->id, 'تخصیصِ دستی باید دست‌نخورده برگردد.');

        $active = WeeklyMovieAssignment::query()
            ->where('status', 'active')
            ->whereDate('week_start', $this->week->toDateString())
            ->get();

        $this->assertCount(1, $active);
        $this->assertSame('manual', $active->first()->assignment_source);
        $this->assertSame($manualFilm->id, $active->first()->film_id);
    }

    public function test_lazy_fallback_creates_assignment_on_first_visit(): void
    {
        $this->film('فیلمِ در دسترس');

        // هیچ تخصیصی وجود ندارد (کران اجرا نشده).
        $this->assertSame(0, WeeklyMovieAssignment::count());

        // بازدید از صفحهٔ عمومی → باید خودکار ساخته شود.
        $view = app(FilmController::class)->today();

        $active = WeeklyMovieAssignment::query()
            ->where('status', 'active')
            ->whereDate('week_start', $this->week->toDateString())
            ->first();

        $this->assertNotNull($active, 'fallbackِ تنبل باید در اولین بازدید تخصیص بسازد.');
        $this->assertSame('automatic', $active->assignment_source);
        $this->assertNotNull($view->getData()['assignment'] ?? null);
    }

    public function test_no_active_films_returns_null_and_empty_state_does_not_crash(): void
    {
        $this->film('غیرفعال', active: false); // فقط فیلمِ غیرفعال

        $this->assertNull($this->assigner()->ensureAssignedFor($this->week));

        // today() نباید کرش کند و باید حالتِ خالی (assignment=null) بدهد.
        $view = app(FilmController::class)->today();

        $this->assertNull($view->getData()['assignment']);
        $this->assertSame(0, WeeklyMovieAssignment::count());
    }
}
