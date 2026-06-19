@extends('panel.layouts.app')
@section('title', 'بلیت‌های من')

@section('content')
<div class="page-head" style="display:flex;align-items:center;gap:0.75rem;">
    <a href="{{ route('panel.dashboard') }}" class="icon-btn" style="flex-shrink:0;">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M9 18l6-6-6-6"/></svg>
    </a>
    <div class="page-title" style="font-size:1.2rem;">بلیت‌های من</div>
</div>

@if($tickets->isEmpty())
    <div class="card" style="text-align:center;padding:3rem 1.5rem;">
        <p style="color:var(--text-dim);">هنوز بلیتی ندارید.</p>
    </div>
@else
    @foreach($tickets as $ticket)
    @if($ticket->event)
    <a href="{{ route('panel.tickets.show', $ticket) }}" class="card" style="display:block;text-decoration:none;color:inherit;padding:0;overflow:hidden;">
        <div style="display:flex;align-items:stretch;">
            <div style="width:6px;background:linear-gradient(var(--gold-1),var(--gold-3));"></div>
            <div style="flex:1;padding:1.1rem 1.2rem;">
                <div style="display:flex;justify-content:space-between;align-items:flex-start;">
                    <div style="font-weight:700;color:#fff;">{{ $ticket->event->title }}</div>
                    <span style="font-size:0.7rem;padding:3px 9px;border-radius:99px;
                        @if($ticket->status === 'active') background:rgba(93,202,143,0.12);color:var(--success);
                        @elseif($ticket->status === 'used') background:rgba(255,255,255,0.05);color:var(--text-dim);
                        @else background:rgba(226,101,90,0.12);color:var(--danger); @endif">
                        {{ $ticket->statusLabel() }}
                    </span>
                </div>
                <div style="font-size:0.78rem;color:var(--text-dim);margin-top:6px;">
                    {{ \Morilog\Jalali\Jalalian::fromDateTime($ticket->event->starts_at)->format('Y/m/d') }} · {{ $ticket->event->starts_at->format('H:i') }}
                </div>
                <div style="font-size:0.72rem;color:var(--gold-2);margin-top:8px;direction:ltr;text-align:right;font-family:monospace;">{{ $ticket->code }}</div>
            </div>
        </div>
    </a>
    @endif
    @endforeach
@endif
@endsection
