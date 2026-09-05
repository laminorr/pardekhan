<?php

namespace Tests\Feature;

use App\Http\Controllers\Admin\MemberDossierController;
use App\Models\DailyFilm;
use App\Models\Member;
use App\Models\WeeklyMovieAssignment;
use App\Models\WeeklyMovieDecision;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use OpenSpout\Reader\XLSX\Reader;
use OpenSpout\Writer\XLSX\Writer;
use ReflectionMethod;
use Tests\TestCase;

/**
 * تستِ راستی‌آزماییِ فاز ۵ (صفحهٔ «تصمیم فیلم هفته» در پروندهٔ اکسل).
 *
 * برای مستقل‌بودن از migrationهای نامرتبط، جدول‌های موردنیاز این‌جا دستی ساخته
 * می‌شوند (SQLite in-memory). فقط منطقِ ساختِ صفحه و برچسب‌ها آزموده می‌شود.
 */
class WeeklyMovieDossierTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Schema::create('members', function (Blueprint $t) {
            $t->id();
            $t->string('first_name')->nullable();
            $t->string('last_name')->nullable();
            $t->timestamps();
        });

        Schema::create('daily_films', function (Blueprint $t) {
            $t->id();
            $t->string('title')->nullable();
            $t->string('original_title')->nullable();
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

        Schema::create('weekly_movie_user_decisions', function (Blueprint $t) {
            $t->id();
            $t->unsignedBigInteger('member_id');
            $t->unsignedBigInteger('weekly_assignment_id');
            $t->string('decision');
            $t->timestamps();
        });
    }

    protected function tearDown(): void
    {
        Schema::dropIfExists('weekly_movie_user_decisions');
        Schema::dropIfExists('weekly_movie_assignments');
        Schema::dropIfExists('daily_films');
        Schema::dropIfExists('members');
        parent::tearDown();
    }

    /** صفحهٔ اکسل را تولید و همهٔ صفحه‌ها را به‌صورت [name => rows[]] برمی‌گرداند. */
    private function buildAndReadSheets(Member $member): array
    {
        $path = tempnam(sys_get_temp_dir(), 'dossier_test_').'.xlsx';

        $writer = new Writer;
        $writer->openToFile($path);
        // صفحهٔ اول از قبل باز است؛ چون فقط متد موردنظر را صدا می‌زنیم، نام‌ش می‌دهیم.
        $writer->getCurrentSheet()->setName('placeholder');

        $method = new ReflectionMethod(MemberDossierController::class, 'sheetWeeklyMovieDecisions');
        $method->setAccessible(true);
        $method->invoke(new MemberDossierController, $writer, $member);

        $writer->close();

        $reader = new Reader;
        $reader->open($path);

        $sheets = [];
        foreach ($reader->getSheetIterator() as $sheet) {
            $rows = [];
            foreach ($sheet->getRowIterator() as $row) {
                $rows[] = array_map(
                    fn ($cell) => (string) $cell->getValue(),
                    $row->getCells()
                );
            }
            $sheets[$sheet->getName()] = $rows;
        }
        $reader->close();

        @unlink($path);

        return $sheets;
    }

    public function test_label_maps_decision_values_correctly(): void
    {
        $this->assertSame('می‌بینم', WeeklyMovieDecision::label('will_watch'));
        $this->assertSame('نمی‌بینم', WeeklyMovieDecision::label('will_not_watch'));
        $this->assertSame('—', WeeklyMovieDecision::label(null));
        $this->assertSame('—', WeeklyMovieDecision::label('نامعتبر'));
    }

    public function test_sheet_reflects_decisions_and_summary_counts(): void
    {
        $member = Member::create(['first_name' => 'زهرا', 'last_name' => 'الف']);

        $filmA = DailyFilm::create(['title' => 'فیلمِ می‌بینم']);
        $filmB = DailyFilm::create(['title' => 'فیلمِ نمی‌بینم']);

        $weekA = WeeklyMovieAssignment::create(['film_id' => $filmA->id, 'week_start' => '2026-08-15']);
        $weekB = WeeklyMovieAssignment::create(['film_id' => $filmB->id, 'week_start' => '2026-08-22']);

        WeeklyMovieDecision::create([
            'member_id' => $member->id, 'weekly_assignment_id' => $weekA->id, 'decision' => 'will_watch',
        ]);
        WeeklyMovieDecision::create([
            'member_id' => $member->id, 'weekly_assignment_id' => $weekB->id, 'decision' => 'will_not_watch',
        ]);

        $sheets = $this->buildAndReadSheets($member);

        $this->assertArrayHasKey('تصمیم فیلم هفته', $sheets, 'صفحهٔ «تصمیم فیلم هفته» باید تولید شود.');

        $rows = $sheets['تصمیم فیلم هفته'];
        $flat = array_map(fn ($r) => implode('|', $r), $rows);
        $joined = implode("\n", $flat);

        // خلاصه: هر تصمیم دقیقاً یک بار → ۱ و ۱ (ارقام فارسی).
        $this->assertContains('می‌بینم|'.fa(1), $flat, 'شمارشِ خلاصهٔ «می‌بینم» باید ۱ باشد.');
        $this->assertContains('نمی‌بینم|'.fa(1), $flat, 'شمارشِ خلاصهٔ «نمی‌بینم» باید ۱ باشد.');

        // ریز تصمیم‌ها: عنوانِ فیلم و برچسبِ تصمیم باید حاضر باشند.
        $this->assertStringContainsString('فیلمِ می‌بینم', $joined);
        $this->assertStringContainsString('فیلمِ نمی‌بینم', $joined);
        $this->assertStringContainsString('تا', $joined, 'بازهٔ هفته باید با «تا» نمایش داده شود.');
    }

    public function test_sheet_shows_empty_state_when_no_decisions(): void
    {
        $member = Member::create(['first_name' => 'بدونِ', 'last_name' => 'تصمیم']);

        $sheets = $this->buildAndReadSheets($member);

        $this->assertArrayHasKey('تصمیم فیلم هفته', $sheets);

        $joined = implode("\n", array_map(
            fn ($r) => implode('|', $r),
            $sheets['تصمیم فیلم هفته']
        ));

        // خلاصه هنوز صفر است و حالتِ خالی نمایش داده می‌شود.
        $this->assertStringContainsString('تصمیمی ثبت نشده است', $joined);
        $this->assertStringContainsString('می‌بینم|'.fa(0), $joined);
    }
}
