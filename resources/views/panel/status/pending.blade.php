@extends('panel.layouts.app')
@section('title', 'در انتظار بررسی')

@section('content')
<div class="panel-card">
    <div class="status-icon">⏳</div>
    <div class="status-text">
        <strong>اکانت شما در حال بررسی است</strong>
        <p style="margin-top:0.75rem;">پس از بررسی اطلاعات، نتیجه از طریق پیامک اطلاع‌رسانی خواهد شد.</p>
    </div>
    <form method="POST" action="{{ route('panel.logout') }}" style="margin-top:2rem;">
        @csrf
        <button type="submit" class="btn btn-secondary">خروج</button>
    </form>
</div>
@endsection
