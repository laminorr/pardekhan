<?php

namespace App\Models;

use App\Services\WeeklyMovie\WeeklyMovieWeekResolver;
use Carbon\Carbon;
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

    // ── Model events ───────────────────────────────────
    protected static function booted(): void
    {
        // نرمال‌سازیِ بازهٔ هفته پیش از هر ذخیره: هر تاریخِ ورودی به
        // شنبهٔ همان هفته (تهران) نرمال می‌شود و جمعهٔ همان هفته week_end می‌شود.
        static::saving(function (self $assignment): void {
            if ($assignment->week_start) {
                $week = (new WeeklyMovieWeekResolver)->weekFor(
                    Carbon::parse($assignment->week_start)
                );
                $assignment->week_start = $week['start']->toDateString();
                $assignment->week_end   = $week['end']->toDateString();
            }

            // مقادیر پیش‌فرضِ رشته‌ای (status/source از نوع string، نه enum)
            if (blank($assignment->status)) {
                $assignment->status = 'active';
            }
            if (blank($assignment->assignment_source)) {
                $assignment->assignment_source = 'manual';
            }
        });
    }

    /**
     * «یک فعال در هر هفته»: هر تخصیصِ فعالِ دیگری که برای همین week_start وجود
     * دارد، superseded می‌شود و superseded_by_id آن به این رکورد اشاره می‌کند.
     * تصمیم‌های ثبت‌شده روی رکوردِ قدیمی همان‌جا می‌مانند (منتقل نمی‌شوند).
     */
    public function supersedePeers(): void
    {
        if ($this->status !== 'active' || ! $this->week_start) {
            return;
        }

        static::query()
            ->whereDate('week_start', $this->week_start->toDateString())
            ->where('status', 'active')
            ->whereKeyNot($this->getKey())
            ->get()
            ->each(function (self $old): void {
                $old->status = 'superseded';
                $old->superseded_by_id = $this->getKey();
                // بدون فراخوانی دوبارهٔ eventها تا از حلقهٔ supersede جلوگیری شود
                $old->saveQuietly();
            });
    }

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
