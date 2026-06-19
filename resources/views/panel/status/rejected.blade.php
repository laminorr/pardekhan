@extends('panel.layouts.auth')
@section('title', 'درخواست رد شد')

@section('content')
<div class="auth-card">
    <div class="status-box">
        <div class="status-icon reject">
            <svg width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="12" cy="12" r="9"/><path d="M15 9l-6 6M9 9l6 6"/>
            </svg>
        </div>
        <h2>متأسفانه اکانت شما تایید نشد</h2>
        <p>در صورت داشتن سوال با ما در تماس باشید.</p>
    </div>
    <form method="POST" action="{{ route('panel.logout') }}">
        @csrf
        <button type="submit" class="btn btn-ghost">خروج</button>
    </form>
</div>
@endsection
