@extends('panel.layouts.app')
@section('title', 'دورهمی‌ها')

@section('content')
{{-- هدر --}}
<div style="display:flex;justify-content:space-between;align-items:flex-end;margin-bottom:1.3rem;">
    <div>
        <div style="font-size:1.5rem;font-weight:800;letter-spacing:-0.5px;">دورهمی‌ها</div>
        <div style="font-size:0.82rem;color:var(--ink-faint);margin-top:3px;">{{ fa($events->count()) }} رویداد پیش‌رو</div>
    </div>
</div>

@if($events->isEmpty())
    <div style="text-align:center;padding:3rem 1rem;color:var(--ink-dim);">
        <svg width="56" height="56" viewBox="0 0 24 24" fill="none" stroke="var(--ink-faint)" stroke-width="1.3" style="margin:0 auto 1rem;"><rect x="3" y="4" width="18" height="18" rx="2"/><path d="M16 2v4M8 2v4M3 10h18"/></svg>
        <div style="font-size:0.95rem;">فعلاً دورهمی‌ای برنامه‌ریزی نشده</div>
        <div style="font-size:0.8rem;margin-top:4px;">به‌زودی رویدادهای جدید اضافه می‌شوند</div>
    </div>
@else
    <div style="display:flex;flex-direction:column;gap:1rem;">
        @foreach($events as $event)
        @php
            $remaining = $event->remainingCapacity();
            $isSpecial = $event->layers->isNotEmpty();
            $price = $event->priceForMember($member);
            $regOpen = $event->status === 'active' && $remaining > 0;
        @endphp
        <a href="{{ route('panel.events.show', $event) }}" style="display:block;text-decoration:none;color:inherit;border:1px solid var(--border);border-radius:22px;overflow:hidden;background:#fff;box-shadow:0 20px 40px -36px rgba(47,93,80,0.5);">
            {{-- تصویر --}}
            <div style="height:120px;position:relative;background:linear-gradient(135deg,#eaefec,#dde6e1);display:flex;align-items:center;justify-content:center;">
                @if($event->image)
                    <img src="{{ Storage::url($event->image) }}" style="width:100%;height:100%;object-fit:cover;">
                @else
                    <span style="font-size:0.78rem;color:#8ba096;">پرده‌خوان</span>
                @endif
                @if($isSpecial)
                    <span style="position:absolute;top:12px;right:12px;background:var(--pine);color:#fff;font-size:0.62rem;font-weight:700;padding:5px 10px;border-radius:16px;">ویژهٔ لایهٔ شما</span>
                @endif
            </div>
            {{-- محتوا --}}
            <div style="padding:0.9rem 1rem 1rem;">
                <div style="font-size:1.05rem;font-weight:800;letter-spacing:-0.3px;color:var(--ink);">{{ $event->title }}</div>
                <div style="display:flex;align-items:center;gap:0.75rem;margin-top:0.5rem;font-size:0.72rem;color:var(--ink-dim);">
                    <span style="display:flex;align-items:center;gap:4px;">
                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="var(--pine)" stroke-width="1.8"><circle cx="12" cy="12" r="8.5"/><path d="M12 7.5V12l3 1.8" stroke-linecap="round"/></svg>
                        {{ fa(\Morilog\Jalali\Jalalian::fromDateTime($event->starts_at)->format('l j F')) }} · {{ fa($event->starts_at->format('H:i')) }}
                    </span>
                </div>
                <div style="display:flex;align-items:center;justify-content:space-between;margin-top:0.75rem;">
                    @if($regOpen)
                        @if($remaining <= 5)
                            <span style="display:inline-flex;align-items:center;gap:6px;font-size:0.72rem;color:var(--burnt);font-weight:700;">
                                <span style="width:7px;height:7px;border-radius:50%;background:var(--burnt);"></span>{{ fa($remaining) }} جای باقی‌مانده
                            </span>
                        @else
                            <span style="font-size:0.72rem;color:var(--ink-dim);">{{ fa($remaining) }} جای خالی</span>
                        @endif
                        <span style="font-size:0.74rem;font-weight:800;color:var(--pine);background:var(--green-soft);padding:6px 13px;border-radius:14px;">ثبت‌نام باز</span>
                    @elseif($event->status === 'full' || $remaining <= 0)
                        <span style="font-size:0.72rem;color:var(--ink-dim);font-weight:600;">ظرفیت تکمیل</span>
                        <span style="font-size:0.74rem;font-weight:700;color:var(--ink-mid);background:var(--bg-mute);padding:6px 13px;border-radius:14px;">لیست انتظار</span>
                    @else
                        <span style="font-size:0.72rem;color:var(--ink-dim);">{{ fa(number_format($price)) }} تومان</span>
                        <span style="font-size:0.74rem;font-weight:700;color:var(--ink-mid);background:var(--bg-mute);padding:6px 13px;border-radius:14px;">جزئیات</span>
                    @endif
                </div>
            </div>
        </a>
        @endforeach
    </div>
@endif
@endsection

@section('nav')
    @include('panel.partials.bottom-nav', ['active' => 'events'])
@endsection
