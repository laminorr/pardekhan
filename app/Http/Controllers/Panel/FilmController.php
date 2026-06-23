<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use App\Models\DailyFilm;

class FilmController extends Controller
{
    public function today()
    {
        // فقط فیلم فعالِ امروز — آرشیو قابل دیدن نیست
        $film = DailyFilm::where('is_active', true)
            ->latest('show_date')
            ->first();

        abort_unless($film, 404, 'فیلمی برای امروز معرفی نشده است');

        return view('panel.film.today', compact('film'));
    }
}
