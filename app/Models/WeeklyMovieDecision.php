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
