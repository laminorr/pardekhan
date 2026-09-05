<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WeeklyMovieDecision extends Model
{
    protected $table = 'weekly_movie_user_decisions';

    protected $fillable = [
        'member_id',
        'weekly_assignment_id',
        'decision',
    ];

    /** برچسب فارسی هر تصمیم. منبع مشترک برای پنل و پرونده. */
    public const LABELS = [
        'will_watch' => 'می‌بینم',
        'will_not_watch' => 'نمی‌بینم',
    ];

    /** برچسب فارسی برای یک مقدار تصمیم (یا خط‌تیره اگر نامعتبر بود). */
    public static function label(?string $v): string
    {
        return self::LABELS[$v] ?? '—';
    }

    // ── Relations ──────────────────────────────────────
    public function member(): BelongsTo
    {
        return $this->belongsTo(Member::class);
    }

    public function assignment(): BelongsTo
    {
        return $this->belongsTo(WeeklyMovieAssignment::class, 'weekly_assignment_id');
    }
}
