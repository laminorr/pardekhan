<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class WeeklyMovieAssignment extends Model
{
    protected $fillable = [
        'film_id',
        'week_start',
        'week_end',
        'status',
        'assignment_source',
        'superseded_by_id',
        'created_by',
    ];

    protected $casts = [
        'week_start' => 'date',
        'week_end'   => 'date',
    ];

    // ── Relations ──────────────────────────────────────
    public function film(): BelongsTo
    {
        return $this->belongsTo(DailyFilm::class, 'film_id');
    }

    public function decisions(): HasMany
    {
        return $this->hasMany(WeeklyMovieDecision::class, 'weekly_assignment_id');
    }

    public function supersededBy(): BelongsTo
    {
        return $this->belongsTo(self::class, 'superseded_by_id');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // ── Scopes ─────────────────────────────────────────
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', 'active');
    }

    public function scopeForWeek(Builder $query, $start): Builder
    {
        return $query->whereDate('week_start', $start);
    }
}
