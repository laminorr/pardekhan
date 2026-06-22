@extends('panel.layouts.app')
@section('title', $conversation->subject)

@section('content')
<div class="page-head" style="display:flex;align-items:center;gap:0.75rem;">
    <a href="{{ route('panel.messages.index') }}" class="icon-btn" style="flex-shrink:0;">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M9 18l6-6-6-6"/></svg>
    </a>
    <div class="page-title" style="font-size:1.1rem;">{{ $conversation->subject }}</div>
</div>

@if (session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif
@if (session('error'))
    <div class="alert alert-danger">{{ session('error') }}</div>
@endif

{{-- پیام‌ها --}}
<div style="display:flex;flex-direction:column;gap:0.85rem;margin-bottom:1.5rem;">
    @foreach($conversation->messages as $msg)
        @if($msg->sender_type === 'member')
            <div style="display:flex;justify-content:flex-start;">
                <div style="max-width:80%;background:rgba(212,175,106,0.1);border:1px solid var(--border);border-radius:14px;border-top-right-radius:4px;padding:0.85rem 1rem;">
                    <div style="line-height:1.7;color:var(--ink);">{{ $msg->body }}</div>
                    <div style="font-size:0.65rem;color:var(--ink-faint);margin-top:5px;">{{ $msg->created_at->format('H:i') }}</div>
                </div>
            </div>
        @else
            <div style="display:flex;justify-content:flex-end;">
                <div style="max-width:80%;background:var(--surface);border:1px solid var(--border);border-radius:14px;border-top-left-radius:4px;padding:0.85rem 1rem;">
                    <div style="font-size:0.68rem;color:var(--pine);margin-bottom:4px;">مدیریت</div>
                    <div style="line-height:1.7;color:var(--ink);">{{ $msg->body }}</div>
                    <div style="font-size:0.65rem;color:var(--ink-faint);margin-top:5px;">{{ $msg->created_at->format('H:i') }}</div>
                </div>
            </div>
        @endif
    @endforeach
</div>

{{-- فرم پاسخ --}}
@if($conversation->status === 'open')
<div class="card">
    <form method="POST" action="{{ route('panel.messages.conversation.reply', $conversation) }}">
        @csrf
        <div class="field">
            <textarea name="body" rows="2" required placeholder="پیام خود را بنویسید..."
                style="width:100%;background:var(--surface);border:1px solid var(--border);border-radius:13px;padding:0.85rem 1rem;color:var(--ink);font-family:inherit;resize:vertical;"></textarea>
        </div>
        <button type="submit" class="btn btn-primary">ارسال</button>
    </form>
</div>
@else
<div style="text-align:center;padding:1rem;color:var(--ink-faint);font-size:0.82rem;">این گفتگو بسته شده است</div>
@endif
@endsection
