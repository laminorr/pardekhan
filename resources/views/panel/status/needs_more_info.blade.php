@extends('panel.layouts.auth')
@section('title', 'نیاز به اطلاعات بیشتر')

@section('content')
<div class="auth-card">
    <div class="status-box" style="padding-bottom:0.5rem;">
        <div class="status-icon wait">
            <svg width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round">
                <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>
            </svg>
        </div>
        <h2>پیام از مدیریت</h2>
    </div>

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @foreach($member->messages()->where('type','interactive')->whereNull('member_reply')->get() as $msg)
    <div style="background:#0d0d0f;border:1px solid var(--border);border-radius:14px;padding:1rem;margin-bottom:1rem;">
        <p style="color:var(--text);line-height:1.8;font-size:0.9rem;">{{ $msg->body }}</p>
        <form method="POST" action="/panel/messages/{{ $msg->id }}/reply" style="margin-top:1rem;">
            @csrf
            <div class="field">
                <textarea name="reply" rows="3" style="width:100%;background:#0d0d0f;border:1px solid var(--border);border-radius:13px;padding:0.85rem 1rem;color:var(--text);font-family:inherit;resize:vertical;" placeholder="پاسخ خود را بنویسید..." required></textarea>
            </div>
            <button type="submit" class="btn btn-gold">ارسال پاسخ</button>
        </form>
    </div>
    @endforeach

    <form method="POST" action="{{ route('panel.logout') }}">
        @csrf
        <button type="submit" class="btn btn-ghost">خروج</button>
    </form>
</div>
@endsection
