@extends('panel.layouts.auth')
@section('title', 'در انتظار بررسی')

@section('content')
<div class="auth-card">
    <div class="status-box">
        <div class="status-icon wait">
            <svg width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/>
            </svg>
        </div>
        <h2>اکانت شما در حال بررسی است</h2>
        <p>پس از بررسی اطلاعات، نتیجه از طریق پیامک اطلاع‌رسانی خواهد شد.</p>
    </div>
    <form method="POST" action="{{ route('panel.logout') }}">
        @csrf
        <button type="submit" class="btn btn-ghost">خروج</button>
    </form>
</div>
@endsection
