@extends('panel.layouts.app')
@section('title', 'پیام‌ها')

@section('content')
{{-- هدر --}}
<div style="display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:1.3rem;">
    <div>
        <div style="font-size:1.5rem;font-weight:800;letter-spacing:-0.5px;">پیام‌ها</div>
        <div style="font-size:0.82rem;color:var(--ink-faint);margin-top:3px;">گفتگو با مدیریت باشگاه</div>
    </div>
    <a href="{{ route('panel.dashboard') }}" class="icon-btn">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 6l6 6-6 6"/></svg>
    </a>
</div>

@if (session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif

{{-- دکمه پیام جدید --}}
<a href="{{ route('panel.messages.new') }}" class="btn btn-primary" style="margin-bottom:1.5rem;">
    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 5v14M5 12h14"/></svg>
    پیام جدید به مدیریت
</a>

{{-- گفتگوهای من --}}
@if($conversations->isNotEmpty())
    @foreach($conversations as $conv)
    <a href="{{ route('panel.messages.conversation', $conv) }}" style="display:flex;align-items:center;gap:0.85rem;padding:0.9rem 0;border-bottom:1px solid var(--bg-mute);text-decoration:none;color:inherit;">
        <div style="width:48px;height:48px;border-radius:16px;background:linear-gradient(135deg,var(--pine),var(--pine-bright));display:flex;align-items:center;justify-content:center;font-size:1.05rem;font-weight:800;color:#fff;flex-shrink:0;">پ</div>
        <div style="flex:1;min-width:0;">
            <div style="display:flex;justify-content:space-between;align-items:center;">
                <span style="font-size:0.9rem;font-weight:800;">{{ $conv->subject }}</span>
                @if($conv->latestMessage)
                    <span style="font-size:0.7rem;color:var(--ink-faint);flex-shrink:0;">{{ fa(\Morilog\Jalali\Jalalian::fromDateTime($conv->latestMessage->created_at)->format('H:i')) }}</span>
                @endif
            </div>
            @if($conv->latestMessage)
                <div style="font-size:0.78rem;color:var(--ink-mid);margin-top:4px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">
                    {{ $conv->latestMessage->sender_type === 'admin' ? 'مدیریت: ' : 'شما: ' }}{{ Str::limit($conv->latestMessage->body, 38) }}
                </div>
            @endif
        </div>
        @if($conv->member_unread)
            <span style="width:20px;height:20px;border-radius:50%;background:var(--pine);color:#fff;font-size:0.66rem;font-weight:700;display:flex;align-items:center;justify-content:center;flex-shrink:0;">۱</span>
        @endif
    </a>
    @endforeach
@endif

{{-- پیام‌های دریافتی (اطلاعیه‌ها) --}}
@if($broadcasts->isNotEmpty())
    @foreach($broadcasts as $recipient)
    @if($recipient->broadcast)
    <a href="{{ route('panel.messages.broadcast', $recipient) }}" style="display:flex;align-items:center;gap:0.85rem;padding:0.9rem 0;border-bottom:1px solid var(--bg-mute);text-decoration:none;color:inherit;">
        <div style="width:48px;height:48px;border-radius:16px;background:{{ $recipient->is_read ? '#eef1ef' : 'var(--green-soft)' }};display:flex;align-items:center;justify-content:center;flex-shrink:0;">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="{{ $recipient->is_read ? 'var(--ink-mid)' : 'var(--pine)' }}" stroke-width="1.6"><path d="M4 6h16v12H4z"/><path d="M4 7l8 6 8-6"/></svg>
        </div>
        <div style="flex:1;min-width:0;">
            <div style="display:flex;justify-content:space-between;align-items:center;">
                <span style="font-size:0.9rem;font-weight:{{ $recipient->is_read ? '700' : '800' }};">{{ $recipient->broadcast->subject }}</span>
                <span style="font-size:0.7rem;color:var(--ink-faint);flex-shrink:0;">{{ fa(\Morilog\Jalali\Jalalian::fromDateTime($recipient->broadcast->created_at)->format('j F')) }}</span>
            </div>
            <div style="font-size:0.78rem;color:{{ $recipient->is_read ? 'var(--ink-faint)' : 'var(--ink-mid)' }};margin-top:4px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">
                {{ Str::limit($recipient->broadcast->body, 42) }}
            </div>
        </div>
        @if(!$recipient->is_read)
            <span style="width:9px;height:9px;border-radius:50%;background:var(--burnt);flex-shrink:0;"></span>
        @endif
    </a>
    @endif
    @endforeach
@endif

@if($conversations->isEmpty() && $broadcasts->isEmpty())
    <div style="text-align:center;padding:3rem 1rem;color:var(--ink-dim);">
        <svg width="56" height="56" viewBox="0 0 24 24" fill="none" stroke="var(--ink-faint)" stroke-width="1.3" style="margin:0 auto 1rem;"><path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z"/></svg>
        <div style="font-size:0.95rem;">هنوز پیامی ندارید</div>
    </div>
@endif
@endsection

@section('nav')
    @include('panel.partials.bottom-nav', ['active' => 'home'])
@endsection
