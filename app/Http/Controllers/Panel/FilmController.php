<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use App\Models\WeeklyMovieAssignment;
use App\Services\WeeklyMovie\WeeklyMovieWeekResolver;

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

        return view('panel.film.today', compact('assignment', 'film', 'week'));
    }
}
