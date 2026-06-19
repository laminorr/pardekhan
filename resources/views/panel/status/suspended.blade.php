@extends('panel.layouts.auth')
@section('title', 'حساب تعلیق شده')

@section('content')
<div class="auth-card">
    <div class="status-box">
        <div class="status-icon reject">
            <svg width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round">
                <rect x="5" y="11" width="14" height="10" rx="2"/><path d="M8 11V7a4 4 0 0 1 8 0v4"/>
            </svg>
        </div>
        <h2>حساب شما موقتاً تعلیق شده است</h2>
        <p>برای اطلاعات بیشتر با مدیریت در تماس باشید.</p>
    </div>
    <form method="POST" action="{{ route('panel.logout') }}">
        @csrf
        <button type="submit" class="btn btn-ghost">خروج</button>
    </form>
</div>
@endsection
