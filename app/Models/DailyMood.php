<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DailyMood extends Model
{
    protected $fillable = ['member_id', 'mood', 'mood_date'];

    protected $casts = [
        'mood'      => 'integer',
        'mood_date' => 'date',
    ];

    /** برچسب فارسی هر حالِ روز (۵ عالی → ۱ بد). منبع مشترک برای داشبورد و پرونده. */
    public const LABELS = [
        5 => 'پر از انرژی',
        4 => 'دلم روشنه',
        3 => 'می‌گذره',
        2 => 'دلم گرفته',
        1 => 'حالم خوش نیست',
    ];

    /** برچسب فارسی برای یک مقدار حال (یا خط‌تیره اگر نامعتبر بود). */
    public static function label(?int $mood): string
    {
        return self::LABELS[$mood] ?? '—';
    }

    public function member(): BelongsTo
    {
        return $this->belongsTo(Member::class);
    }
}
