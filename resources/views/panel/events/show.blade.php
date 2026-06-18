@extends('panel.layouts.app')
@section('title', $event->title)

@section('content')
<div class="panel-card" style="padding:0;overflow:hidden;">
    @if($event->image)
        <img src="{{ Storage::url($event->image) }}" style="width:100%;height:200px;object-fit:cover;">
    @endif

    <div style="padding:1.5rem;">
        <h2 style="margin-bottom:0.3rem;">{{ $event->title }}</h2>
        @if($event->subtitle)
            <p style="color:#888;font-size:0.9rem;margin-bottom:1rem;">{{ $event->subtitle }}</p>
        @endif

        {{-- اطلاعات --}}
        <div style="display:flex;flex-direction:column;gap:0.6rem;margin:1.2rem 0;padding:1rem;background:#111;border-radius:10px;font-size:0.9rem;">
            <div style="display:flex;justify-content:space-between;">
                <span style="color:#888;">📅 تاریخ</span>
                <span>{{ \Morilog\Jalali\Jalalian::fromDateTime($event->starts_at)->format('Y/m/d') }}</span>
            </div>
            <div style="display:flex;justify-content:space-between;">
                <span style="color:#888;">🕐 ساعت</span>
                <span>{{ $event->starts_at->format('H:i') }}</span>
            </div>
            @if($event->venue)
            <div style="display:flex;justify-content:space-between;">
                <span style="color:#888;">📍 مکان</span>
                <span>{{ $event->venue->name }}</span>
            </div>
            <div style="display:flex;justify-content:space-between;">
                <span style="color:#888;">آدرس</span>
                <span style="text-align:left;max-width:60%;">{{ $event->venue->address }}</span>
            </div>
            @if($event->venue->map_link)
            <a href="{{ $event->venue->map_link }}" target="_blank" style="color:#f59e0b;text-decoration:none;font-size:0.85rem;">مشاهده روی نقشه ←</a>
            @endif
            @endif
        </div>

        {{-- توضیحات --}}
        @if($event->description)
        <div style="color:#ccc;line-height:1.8;font-size:0.9rem;margin-bottom:1.2rem;">
            {!! nl2br(e($event->description)) !!}
        </div>
        @endif

        {{-- قیمت --}}
        <div style="background:#111;border-radius:10px;padding:1rem;margin-bottom:1.2rem;">
            @if($discount > 0)
                <div style="display:flex;justify-content:space-between;color:#888;font-size:0.85rem;">
                    <span>قیمت پایه</span>
                    <span style="text-decoration:line-through;">{{ number_format($event->base_price) }} تومان</span>
                </div>
                <div style="display:flex;justify-content:space-between;color:#22c55e;font-size:0.85rem;margin-top:0.3rem;">
                    <span>تخفیف لایه شما</span>
                    <span>{{ $discount }}%</span>
                </div>
                <hr style="border-color:#2a2a2a;margin:0.6rem 0;">
            @endif
            <div style="display:flex;justify-content:space-between;align-items:center;">
                <span style="color:#fff;font-weight:bold;">قیمت نهایی</span>
                <span style="color:#f59e0b;font-size:1.3rem;font-weight:bold;">{{ number_format($price) }} تومان</span>
            </div>
        </div>

        {{-- عکس شرکت‌کنندگان --}}
        @if($attendeeAvatars->isNotEmpty())
        <div style="margin-bottom:1.2rem;">
            <div style="color:#888;font-size:0.8rem;margin-bottom:0.5rem;">شرکت‌کنندگان</div>
            <div style="display:flex;flex-wrap:wrap;gap:-8px;">
                @foreach($attendeeAvatars as $avatar)
                    <div style="width:36px;height:36px;border-radius:50%;overflow:hidden;border:2px solid #1a1a1a;margin-left:-8px;">
                        <img src="{{ Storage::url($avatar->avatar) }}" style="width:100%;height:100%;object-fit:cover;">
                    </div>
                @endforeach
            </div>
        </div>
        @endif

        {{-- ظرفیت --}}
        @php $remaining = $event->remainingCapacity(); @endphp
        @if($remaining <= 3 && $remaining > 0)
            <div style="text-align:center;color:#f59e0b;font-size:0.85rem;margin-bottom:1rem;">فقط {{ $remaining }} جای خالی باقی مانده</div>
        @endif

        {{-- دکمه ثبت‌نام / وضعیت --}}
        @if($isRegistered)
            <div style="text-align:center;padding:0.85rem;background:#052e16;border:1px solid #166534;border-radius:10px;color:#22c55e;">
                ✓ شما در این دورهمی ثبت‌نام کرده‌اید
            </div>
        @elseif($isWaiting)
            <div style="text-align:center;padding:0.85rem;background:#1a1a1a;border:1px solid #333;border-radius:10px;color:#aaa;">
                شما در لیست انتظار این دورهمی هستید
            </div>
        @elseif($event->status === 'full' || $remaining <= 0)
            <div style="text-align:center;padding:0.85rem;background:#1a1a1a;border:1px solid #333;border-radius:10px;color:#aaa;margin-bottom:0.75rem;">
                ظرفیت تکمیل شده است
            </div>
            <form method="POST" action="{{ route('panel.events.waitlist', $event) }}">
                @csrf
                <button type="submit" class="btn btn-secondary" style="width:100%;">عضویت در لیست انتظار</button>
            </form>
            <p style="color:#666;font-size:0.75rem;text-align:center;margin-top:0.5rem;">در صورت خالی شدن ظرفیت یا تاریخ جدید به شما اطلاع داده می‌شود</p>
        @elseif(!$event->isRegistrationOpen())
            <div style="text-align:center;padding:0.85rem;background:#1a1a1a;border:1px solid #333;border-radius:10px;color:#aaa;">
                ثبت‌نام این دورهمی بسته شده است
            </div>
        @else
            <a href="#" style="display:block;text-align:center;padding:0.85rem;background:#f59e0b;color:#000;font-weight:bold;border-radius:10px;text-decoration:none;">
                ثبت‌نام در این دورهمی
            </a>
            <p style="color:#666;font-size:0.75rem;text-align:center;margin-top:0.5rem;">پرداخت در فاز بعدی فعال می‌شود</p>
        @endif
    </div>
</div>

<div style="margin-top:1rem;">
    <a href="{{ route('panel.events.index') }}" style="display:block;text-align:center;padding:0.85rem;background:#2a2a2a;color:#ddd;border-radius:10px;text-decoration:none;">بازگشت به لیست</a>
</div>
@endsection
