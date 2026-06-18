@extends('panel.layouts.app')
@section('title', 'درخواست رد شد')

@section('content')
<div class="panel-card">
    <div class="status-icon">❌</div>
    <div class="status-text">
        <strong>متأسفانه اکانت شما تایید نشد</strong>
        <p style="margin-top:0.75rem;">در صورت داشتن سوال با ما در تماس باشید.</p>
    </div>
    <form method="POST" action="{{ route('panel.logout') }}" style="margin-top:2rem;">
        @csrf
        <button type="submit" class="btn btn-secondary">خروج</button>
    </form>
</div>
@endsection
