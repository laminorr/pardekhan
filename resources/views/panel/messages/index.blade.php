@extends('panel.layouts.app')
@section('title', 'پیام‌ها')

@section('content')
<div class="page-head" style="display:flex;align-items:center;gap:0.75rem;">
    <a href="{{ route('panel.dashboard') }}" class="icon-btn" style="flex-shrink:0;">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M9 18l6-6-6-6"/></svg>
    </a>
    <div class="page-title" style="font-size:1.2rem;">پیام‌ها</div>
</div>

@if (session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif

{{-- دکمه پیام جدید --}}
<a href="{{ route('panel.messages.new') }}" class="btn btn-gold" style="margin-bottom:1.5rem;">
    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 5v14M5 12h14"/></svg>
    پیام جدید به مدیریت
</a>

{{-- گفتگوهای فعال --}}
@if($conversations->isNotEmpty())
<div class="section-head"><div class="section-title"><span class="bar"></span> گفتگوهای من</div></div>
@foreach($conversations as $conv)
<a href="{{ route('panel.messages.conversation', $conv) }}" class="card" style="display:block;text-decoration:none;color:inherit;margin-bottom:0.75rem;">
    <div style="display:flex;justify-content:space-between;align-items:flex-start;">
        <div style="flex:1;min-width:0;">
            <div style="font-weight:700;color:#fff;display:flex;align-items:center;gap:6px;">
                @if($conv->member_unread)<span style="width:8px;height:8px;border-radius:50%;background:var(--gold-2);flex-shrink:0;"></span>@endif
                {{ $conv->subject }}
            </div>
            @if($conv->latestMessage)
                <div style="font-size:0.8rem;color:var(--text-dim);margin-top:4px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">
                    {{ $conv->latestMessage->sender_type === 'admin' ? 'مدیریت: ' : 'شما: ' }}{{ Str::limit($conv->latestMessage->body, 40) }}
                </div>
            @endif
        </div>
        <span style="font-size:0.7rem;padding:3px 8px;border-radius:99px;flex-shrink:0;margin-right:0.5rem;
            {{ $conv->status === 'open' ? 'background:rgba(93,202,143,0.12);color:var(--success);' : 'background:rgba(255,255,255,0.05);color:var(--text-dim);' }}">
            {{ $conv->status === 'open' ? 'باز' : 'بسته' }}
        </span>
    </div>
</a>
@endforeach
@endif

{{-- پیام‌های دریافتی --}}
<div class="section-head"><div class="section-title"><span class="bar"></span> پیام‌های دریافتی</div></div>
@if($broadcasts->isEmpty())
    <div class="card" style="text-align:center;padding:2.5rem 1.5rem;">
        <p style="color:var(--text-dim);">پیامی ندارید.</p>
    </div>
@else
    @foreach($broadcasts as $recipient)
    @if($recipient->broadcast)
    <a href="{{ route('panel.messages.broadcast', $recipient) }}" class="card" style="display:block;text-decoration:none;color:inherit;margin-bottom:0.75rem;">
        <div style="display:flex;justify-content:space-between;align-items:flex-start;">
            <div style="flex:1;min-width:0;">
                <div style="font-weight:700;color:#fff;display:flex;align-items:center;gap:6px;">
                    @if(!$recipient->is_read)<span style="width:8px;height:8px;border-radius:50%;background:var(--gold-2);flex-shrink:0;"></span>@endif
                    {{ $recipient->broadcast->subject }}
                </div>
                <div style="font-size:0.8rem;color:var(--text-dim);margin-top:4px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">
                    {{ Str::limit($recipient->broadcast->body, 45) }}
                </div>
            </div>
            @if($recipient->broadcast->is_replyable)
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="var(--gold-2)" stroke-width="2" style="flex-shrink:0;margin-right:0.5rem;"><path d="M9 17l-5-5 5-5M4 12h12a4 4 0 0 1 0 8h-1"/></svg>
            @endif
        </div>
        <div style="font-size:0.7rem;color:var(--text-faint);margin-top:8px;">
            {{ \Morilog\Jalali\Jalalian::fromDateTime($recipient->broadcast->created_at)->format('Y/m/d H:i') }}
        </div>
    </a>
    @endif
    @endforeach
@endif
@endsection
