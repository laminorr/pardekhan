<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use App\Models\WeeklyMovieAssignment;
use App\Models\WeeklyMovieDecision;
use App\Services\WeeklyMovie\WeeklyMovieWeekResolver;
use Illuminate\Http\Request;

class FilmController extends Controller
{
    public function today()
    {
        // فقط «فیلمِ هفتهٔ جاری» — تخصیصِ فعالِ همین هفته (شنبه→جمعه، تهران).
        $week = (new WeeklyMovieWeekResolver)->currentWeek();

        $assignment = WeeklyMovieAssignment::active()
            ->forWeek($week['start']->toDateString())
            ->with('film')
            ->first();

        // بدون تخصیص → 404 نده؛ همان ویو با حالتِ خالیِ دوستانه رندر شود.
        $film = $assignment?->film;

        // نتیجهٔ رأی‌گیری فقط وقتی تخصیصی هست محاسبه می‌شود.
        $result = null;
        if ($assignment) {
            $member = auth('member')->user();
            $result = $this->voteResult($assignment, $member?->id);
        }

        return view('panel.film.today', array_merge(
            compact('assignment', 'film', 'week'),
            $result ?? [
                'myDecision'   => null,
                'willWatch'    => 0,
                'willNot'      => 0,
                'total'        => 0,
                'threshold'    => config('weekly_movie.social_proof_threshold', 5),
                'reveal'       => false,
                'willWatchPct' => 0,
            ],
        ));
    }

    /**
     * ثبت/تغییرِ رأیِ عضو روی فیلمِ هفتهٔ جاری.
     * تخصیص همیشه سمتِ سرور از روی هفتهٔ جاری resolve می‌شود؛ هیچ id از کلاینت
     * پذیرفته نمی‌شود تا رأی روی تخصیصِ هفتهٔ دیگر/جایگزین‌شده ممکن نباشد.
     */
    public function vote(Request $request)
    {
        $member = auth('member')->user();

        $validated = $request->validate([
            'decision' => ['required', 'in:will_watch,will_not_watch'],
        ]);

        // تخصیصِ فعالِ هفتهٔ جاری — سمتِ سرور.
        $week = (new WeeklyMovieWeekResolver)->currentWeek();
        $assignment = WeeklyMovieAssignment::active()
            ->forWeek($week['start']->toDateString())
            ->first();

        if (! $assignment) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['ok' => false, 'error' => 'no_active'], 422);
            }

            return redirect()->route('panel.film.today')->with('vote_error', true);
        }

        // یکتایی روی (member_id, weekly_assignment_id) → تغییرِ رأی مجاز است.
        WeeklyMovieDecision::updateOrCreate(
            ['member_id' => $member->id, 'weekly_assignment_id' => $assignment->id],
            ['decision' => $validated['decision']],
        );

        $result = $this->voteResult($assignment, $member->id);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'ok'          => true,
                'my_decision' => $result['myDecision'],
                'reveal'      => $result['reveal'],
                'will_watch'  => $result['willWatch'],
                'will_not'    => $result['willNot'],
                'total'       => $result['total'],
                'threshold'   => $result['threshold'],
                'pct'         => $result['willWatchPct'],
            ]);
        }

        return redirect()->route('panel.film.today')->with('vote_saved', true);
    }

    /**
     * محاسبهٔ payloadِ نتیجهٔ رأی‌گیری برای یک تخصیص و یک عضو.
     * گیتِ اثبات اجتماعی: نتیجهٔ جمعی فقط وقتی آشکار می‌شود که عضو خودش رأی
     * داده باشد و مجموع رأی‌ها به آستانه رسیده باشد.
     *
     * @return array{myDecision:?string,willWatch:int,willNot:int,total:int,threshold:int,reveal:bool,willWatchPct:int}
     */
    private function voteResult(WeeklyMovieAssignment $assignment, ?int $memberId): array
    {
        $myDecision = $memberId
            ? WeeklyMovieDecision::query()
                ->where('weekly_assignment_id', $assignment->id)
                ->where('member_id', $memberId)
                ->value('decision')
            : null;

        $willWatch = WeeklyMovieDecision::query()
            ->where('weekly_assignment_id', $assignment->id)
            ->where('decision', 'will_watch')
            ->count();

        $willNot = WeeklyMovieDecision::query()
            ->where('weekly_assignment_id', $assignment->id)
            ->where('decision', 'will_not_watch')
            ->count();

        $total     = $willWatch + $willNot;
        $threshold = (int) config('weekly_movie.social_proof_threshold', 5);

        // آشکارسازی فقط پس از رأیِ خودِ عضو و رسیدن به آستانه.
        $reveal = ($myDecision !== null) && ($total >= $threshold);

        $willWatchPct = $total ? (int) round($willWatch / $total * 100) : 0;

        return compact(
            'myDecision',
            'willWatch',
            'willNot',
            'total',
            'threshold',
            'reveal',
            'willWatchPct',
        );
    }
}
