@extends('panel.layouts.app')
@section('title', 'بلیت‌های من')

@section('content')
{{-- هدر --}}
<div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1.3rem;">
    <div style="font-size:1.5rem;font-weight:800;letter-spacing:-0.5px;">بلیت‌های من</div>
    <a href="{{ route('panel.dashboard') }}" class="icon-btn">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 6l6 6-6 6"/></svg>
    </a>
</div>

@if($tickets->isEmpty())
    <div style="text-align:center;padding:3rem 1rem;color:var(--ink-dim);">
        <svg width="56" height="56" viewBox="0 0 24 24" fill="none" stroke="var(--ink-faint)" stroke-width="1.3" style="margin:0 auto 1rem;"><path d="M3 9a2 2 0 0 0 0 6v2a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-2a2 2 0 0 1 0-6V7a2 2 0 0 0-2-2H5a2 2 0 0 0-2 2z"/></svg>
        <div style="font-size:0.95rem;">هنوز بلیتی ندارید</div>
        <a href="{{ route('panel.events.index') }}" class="btn btn-primary" style="margin-top:1.2rem;max-width:240px;margin-inline:auto;">مشاهدهٔ دورهمی‌ها</a>
    </div>
@else
<div style="display:flex;flex-direction:column;gap:1rem;">
    @foreach($tickets as $ticket)
    @php
        $statusInfo = match($ticket->status) {
            'active' => ['معتبر', 'var(--pine)', 'var(--green-soft)'],
            'pending_payment' => ['در انتظار پرداخت', 'var(--burnt)', '#fbeee4'],
            'used' => ['استفاده شده', 'var(--ink-dim)', '#f0f1f0'],
            default => ['لغو شده', 'var(--burnt)', '#fbeae4'],
        };
    @endphp
    <a href="{{ route('panel.tickets.show', $ticket) }}" style="display:flex;text-decoration:none;color:inherit;background:#fff;border:1px solid var(--border);border-radius:18px;overflow:hidden;box-shadow:0 4px 20px rgba(40,60,50,0.05);">
        {{-- نوار رنگی کناری --}}
        <div style="width:6px;background:linear-gradient(var(--pine),var(--pine-bright));flex-shrink:0;"></div>
        <div style="flex:1;padding:1.1rem 1.2rem;">
            <div style="display:flex;justify-content:space-between;align-items:flex-start;gap:0.5rem;">
                <div style="font-size:1.02rem;font-weight:800;letter-spacing:-0.2px;">{{ $ticket->event->title }}</div>
                <span style="flex-shrink:0;font-size:0.68rem;font-weight:700;padding:4px 10px;border-radius:99px;color:{{ $statusInfo[1] }};background:{{ $statusInfo[2] }};">{{ $statusInfo[0] }}</span>
            </div>
            <div style="display:flex;align-items:center;gap:1rem;margin-top:0.6rem;font-size:0.76rem;color:var(--ink-dim);">
                <span style="display:flex;align-items:center;gap:4px;">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="var(--pine)" stroke-width="1.8"><rect x="3" y="4" width="18" height="18" rx="2"/><path d="M16 2v4M8 2v4M3 10h18"/></svg>
                    {{ fa(\Morilog\Jalali\Jalalian::fromDateTime($ticket->event->starts_at)->format('j F')) }} · {{ fa($ticket->event->starts_at->format('H:i')) }}
                </span>
            </div>
            <div style="font-size:0.72rem;color:var(--ink-faint);margin-top:8px;direction:ltr;text-align:right;letter-spacing:1px;">{{ fa($ticket->code) }}</div>
        </div>
    </a>
    @endforeach
</div>
@endif
@endsection

@section('nav')
    @include('panel.partials.bottom-nav', ['active' => 'tickets'])
@endsection
