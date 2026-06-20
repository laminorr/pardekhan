@extends('panel.layouts.app')
@section('title', $recipient->broadcast->subject)

@section('content')
<div class="page-head" style="display:flex;align-items:center;gap:0.75rem;">
    <a href="{{ route('panel.messages.index') }}" class="icon-btn" style="flex-shrink:0;">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M9 18l6-6-6-6"/></svg>
    </a>
    <div class="page-title" style="font-size:1.2rem;">پیام</div>
</div>

@if (session('error'))
    <div class="alert alert-danger">{{ session('error') }}</div>
@endif

<div class="card card-gold">
    <div style="font-size:1.1rem;font-weight:700;color:#fff;margin-bottom:0.5rem;">{{ $recipient->broadcast->subject }}</div>
    <div style="font-size:0.7rem;color:var(--text-dim);margin-bottom:1rem;">
        مدیریت پرده‌خوان · {{ \Morilog\Jalali\Jalalian::fromDateTime($recipient->broadcast->created_at)->format('Y/m/d H:i') }}
    </div>
    <div style="line-height:1.9;color:var(--text);">{!! nl2br(e($recipient->broadcast->body)) !!}</div>
</div>

{{-- اگه قابل پاسخ بود --}}
@if($recipient->broadcast->is_replyable)
    @if($conversation)
        {{-- قبلاً جواب داده، برو به گفتگو --}}
        <a href="{{ route('panel.messages.conversation', $conversation) }}" class="btn btn-ghost">
            مشاهده گفتگو
        </a>
    @else
        <div class="card">
            <div style="font-size:0.85rem;color:var(--text-dim);margin-bottom:0.75rem;">پاسخ شما (فقط مدیریت می‌بیند)</div>
            <form method="POST" action="{{ route('panel.messages.broadcast.reply', $recipient) }}">
                @csrf
                <div class="field">
                    <textarea name="body" rows="3" required placeholder="پاسخ خود را بنویسید..."
                        style="width:100%;background:#0d0d0f;border:1px solid var(--border);border-radius:13px;padding:0.85rem 1rem;color:var(--text);font-family:inherit;resize:vertical;"></textarea>
                </div>
                <button type="submit" class="btn btn-gold">ارسال پاسخ</button>
            </form>
        </div>
    @endif
@else
    <div style="text-align:center;padding:1rem;color:var(--text-faint);font-size:0.82rem;">این پیام فقط اطلاع‌رسانی است</div>
@endif
@endsection
