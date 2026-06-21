@extends('panel.layouts.auth')
@section('title', 'ورود')

@section('content')
<div class="auth-card">
    <h2>ورود</h2>
    <p class="lead">به حساب کاربری خود وارد شوید</p>

    @if ($errors->any())
        <div class="alert alert-danger">
            @foreach ($errors->all() as $error)<div>{{ $error }}</div>@endforeach
        </div>
    @endif

    <form method="POST" action="{{ route('panel.login') }}">
        @csrf
        <div class="field">
            <label>شماره موبایل</label>
            <input type="tel" name="phone" value="{{ old('phone') }}" required placeholder="09xxxxxxxxx" style="direction:ltr;text-align:right;">
        </div>
        <div class="field">
            <label>رمز عبور</label>
            <input type="password" name="password" required placeholder="رمز عبور">
        </div>
        <label style="display:flex;align-items:center;gap:0.5rem;color:var(--text-dim);font-size:0.85rem;margin-bottom:0.5rem;cursor:pointer;">
            <input type="checkbox" name="remember" style="width:auto;"> مرا به خاطر بسپار
        </label>
        <button type="submit" class="btn btn-gold">ورود</button>
    </form>

    <div style="text-align:center;margin-top:1rem;">
        <a href="{{ route('panel.login.otp') }}" style="font-size:0.85rem;color:var(--pine);text-decoration:none;font-weight:600;">رمزتان را فراموش کرده‌اید؟ ورود با کد</a>
    </div>

    <div class="auth-foot">
        عضو نیستید؟ <a href="{{ route('panel.register') }}">ثبت‌نام</a>
    </div>
</div>
@endsection
