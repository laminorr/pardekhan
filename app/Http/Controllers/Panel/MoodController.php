<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use App\Models\DailyMood;
use Illuminate\Http\Request;

class MoodController extends Controller
{
    /**
     * ثبت/به‌روزرسانی حالِ امروزِ عضو.
     * یک ردیف برای هر عضو در هر روز (به وقت تهران) — ثبت دوباره همان روز را آپدیت می‌کند.
     */
    public function store(Request $request)
    {
        $member = auth('member')->user();

        $validated = $request->validate([
            'mood' => ['required', 'integer', 'min:1', 'max:5'],
        ]);

        $today = \Carbon\Carbon::now('Asia/Tehran')->toDateString();

        // یک ردیف در روز؛ اگر امروز ثبت شده بود آپدیت، وگرنه ایجاد.
        // (whereDate روی هر دو موتور MySQL و SQLite درست کار می‌کند.)
        $existing = DailyMood::where('member_id', $member->id)
            ->whereDate('mood_date', $today)
            ->first();

        if ($existing) {
            $existing->update(['mood' => $validated['mood']]);
        } else {
            DailyMood::create([
                'member_id' => $member->id,
                'mood'      => $validated['mood'],
                'mood_date' => $today,
            ]);
        }

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json(['ok' => true]);
        }

        return redirect()->route('panel.dashboard')->with('mood_saved', true);
    }
}
