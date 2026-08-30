<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Event extends Model
{
    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'starts_at'          => 'datetime',
            'over_capacity_flag' => 'boolean',
        ];
    }

    public function venue(): BelongsTo
    {
        return $this->belongsTo(Venue::class);
    }

    // کتاب مرتبط با این دورهمی (اختیاری)
    public function book(): BelongsTo
    {
        return $this->belongsTo(Book::class);
    }

    public function layers(): BelongsToMany
    {
        return $this->belongsToMany(Layer::class, 'event_layer')
            ->withPivot('discount_percent', 'price_override');
    }

    public function invitedMembers(): BelongsToMany
    {
        return $this->belongsToMany(Member::class, 'event_invitations')
            ->withTimestamps();
    }

    public function registrations(): HasMany
    {
        return $this->hasMany(Registration::class);
    }

    public function waitingList(): HasMany
    {
        return $this->hasMany(WaitingList::class);
    }

    // ── Helpers ────────────────────────────────────────
    // آیا این دورهمی شرح صوتی (قسمت پادکست) دارد؟
    public function hasVoice(): bool
    {
        return ! empty($this->voice_url);
    }

    public function confirmedCount(): int
    {
        return $this->registrations()
            ->whereIn('attendance_status', ['registered', 'attended'])
            ->count();
    }

    public function remainingCapacity(): int
    {
        return max(0, $this->capacity - $this->confirmedCount());
    }

    public function isFull(): bool
    {
        return $this->remainingCapacity() <= 0;
    }

    public function registrationClosesAt(): \Carbon\Carbon
    {
        return $this->starts_at->copy()->subHours(12);
    }

    public function isRegistrationOpen(): bool
    {
        return in_array($this->status, ['active'])
            && now()->lt($this->registrationClosesAt())
            && ! $this->isFull();
    }

    // تخفیف یک لایه برای این رویداد
    public function discountForLayer(?Layer $layer): int
    {
        if (! $layer) return 0;

        $pivot = $this->layers()->where('layers.id', $layer->id)->first()?->pivot;

        if (! $pivot) {
            // لایه مجاز نیست ولی شاید دعوت شده — تخفیف پایه لایه
            return $layer->discount_percent;
        }

        // اگه override تعریف شده استفاده کن، وگرنه تخفیف پایه لایه
        return $pivot->discount_percent ?? $layer->discount_percent;
    }

    /**
     * قیمت نهایی یک لایه برای این رویداد.
     * اولویت: قیمت مطلق (price_override، شامل ۰ = رایگان) > تخفیف درصدی > قیمت پایه
     */
    public function priceForLayer(?Layer $layer): int
    {
        if (! $layer) {
            return (int) $this->base_price;
        }

        $pivot = $this->layers()->where('layers.id', $layer->id)->first()?->pivot;

        // قیمت مطلق تعریف شده (حتی اگر ۰ باشد = رایگان) → مقدم بر همه‌چیز
        if ($pivot && $pivot->price_override !== null) {
            return (int) $pivot->price_override;
        }

        // در غیر این صورت: تخفیف درصدی روی قیمت پایه (رفتار قبلی)
        $discount = $this->discountForLayer($layer);
        return (int) round($this->base_price * (100 - $discount) / 100);
    }

    public function priceForMember(Member $member): int
    {
        return $this->priceForLayer($member->layer);
    }

    // رایگان‌بودن این رویداد برای عضو؟
    public function isFreeForMember(Member $member): bool
    {
        return $this->priceForMember($member) === 0;
    }

    // آیا برای عضو تخفیف/قیمت ویژه دارد (برای نمایش خط‌خورده)؟
    public function hasDiscountForMember(Member $member): bool
    {
        return $this->priceForMember($member) < (int) $this->base_price;
    }

    /**
     * دورهمی‌های قابل‌دیدن برای یک عضو:
     * عمومی (بدون لایه و دعوت) یا لایهٔ مجاز یا دعوت اختصاصی
     */
    public function scopeVisibleTo($query, $member)
    {
        $layerId = $member->layer_id;
        return $query->where(function ($q) use ($member, $layerId) {
            $q->where(function ($sub) {
                $sub->whereDoesntHave('layers')->whereDoesntHave('invitedMembers');
            });
            if ($layerId) {
                $q->orWhereHas('layers', fn ($qq) => $qq->where('layers.id', $layerId));
            }
            $q->orWhereHas('invitedMembers', fn ($qq) => $qq->where('members.id', $member->id));
        });
    }

}
