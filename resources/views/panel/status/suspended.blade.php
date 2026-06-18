@extends('panel.layouts.app')
@section('title', 'حساب تعلیق شده')

@section('content')
<div class="panel-card">
    <div class="status-icon">🔒</div>
    <div class="status-text">
        <strong>حساب شما موقتاً تعلیق شده است</strong>
        <p style="margin-top:0.75rem;">برای اطلاعات بیشتر با مدیریت در تماس باشید.</p>
    </div>
    <form method="POST" action="{{ route('panel.logout') }}" style="margin-top:2rem;">
        @csrf
        <button type="submit" class="btn btn-secondary">خروج</button>
    </form>
</div>
@endsection
