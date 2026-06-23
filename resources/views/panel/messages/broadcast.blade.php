@extends('panel.layouts.app')
@section('title', $recipient->broadcast->subject)

@section('content')
{{-- هدر --}}
<div style="display:flex;align-items:center;gap:0.75rem;margin-bottom:1.3rem;">
    <a href="{{ route('panel.messages.index') }}" class="icon-btn" style="flex-shrink:0;">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 6l6 6-6 6"/></svg>
    </a>
    <div style="font-size:1.05rem;font-weight:800;">اطلاعیه</div>
</div>

@if (session('error'))
    <div class="alert alert-danger">{{ session('error') }}</div>
@endif

{{-- متن اطلاعیه --}}
<div style="border:1px solid var(--border);border-radius:18px;padding:1.2rem;background:#fff;box-shadow:0 2px 14px rgba(40,60,50,0.04);">
    <div style="display:flex;align-items:center;gap:0.7rem;margin-bottom:0.9rem;">
        <div style="width:38px;height:38px;border-radius:12px;background:var(--green-soft);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
            <svg width="19" height="19" viewBox="0 0 24 24" fill="none" stroke="var(--pine)" stroke-width="1.6"><path d="M4 6h16v12H4z"/><path d="M4 7l8 6 8-6"/></svg>
        </div>
        <div>
            <div style="font-size:0.92rem;font-weight:800;">{{ $recipient->broadcast->subject }}</div>
            <div style="font-size:0.68rem;color:var(--ink-dim);margin-top:1px;">مدیریت پرده‌خوان · {{ fa(\Morilog\Jalali\Jalalian::fromDateTime($recipient->broadcast->created_at)->format('j F · H:i')) }}</div>
        </div>
    </div>
    <div style="font-size:0.88rem;line-height:1.95;color:var(--ink-mid);text-align:justify;">{!! nl2br(e($recipient->broadcast->body)) !!}</div>
</div>

{{-- پاسخ --}}
@if($recipient->broadcast->is_replyable)
    @if($conversation)
        <a href="{{ route('panel.messages.conversation', $conversation) }}" class="btn btn-primary" style="margin-top:1rem;">
            <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z"/></svg>
            مشاهدهٔ گفتگو
        </a>
    @else
        <div style="margin-top:1rem;border:1px solid var(--border);border-radius:18px;padding:1.2rem;background:#fff;">
            <div style="font-size:0.84rem;font-weight:700;color:var(--ink);margin-bottom:0.75rem;">پاسخ شما</div>
            <form method="POST" action="{{ route('panel.messages.broadcast.reply', $recipient) }}">
                @csrf
                <textarea name="body" rows="3" required placeholder="پاسخ خود را بنویسید..."
                    style="width:100%;background:var(--surface);border:1px solid var(--border);border-radius:14px;padding:0.85rem 1rem;color:var(--ink);font-family:inherit;font-size:0.88rem;resize:vertical;"></textarea>
                <button type="submit" class="btn btn-primary" style="margin-top:0.8rem;">ارسال پاسخ</button>
            </form>
        </div>
    @endif
@endif
@endsection

@section('nav')
    @include('panel.partials.bottom-nav', ['active' => 'home'])
@endsection
