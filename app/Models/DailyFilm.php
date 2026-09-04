<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class DailyFilm extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'title', 'original_title', 'year', 'director', 'genre',
        'cover', 'cover_url', 'description', 'link', 'imdb_url', 'filimo_url',
        'show_date', 'is_active',
    ];

    protected $casts = [
        'show_date' => 'date',
        'is_active' => 'boolean',
    ];

    // ── Relations ──────────────────────────────────────
    public function assignments(): HasMany
    {
        return $this->hasMany(WeeklyMovieAssignment::class, 'film_id');
    }

    // ── Computed (در جدول ذخیره نمی‌شود) ────────────────
    /** تعداد دفعاتی که این فیلم به‌عنوان فیلم هفته تخصیص یافته. */
    public function getTimesUsedAttribute(): int
    {
        // اگر با withCount('assignments') لود شده باشد، از همان استفاده کن
        if (array_key_exists('assignments_count', $this->attributes)) {
            return (int) $this->attributes['assignments_count'];
        }

        return $this->assignments()->count();
    }

    /** آخرین هفته‌ای که این فیلم نمایش داده شده (week_start بیشینه) یا null. */
    public function getLastUsedAtAttribute()
    {
        if (array_key_exists('last_used_at', $this->attributes)) {
            $value = $this->attributes['last_used_at'];

            return $value ? \Illuminate\Support\Carbon::parse($value) : null;
        }

        return $this->assignments()->max('week_start')
            ? \Illuminate\Support\Carbon::parse($this->assignments()->max('week_start'))
            : null;
    }

    /**
     * آدرس نهایی کاور (آپلودی یا لینک)
     */
    public function getCoverSrcAttribute(): ?string
    {
        if ($this->cover) {
            return \Illuminate\Support\Facades\Storage::url($this->cover);
        }
        return $this->cover_url ?: null;
    }
}
