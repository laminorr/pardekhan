@extends('panel.layouts.app')
@section('title', 'بازخورد شما')

@section('content')
<div class="page-head" style="display:flex;align-items:center;gap:0.75rem;">
    <a href="{{ route('panel.events.my') }}" class="icon-btn" style="flex-shrink:0;">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M9 18l6-6-6-6"/></svg>
    </a>
    <div class="page-title" style="font-size:1.2rem;">بازخورد شما</div>
</div>

<div class="card">
    <div style="font-weight:700;color:var(--ink);">{{ $event->title }}</div>
    <div style="font-size:0.8rem;color:var(--ink-dim);margin-top:4px;">{{ \Morilog\Jalali\Jalalian::fromDateTime($event->starts_at)->format('Y/m/d') }}</div>
</div>

<div class="card">
    {{-- ستاره‌ها --}}
    <div style="display:flex;justify-content:center;gap:0.4rem;direction:ltr;margin-bottom:1rem;">
        @for($i = 1; $i <= 5; $i++)
        <svg width="32" height="32" viewBox="0 0 24 24" fill="{{ $i <= $existing->rating ? 'var(--pine)' : 'none' }}" stroke="{{ $i <= $existing->rating ? 'var(--pine)' : 'var(--pine-bright)' }}" stroke-width="1.5">
            <path d="M12 2l2.6 6.6L21 9.2l-5 4.5 1.5 7L12 17l-5.5 3.7L8 13.7l-5-4.5 6.4-.6z"/>
        </svg>
        @endfor
    </div>

    @if($existing->comment)
        <div style="background:var(--surface);border:1px solid var(--border);border-radius:12px;padding:1rem;line-height:1.8;color:var(--ink);">
            {{ $existing->comment }}
        </div>
    @endif

    @if($existing->admin_reply)
        <div style="margin-top:1rem;background:rgba(93,202,143,0.08);border:1px solid rgba(93,202,143,0.25);border-radius:12px;padding:1rem;">
            <div style="font-size:0.72rem;color:var(--pine);margin-bottom:5px;">پاسخ مدیریت</div>
            <div style="line-height:1.8;color:var(--ink);">{{ $existing->admin_reply }}</div>
        </div>
    @endif

    <div style="text-align:center;color:var(--ink-faint);font-size:0.8rem;margin-top:1rem;">بازخورد شما ثبت شده است</div>
</div>
@endsection
