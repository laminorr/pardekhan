@extends('panel.layouts.app')
@section('title', 'دورهمی‌ها')

@section('content')
<div class="page-head" style="display:flex;align-items:center;gap:0.75rem;">
    <a href="{{ route('panel.dashboard') }}" class="icon-btn" style="flex-shrink:0;">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M9 18l6-6-6-6"/></svg>
    </a>
    <div>
        <div class="page-title">دورهمی‌ها</div>
        <div class="page-sub">دورهمی‌های قابل دسترسی شما</div>
    </div>
</div>

@if($events->isEmpty())
    <div class="card" style="text-align:center;padding:3rem 1.5rem;">
        <div style="width:64px;height:64px;border-radius:20px;background:rgba(212,175,106,0.08);border:1px solid var(--gold-border);display:flex;align-items:center;justify-content:center;color:var(--gold-2);margin:0 auto 1rem;">
            <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="4" width="20" height="16" rx="2"/><path d="M7 4v16M17 4v16M2 9h5M2 15h5M17 9h5M17 15h5"/></svg>
        </div>
        <p style="color:var(--text-dim);">در حال حاضر دورهمی فعالی برای شما وجود ندارد.</p>
    </div>
@else
    <div style="display:flex;flex-direction:column;gap:1rem;">
        @foreach($events as $event)
        @php $remaining = $event->remainingCapacity(); @endphp
        <a href="{{ route('panel.events.show', $event) }}" class="card" style="margin-bottom:0;padding:0;overflow:hidden;text-decoration:none;color:inherit;">
            @if($event->image)
                <div style="height:140px;position:relative;">
                    <img src="{{ Storage::url($event->image) }}" style="width:100%;height:100%;object-fit:cover;">
                    <div style="position:absolute;inset:0;background:linear-gradient(to top,var(--surface) 0%,transparent 50%);"></div>
                    @if($event->status === 'full' || $remaining <= 0)
                        <span style="position:absolute;top:0.85rem;right:0.85rem;background:rgba(0,0,0,0.5);backdrop-filter:blur(4px);border:1px solid rgba(255,255,255,0.1);color:#fff;font-size:0.7rem;padding:4px 10px;border-radius:99px;">تکمیل</span>
                    @elseif($remaining <= 3)
                        <span style="position:absolute;top:0.85rem;right:0.85rem;background:rgba(212,175,106,0.9);color:#1a1408;font-size:0.7rem;font-weight:700;padding:4px 10px;border-radius:99px;">{{ $remaining }} جای باقی‌مانده</span>
                    @endif
                </div>
            @endif
            <div style="padding:1.1rem 1.2rem 1.3rem;{{ $event->image ? 'margin-top:-1.5rem;position:relative;z-index:1;' : '' }}">
                <div style="font-size:1.05rem;font-weight:700;color:#fff;">{{ $event->title }}</div>
                @if($event->subtitle)
                    <div style="font-size:0.8rem;color:var(--text-dim);margin-top:3px;">{{ $event->subtitle }}</div>
                @endif
                <div style="display:flex;gap:1.2rem;margin-top:0.85rem;">
                    <div style="display:flex;align-items:center;gap:5px;font-size:0.76rem;color:var(--text-dim);">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="var(--gold-2)" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2"/><path d="M16 2v4M8 2v4M3 10h18"/></svg>
                        {{ \Morilog\Jalali\Jalalian::fromDateTime($event->starts_at)->format('Y/m/d') }}
                    </div>
                    <div style="display:flex;align-items:center;gap:5px;font-size:0.76rem;color:var(--text-dim);">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="var(--gold-2)" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/></svg>
                        {{ $event->starts_at->format('H:i') }}
                    </div>
                </div>
            </div>
        </a>
        @endforeach
    </div>
@endif
@endsection
