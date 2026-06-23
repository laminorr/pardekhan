@extends('panel.layouts.app')
@section('title', $conversation->subject)

@section('content')
{{-- هدر گفتگو --}}
<div style="display:flex;align-items:center;gap:0.75rem;margin-bottom:1.3rem;">
    <a href="{{ route('panel.messages.index') }}" class="icon-btn" style="flex-shrink:0;">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 6l6 6-6 6"/></svg>
    </a>
    <div style="width:40px;height:40px;border-radius:13px;background:linear-gradient(135deg,var(--pine),var(--pine-bright));display:flex;align-items:center;justify-content:center;font-size:0.95rem;font-weight:800;color:#fff;flex-shrink:0;">پ</div>
    <div style="flex:1;min-width:0;">
        <div style="font-size:0.98rem;font-weight:800;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">{{ $conversation->subject }}</div>
        <div style="font-size:0.72rem;color:var(--ink-faint);">مدیریت پرده‌خوان</div>
    </div>
</div>

@if (session('error'))
    <div class="alert alert-danger">{{ session('error') }}</div>
@endif

{{-- پیام‌ها (چت) --}}
<div style="display:flex;flex-direction:column;gap:0.6rem;margin-bottom:1.5rem;">
    @foreach($conversation->messages as $msg)
        @if($msg->sender_type === 'member')
            {{-- پیام کاربر - راست --}}
            <div style="display:flex;justify-content:flex-start;">
                <div style="max-width:78%;background:var(--green-soft);border-radius:16px;border-top-right-radius:5px;padding:0.7rem 0.9rem;">
                    <div style="font-size:0.86rem;line-height:1.75;color:var(--ink);">{{ $msg->body }}</div>
                    <div style="font-size:0.6rem;color:var(--pine);opacity:0.6;margin-top:4px;text-align:left;">{{ fa($msg->created_at->format('H:i')) }}</div>
                </div>
            </div>
        @else
            {{-- پاسخ ادمین - چپ --}}
            <div style="display:flex;justify-content:flex-end;">
                <div style="max-width:78%;background:#fff;border:1px solid var(--border);border-radius:16px;border-top-left-radius:5px;padding:0.7rem 0.9rem;">
                    <div style="font-size:0.62rem;color:var(--pine);font-weight:700;margin-bottom:3px;">مدیریت</div>
                    <div style="font-size:0.86rem;line-height:1.75;color:var(--ink);">{{ $msg->body }}</div>
                    <div style="font-size:0.6rem;color:var(--ink-faint);margin-top:4px;text-align:left;">{{ fa($msg->created_at->format('H:i')) }}</div>
                </div>
            </div>
        @endif
    @endforeach
</div>
<div style="height:1rem;"></div>
@endsection

@section('nav')
{{-- نوار پاسخ ثابت پایین --}}
@if($conversation->status === 'open')
<div style="position:fixed;bottom:0;left:50%;transform:translateX(-50%);width:100%;max-width:430px;background:rgba(252,252,251,0.97);backdrop-filter:blur(10px);border-top:1px solid var(--border);padding:0.8rem 1rem calc(0.8rem + env(safe-area-inset-bottom));z-index:60;">
    <form method="POST" action="{{ route('panel.messages.conversation.reply', $conversation) }}" style="display:flex;align-items:flex-end;gap:0.6rem;">
        @csrf
        <textarea name="body" rows="1" required placeholder="پیام خود را بنویسید..."
            style="flex:1;background:#fff;border:1px solid var(--border);border-radius:22px;padding:0.7rem 1.1rem;color:var(--ink);font-family:inherit;font-size:0.88rem;resize:none;max-height:100px;line-height:1.6;"
            oninput="this.style.height='auto';this.style.height=this.scrollHeight+'px';"></textarea>
        <button type="submit" style="width:44px;height:44px;border-radius:50%;background:var(--pine);border:none;display:flex;align-items:center;justify-content:center;cursor:pointer;flex-shrink:0;box-shadow:0 4px 12px rgba(47,93,80,0.3);">
            <svg width="19" height="19" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 2L11 13M22 2l-7 20-4-9-9-4 20-7z"/></svg>
        </button>
    </form>
</div>
@else
<div style="position:fixed;bottom:0;left:50%;transform:translateX(-50%);width:100%;max-width:430px;background:var(--bg);border-top:1px solid var(--border);text-align:center;padding:1rem calc(1rem + env(safe-area-inset-bottom));color:var(--ink-faint);font-size:0.82rem;z-index:60;">این گفتگو بسته شده است</div>
@endif
@endsection
