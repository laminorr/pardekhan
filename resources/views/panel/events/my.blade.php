@extends('panel.layouts.app')
@section('title', 'دورهمی‌های من')

@section('content')
<div class="page-head" style="display:flex;align-items:center;gap:0.75rem;">
    <a href="{{ route('panel.dashboard') }}" class="icon-btn" style="flex-shrink:0;">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M9 18l6-6-6-6"/></svg>
    </a>
    <div class="page-title" style="font-size:1.2rem;">دورهمی‌های من</div>
</div>

@php
    $statusLabel = fn($s) => match($s) {
        'registered' => ['ثبت‌نام شده', 'var(--gold-1)'],
        'attended' => ['حضور یافته', 'var(--success)'],
        'cancelled_by_user' => ['انصراف داده', 'var(--text-dim)'],
        'absent' => ['غایب', 'var(--danger)'],
        'cancelled_by_admin' => ['لغو شده', 'var(--danger)'],
        default => [$s, 'var(--text-dim)'],
    };
@endphp

@if($upcoming->isEmpty() && $past->isEmpty())
    <div class="card" style="text-align:center;padding:3rem 1.5rem;">
        <p style="color:var(--text-dim);">هنوز در دورهمی‌ای ثبت‌نام نکرده‌اید.</p>
        <a href="{{ route('panel.events.index') }}" class="btn btn-gold" style="margin-top:1rem;">مشاهده دورهمی‌ها</a>
    </div>
@endif

@if($upcoming->isNotEmpty())
<div class="section-head"><div class="section-title"><span class="bar"></span> آینده</div></div>
@foreach($upcoming as $reg)
    @if($reg->event)
    <a href="{{ route('panel.events.show', $reg->event) }}" class="card" style="display:block;text-decoration:none;color:inherit;">
        <div style="display:flex;justify-content:space-between;align-items:flex-start;">
            <div>
                <div style="font-weight:700;color:#fff;">{{ $reg->event->title }}</div>
                <div style="font-size:0.78rem;color:var(--text-dim);margin-top:4px;">{{ \Morilog\Jalali\Jalalian::fromDateTime($reg->event->starts_at)->format('Y/m/d') }} · {{ $reg->event->starts_at->format('H:i') }}</div>
            </div>
            @php [$label, $color] = $statusLabel($reg->attendance_status); @endphp
            <span style="font-size:0.72rem;color:{{ $color }};background:rgba(255,255,255,0.04);padding:3px 10px;border-radius:99px;">{{ $label }}</span>
        </div>
        @if($reg->payment_status === 'pending')
            <div style="font-size:0.72rem;color:var(--gold-2);margin-top:0.6rem;padding-top:0.6rem;border-top:1px solid var(--border);">⏳ پرداخت در انتظار بررسی</div>
        @endif
    </a>
    @endif
@endforeach
@endif

@if($past->isNotEmpty())
<div class="section-head"><div class="section-title"><span class="bar"></span> گذشته</div></div>
@foreach($past as $reg)
    @if($reg->event)
    <div class="card">
        <div style="display:flex;justify-content:space-between;align-items:flex-start;">
            <div>
                <div style="font-weight:700;color:#fff;">{{ $reg->event->title }}</div>
                <div style="font-size:0.78rem;color:var(--text-dim);margin-top:4px;">{{ \Morilog\Jalali\Jalalian::fromDateTime($reg->event->starts_at)->format('Y/m/d') }}</div>
            </div>
            @php [$label, $color] = $statusLabel($reg->attendance_status); @endphp
            <span style="font-size:0.72rem;color:{{ $color }};background:rgba(255,255,255,0.04);padding:3px 10px;border-radius:99px;">{{ $label }}</span>
        </div>
        <a href="{{ route('panel.feedback.create', $reg->event) }}" style="display:flex;align-items:center;justify-content:center;gap:6px;margin-top:0.85rem;padding:0.6rem;background:rgba(212,175,106,0.08);border:1px solid var(--gold-border);border-radius:10px;color:var(--gold-1);text-decoration:none;font-size:0.82rem;">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7"><path d="M12 2l2.6 6.6L21 9.2l-5 4.5 1.5 7L12 17l-5.5 3.7L8 13.7l-5-4.5 6.4-.6z"/></svg>
            ثبت بازخورد
        </a>
    </div>
    @endif
@endforeach
@endif
@endsection
