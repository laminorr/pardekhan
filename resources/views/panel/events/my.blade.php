@extends('panel.layouts.app')
@section('title', 'دورهمی‌های من')

@section('content')
{{-- هدر --}}
<div class="page-head">
    <a href="{{ route('panel.dashboard') }}" class="icon-btn" style="flex-shrink:0;">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 6l6 6-6 6"/></svg>
    </a>
    <div>
        <div class="page-title">دورهمی‌های من</div>
    </div>
</div>

@if($upcoming->isEmpty() && $past->isEmpty())
    <div style="text-align:center;padding:3rem 1rem;color:var(--ink-dim);">
        <svg width="56" height="56" viewBox="0 0 24 24" fill="none" stroke="var(--ink-faint)" stroke-width="1.3" style="margin:0 auto 1rem;"><rect x="3" y="4" width="18" height="18" rx="2"/><path d="M16 2v4M8 2v4M3 10h18"/></svg>
        <div style="font-size:0.95rem;">هنوز در دورهمی‌ای ثبت‌نام نکرده‌اید</div>
        <a href="{{ route('panel.events.index') }}" class="btn btn-primary" style="margin-top:1.2rem;max-width:240px;margin-inline:auto;">مشاهدهٔ دورهمی‌ها</a>
    </div>
@endif

@if($upcoming->isNotEmpty())
<div class="section-head"><div class="section-title">پیش‌رو</div></div>
<div style="display:flex;flex-direction:column;gap:0.85rem;">
@foreach($upcoming as $reg)
    <a href="{{ route('panel.events.show', $reg->event) }}" style="display:block;text-decoration:none;color:inherit;background:#fff;border:1px solid var(--border);border-radius:18px;padding:1.1rem;box-shadow:0 2px 14px rgba(40,60,50,0.04);">
        <div style="font-size:1rem;font-weight:800;letter-spacing:-0.2px;">{{ $reg->event->title }}</div>
        <div style="display:flex;align-items:center;gap:1rem;margin-top:0.6rem;font-size:0.76rem;color:var(--ink-dim);">
            <span style="display:flex;align-items:center;gap:4px;">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="var(--pine)" stroke-width="1.8"><rect x="3" y="4" width="18" height="18" rx="2"/><path d="M16 2v4M8 2v4M3 10h18"/></svg>
                {{ fa(\Morilog\Jalali\Jalalian::fromDateTime($reg->event->starts_at)->format('j F')) }} · {{ fa($reg->event->starts_at->format('H:i')) }}
            </span>
            <span style="color:var(--pine);font-weight:700;">ثبت‌نام شده</span>
        </div>
    </a>
@endforeach
</div>
@endif

@if($past->isNotEmpty())
<div class="section-head"><div class="section-title">گذشته</div></div>
<div style="display:flex;flex-direction:column;gap:0.85rem;">
@foreach($past as $reg)
    <div style="background:#fff;border:1px solid var(--border);border-radius:18px;padding:1.1rem;box-shadow:0 2px 14px rgba(40,60,50,0.04);">
        <div style="font-size:1rem;font-weight:800;letter-spacing:-0.2px;">{{ $reg->event->title }}</div>
        <div style="display:flex;align-items:center;gap:1rem;margin-top:0.6rem;font-size:0.76rem;color:var(--ink-dim);">
            <span>{{ fa(\Morilog\Jalali\Jalalian::fromDateTime($reg->event->starts_at)->format('j F Y')) }}</span>
            @if($reg->attendance_status === 'attended')
                <span style="color:var(--pine);font-weight:700;">✓ حاضر</span>
            @elseif($reg->attendance_status === 'absent')
                <span style="color:var(--burnt);font-weight:700;">غایب</span>
            @endif
        </div>
        @if($reg->attendance_status === 'attended')
        <a href="{{ route('panel.feedback.create', $reg->event) }}" style="display:flex;align-items:center;justify-content:center;gap:6px;margin-top:0.85rem;padding:0.6rem;background:var(--green-tint);border:1px solid #c5ddd2;border-radius:11px;color:var(--pine);text-decoration:none;font-size:0.82rem;font-weight:600;">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M12 2l2.6 6.6L21 9.2l-5 4.5 1.5 7L12 17l-5.5 3.7L8 13.7l-5-4.5 6.4-.6z"/></svg>
            ثبت بازخورد
        </a>
        @endif
    </div>
@endforeach
</div>
@endif
@endsection

@section('nav')
    @include('panel.partials.bottom-nav', ['active' => 'events'])
@endsection
