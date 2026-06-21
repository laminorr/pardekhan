@extends('panel.layouts.auth')
@section('title', 'تایید شماره موبایل')

@section('content')
<div class="auth-card">
    <h2>تایید شماره موبایل</h2>
    <p class="lead">کد ۶ رقمی به شماره <strong style="color:var(--pine);direction:ltr;display:inline-block;">{{ $phone }}</strong> ارسال شد.</p>

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if ($errors->any())
        <div class="alert alert-danger">
            @foreach ($errors->all() as $error)<div>{{ $error }}</div>@endforeach
        </div>
    @endif

    <form method="POST" action="{{ route('panel.otp') }}">
        @csrf
        <div class="field">
            <label>کد تایید</label>
            <input type="text" name="code" required placeholder="------" maxlength="6"
                style="direction:ltr;text-align:center;font-size:1.6rem;letter-spacing:10px;font-weight:700;">
        </div>
        <button type="submit" class="btn btn-gold">تایید کد</button>
    </form>

    <form method="POST" action="{{ route('panel.otp.resend') }}">
        @csrf
        <button type="submit" class="btn btn-ghost">ارسال مجدد کد</button>
    </form>
</div>
@endsection
