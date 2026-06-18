@extends('panel.layouts.app')
@section('title', 'ثبت‌نام')

@section('content')
<div class="panel-card">
    <h2>ثبت‌نام در پرده‌خوان</h2>

    @if ($errors->any())
        <div style="color:#ef4444;background:#1f0000;border:1px solid #7f1d1d;border-radius:8px;padding:0.75rem 1rem;margin-bottom:1rem;font-size:0.85rem;">
            @foreach ($errors->all() as $error)
                <div>{{ $error }}</div>
            @endforeach
        </div>
    @endif

    <form method="POST" action="{{ route('panel.register') }}">
        @csrf
        <div class="form-group">
            <label>نام</label>
            <input type="text" name="first_name" value="{{ old('first_name') }}" required placeholder="نام خود را وارد کنید">
        </div>
        <div class="form-group">
            <label>نام خانوادگی</label>
            <input type="text" name="last_name" value="{{ old('last_name') }}" required placeholder="نام خانوادگی خود را وارد کنید">
        </div>
        <div class="form-group">
            <label>شماره موبایل</label>
            <input type="tel" name="phone" value="{{ old('phone') }}" required placeholder="09xxxxxxxxx" style="direction:ltr;text-align:right;">
        </div>
        <div class="form-group">
            <label>رمز عبور</label>
            <input type="password" name="password" required placeholder="حداقل ۸ کاراکتر">
        </div>
        <button type="submit" class="btn btn-primary">ثبت‌نام و دریافت کد تایید</button>
    </form>
</div>

<div class="panel-link">
    قبلاً عضو شده‌اید؟ <a href="{{ route('panel.login') }}">ورود</a>
</div>
@endsection
