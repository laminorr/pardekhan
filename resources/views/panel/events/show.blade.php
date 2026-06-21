@extends('panel.layouts.app')
@section('title', $event->title)

@push('styles')
<style>
    .ev-hero { position:relative; height:260px; margin:-1.4rem -1.2rem 0; background:linear-gradient(135deg,#dfe7e3,#cfdbd5); display:flex; align-items:center; justify-content:center; }
    .ev-hero img { width:100%; height:100%; object-fit:cover; }
    .ev-hero-actions { position:absolute; top:1rem; right:1.2rem; left:1.2rem; display:flex; justify-content:space-between; }
    .ev-hero-btn { width:44px; height:44px; border-radius:14px; background:rgba(255,255,255,0.92); border:none; display:flex; align-items:center; justify-content:center; backdrop-filter:blur(6px); cursor:pointer; text-decoration:none; }
    .ev-body { position:relative; margin-top:-26px; background:var(--bg); border-radius:28px 28px 0 0; padding:1.5rem 0.2rem 0; }
    .ev-info-row { display:flex; align-items:center; gap:0.85rem; }
    .ev-info-ico { width:44px; height:44px; border-radius:14px; background:var(--bg-soft); display:flex; align-items:center; justify-content:center; flex-shrink:0; }
</style>
@endpush

@section('content')
{{-- تصویر بزرگ --}}
<div class="ev-hero">
    @if($event->image)
        <img src="{{ Storage::url($event->image) }}" alt="{{ $event->title }}">
    @else
        <span style="font-size:0.9rem;color:#7e948b;letter-spacing:1px;">پرده‌خوان</span>
    @endif
    <div class="ev-hero-actions">
        <a href="{{ route('panel.events.index') }}" class="ev-hero-btn">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#16181a" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 6l6 6-6 6"/></svg>
        </a>
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
        <div style="font-size:0.86rem;color:var(--ink-dim);margin-top:0.6rem;line-height:1.85;">{{ $event->description }}</div>
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
                    <div style="font-size:0.78rem;color:var(--ink-dim);margin-top:2px;">{{ $event->venue->address }}</div>
                @endif
            </div>
        </div>
        @endif
        {{-- ظرفیت --}}
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

    {{-- عکس شرکت‌کنندگان --}}
    @if($attendeeAvatars->isNotEmpty())
    <div style="margin-top:1.3rem;display:flex;align-items:center;gap:0.6rem;">
        <div style="display:flex;">
            @foreach($attendeeAvatars->take(6) as $i => $att)
                <div style="width:34px;height:34px;border-radius:50%;border:2px solid var(--bg);overflow:hidden;margin-right:{{ $i > 0 ? '-10px' : '0' }};">
                    <img src="{{ Storage::url($att->avatar) }}" style="width:100%;height:100%;object-fit:cover;">
                </div>
            @endforeach
        </div>
        <span style="font-size:0.78rem;color:var(--ink-dim);">{{ fa($attendeeAvatars->count()) }} نفر ثبت‌نام کرده‌اند</span>
    </div>
    @endif

    {{-- قیمت و دکمه --}}
    <div style="margin-top:1.5rem;padding:1.2rem;background:#fff;border:1px solid var(--border);border-radius:20px;box-shadow:0 4px 20px rgba(40,60,50,0.05);">
        <div style="display:flex;align-items:flex-end;justify-content:space-between;margin-bottom:1rem;">
            <div>
                <div style="font-size:0.72rem;color:var(--ink-dim);">هزینهٔ ثبت‌نام</div>
                @if($discount > 0)
                    <span style="font-size:0.78rem;color:var(--ink-faint);text-decoration:line-through;">{{ fa(number_format($event->base_price)) }}</span>
                @endif
                <div style="font-size:1.5rem;font-weight:800;color:var(--pine);">{{ fa(number_format($price)) }} <small style="font-size:0.72rem;color:var(--ink-dim);font-weight:400;">تومان</small></div>
            </div>
        </div>

        @if($isRegistered)
            <div style="text-align:center;padding:0.6rem;background:var(--green-tint);border-radius:13px;color:var(--pine-deep);font-size:0.88rem;font-weight:700;margin-bottom:0.5rem;">
                ✓ شما ثبت‌نام کرده‌اید
            </div>
            <a href="{{ route('panel.tickets.index') }}" class="btn btn-primary">مشاهدهٔ بلیت</a>
            <form method="POST" action="{{ route('panel.events.cancel', $event) }}" style="margin-top:0.6rem;" onsubmit="return confirm('آیا از انصراف مطمئن هستید؟');">
                @csrf
                <button type="submit" class="btn btn-ghost" style="color:var(--burnt);">انصراف از دورهمی</button>
            </form>
        @elseif($isWaiting)
            <div style="text-align:center;padding:0.6rem;background:var(--bg-soft);border-radius:13px;color:var(--ink-mid);font-size:0.88rem;font-weight:600;">
                شما در لیست انتظار هستید
            </div>
        @elseif($event->remainingCapacity() <= 0)
            <form method="POST" action="{{ route('panel.events.waitlist', $event) }}">
                @csrf
                <button type="submit" class="btn btn-primary">عضویت در لیست انتظار</button>
            </form>
        @else
            <a href="{{ route('panel.payment.checkout', $event) }}" class="btn btn-primary">
                ثبت‌نام در دورهمی
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 12H5M12 5l-7 7 7 7"/></svg>
            </a>
        @endif
    </div>
    <div style="height:1.5rem;"></div>
</div>
@endsection
