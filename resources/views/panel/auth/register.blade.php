@extends('panel.layouts.auth')
@section('title', 'ثبت‌نام')

@section('content')
<div class="auth-card">
    <h2>ثبت‌نام</h2>
    <p class="lead">به باشگاه اعضای پرده‌خوان بپیوندید</p>

    @if ($errors->any())
        <div class="alert alert-danger">
            @foreach ($errors->all() as $error)<div>{{ $error }}</div>@endforeach
        </div>
    @endif

    <form method="POST" action="{{ route('panel.register') }}">
        @csrf
        <div class="field">
            <label>نام</label>
            <input type="text" name="first_name" value="{{ old('first_name') }}" required placeholder="نام شما">
        </div>
        <div class="field">
            <label>نام خانوادگی</label>
            <input type="text" name="last_name" value="{{ old('last_name') }}" required placeholder="نام خانوادگی شما">
        </div>
        <div class="field">
            <label>شماره موبایل</label>
            <input type="tel" name="phone" value="{{ old('phone') }}" required placeholder="09xxxxxxxxx" style="direction:ltr;text-align:right;">
        </div>
        <div class="field">
            <label>رمز عبور</label>
            <input type="password" name="password" required placeholder="حداقل ۸ کاراکتر">
        </div>
        <button type="submit" class="btn btn-gold">ثبت‌نام و دریافت کد تایید</button>
    </form>

    <div class="auth-foot">
        قبلاً عضو شده‌اید؟ <a href="{{ route('panel.login') }}">ورود</a>
    </div>
</div>
@endsection
