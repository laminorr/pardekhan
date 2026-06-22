@extends('panel.layouts.app')
@section('title', $event->title)

@push('styles')
<style>
    .ev-hero { position:relative; height:260px; margin:-1.4rem -1.2rem 0; background:linear-gradient(135deg,#dfe7e3,#cfdbd5); display:flex; align-items:center; justify-content:center; }
    .ev-hero img { width:100%; height:100%; object-fit:cover; }
    .ev-hero-actions { position:absolute; top:1rem; right:1.2rem; left:1.2rem; display:flex; justify-content:space-between; }
    .ev-hero-btn { width:44px; height:44px; border-radius:14px; background:rgba(255,255,255,0.92); border:none; display:flex; align-items:center; justify-content:center; backdrop-filter:blur(6px); cursor:pointer; text-decoration:none; }
    .ev-body { position:relative; margin-top:-26px; background:var(--bg); border-radius:28px 28px 0 0; padding:1.5rem 0.2rem 0; }
    .ev-desc { font-size:0.86rem; color:var(--ink-dim); margin-top:0.6rem; line-height:1.95; text-align:justify; }
    .ev-info-row { display:flex; align-items:center; gap:0.85rem; }
    .ev-info-ico { width:44px; height:44px; border-radius:14px; background:var(--bg-soft); display:flex; align-items:center; justify-content:center; flex-shrink:0; }
    .ev-map { margin-top:1.3rem; height:120px; border-radius:18px; background:var(--green-line); position:relative; overflow:hidden; border:1px solid var(--border-2); }
    .ev-map-grid { position:absolute; inset:0; background-image:linear-gradient(#dde2df 1px,transparent 1px),linear-gradient(90deg,#dde2df 1px,transparent 1px); background-size:26px 26px; }
    .ev-paybar { position:fixed; bottom:0; left:50%; transform:translateX(-50%); width:100%; max-width:430px; background:rgba(252,252,251,0.96); backdrop-filter:blur(10px); border-top:1px solid var(--border); padding:1rem 1.2rem calc(1rem + env(safe-area-inset-bottom)); display:flex; align-items:center; justify-content:space-between; gap:1rem; z-index:60; }
</style>
@endpush

@section('content')
{{-- تصویر بزرگ --}}
<div class="ev-hero">
    @if($event->image)
        <img src="{{ Storage::url($event->image) }}" alt="{{ $event->title }}">
    @else
        <span style="font-size:0.85rem;color:#7e948b;letter-spacing:1px;">تصویر دورهمی</span>
    @endif
    <div class="ev-hero-actions">
        <a href="{{ route('panel.events.index') }}" class="ev-hero-btn">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#16181a" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 6l6 6-6 6"/></svg>
        </a>
        <div class="ev-hero-btn" style="cursor:default;">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="var(--pine)" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><path d="M12 20s-7-4.5-7-9.5A4 4 0 0 1 12 7a4 4 0 0 1 7 3.5C19 15.5 12 20 12 20z"/></svg>
        </div>
    </div>
</div>

{{-- محتوا --}}
<div class="ev-body">
    @if($discount > 0)
        <span style="display:inline-block;background:var(--green-soft);color:var(--pine);font-size:0.7rem;font-weight:700;padding:6px 13px;border-radius:20px;">ویژهٔ لایهٔ شما · {{ fa($discount) }}٪ تخفیف</span>
    @endif

    <div style="font-size:1.6rem;font-weight:800;line-height:1.25;margin-top:0.8rem;letter-spacing:-0.5px;">{{ $event->title }}</div>
    @if($event->subtitle)
        <div style="font-size:0.92rem;color:var(--pine);margin-top:4px;font-weight:600;">{{ $event->subtitle }}</div>
    @endif
    @if($event->description)
        <div class="ev-desc">{{ $event->description }}</div>
    @endif

    {{-- اطلاعات --}}
    <div style="margin-top:1.3rem;display:flex;flex-direction:column;gap:0.9rem;">
        <div class="ev-info-row">
            <div class="ev-info-ico">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="var(--pine)" stroke-width="1.6"><rect x="4" y="6" width="16" height="15" rx="2.5"/><path d="M4 10h16M8 3v4M16 3v4"/></svg>
            </div>
            <div>
                <div style="font-size:0.9rem;font-weight:700;">{{ fa(\Morilog\Jalali\Jalalian::fromDateTime($event->starts_at)->format('l j F Y')) }}</div>
                <div style="font-size:0.78rem;color:var(--ink-dim);margin-top:2px;">ساعت {{ fa($event->starts_at->format('H:i')) }}@if($event->ends_at) تا {{ fa($event->ends_at->format('H:i')) }}@endif</div>
            </div>
        </div>
        @if($event->venue)
        <div class="ev-info-row">
            <div class="ev-info-ico">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="var(--pine)" stroke-width="1.6"><path d="M12 21s7-5.5 7-11a7 7 0 1 0-14 0c0 5.5 7 11 7 11z"/><circle cx="12" cy="10" r="2.4"/></svg>
            </div>
            <div>
                <div style="font-size:0.9rem;font-weight:700;">{{ $event->venue->name }}</div>
                @if($event->venue->address)
                    <div style="font-size:0.78rem;color:var(--ink-dim);margin-top:2px;text-align:justify;">{{ $event->venue->address }}</div>
                @endif
            </div>
        </div>
        @endif
        <div class="ev-info-row">
            <div class="ev-info-ico">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="var(--pine)" stroke-width="1.6"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/></svg>
            </div>
            <div>
                <div style="font-size:0.9rem;font-weight:700;">{{ fa($event->capacity) }} نفر ظرفیت</div>
                <div style="font-size:0.78rem;color:var(--ink-dim);margin-top:2px;">{{ fa($event->remainingCapacity()) }} جای باقی‌مانده</div>
            </div>
        </div>
    </div>

    {{-- نقشه --}}
    @if($event->venue)
    <div class="ev-map">
        <div class="ev-map-grid"></div>
        <div style="position:absolute;top:50%;left:50%;transform:translate(-50%,-50%);display:flex;flex-direction:column;align-items:center;gap:6px;">
            <svg width="26" height="26" viewBox="0 0 24 24" fill="var(--pine)"><path d="M12 2a7 7 0 0 0-7 7c0 5 7 13 7 13s7-8 7-13a7 7 0 0 0-7-7zm0 9.5A2.5 2.5 0 1 1 12 6a2.5 2.5 0 0 1 0 5.5z"/></svg>
            @if($event->venue->map_link)
                <a href="{{ $event->venue->map_link }}" target="_blank" style="font-size:0.7rem;color:var(--pine);font-weight:600;text-decoration:none;">نمایش روی نقشه</a>
            @else
                <span style="font-size:0.7rem;color:var(--ink-mid);font-weight:600;">{{ $event->venue->name }}</span>
            @endif
        </div>
    </div>
    @endif

    {{-- شرکت‌کنندگان --}}
    @if($attendeeAvatars->isNotEmpty())
    <div style="margin-top:1.3rem;display:flex;align-items:center;justify-content:space-between;">
        <div style="display:flex;align-items:center;">
            @foreach($attendeeAvatars->take(4) as $i => $att)
                <div style="width:34px;height:34px;border-radius:50%;border:2.5px solid var(--bg);overflow:hidden;margin-right:{{ $i > 0 ? '-10px' : '0' }};">
                    <img src="{{ Storage::url($att->avatar) }}" style="width:100%;height:100%;object-fit:cover;">
                </div>
            @endforeach
            @if($attendeeAvatars->count() > 4)
                <div style="width:34px;height:34px;border-radius:50%;background:var(--pine);border:2.5px solid var(--bg);margin-right:-10px;display:flex;align-items:center;justify-content:center;font-size:0.66rem;color:#fff;font-weight:700;">{{ fa($attendeeAvatars->count() - 4) }}+</div>
            @endif
        </div>
        <span style="font-size:0.76rem;color:var(--ink-dim);">{{ fa($attendeeAvatars->count()) }} نفر ثبت‌نام کرده‌اند</span>
    </div>
    @endif

    {{-- فاصله برای نوار پایین --}}
    <div style="height:120px;"></div>
</div>

{{-- نوار قیمت ثابت پایین --}}
<div class="ev-paybar">
    <div>
        @if($discount > 0)
            <div style="font-size:0.68rem;color:var(--ink-faint);text-decoration:line-through;">{{ fa(number_format($event->base_price)) }}</div>
        @endif
        <div>
            <span style="font-size:1.4rem;font-weight:800;letter-spacing:-0.5px;color:var(--pine);">{{ fa(number_format($price)) }}</span>
            <span style="font-size:0.7rem;color:var(--ink-dim);margin-right:4px;">تومان</span>
        </div>
    </div>

    @if($isRegistered)
        <a href="{{ route('panel.tickets.index') }}" class="btn btn-primary" style="width:auto;padding:0.85rem 1.8rem;">مشاهدهٔ بلیت</a>
    @elseif($isWaiting)
        <span style="font-size:0.82rem;color:var(--ink-mid);font-weight:600;background:var(--bg-soft);padding:0.85rem 1.4rem;border-radius:14px;">در لیست انتظار</span>
    @elseif($event->remainingCapacity() <= 0)
        <form method="POST" action="{{ route('panel.events.waitlist', $event) }}">
            @csrf
            <button type="submit" class="btn btn-primary" style="width:auto;padding:0.85rem 1.6rem;">لیست انتظار</button>
        </form>
    @else
        <a href="{{ route('panel.payment.checkout', $event) }}" class="btn btn-primary" style="width:auto;padding:0.85rem 1.9rem;">
            ثبت‌نام
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 12H5M12 5l-7 7 7 7"/></svg>
        </a>
    @endif
</div>

{{-- اگر ثبت‌نام کرده، گزینه انصراف زیر نوار (به‌صورت لینک کوچک) --}}
@if($isRegistered)
<div style="position:fixed;bottom:calc(5rem + env(safe-area-inset-bottom));left:50%;transform:translateX(-50%);z-index:59;">
    <form method="POST" action="{{ route('panel.events.cancel', $event) }}" onsubmit="return confirm('آیا از انصراف مطمئن هستید؟');">
        @csrf
        <button type="submit" style="background:none;border:none;color:var(--burnt);font-family:inherit;font-size:0.78rem;font-weight:600;cursor:pointer;text-decoration:underline;">انصراف از دورهمی</button>
    </form>
</div>
@endif
@endsection
