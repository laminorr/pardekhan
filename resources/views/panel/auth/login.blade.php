@extends('panel.layouts.app')
@section('title', 'ورود')

@section('content')
<div class="panel-card">
    <h2>ورود به پرده‌خوان</h2>

    @if ($errors->any())
        <div style="color:#ef4444;background:#1f0000;border:1px solid #7f1d1d;border-radius:8px;padding:0.75rem 1rem;margin-bottom:1rem;font-size:0.85rem;">
            @foreach ($errors->all() as $error)
                <div>{{ $error }}</div>
            @endforeach
        </div>
    @endif

    <form method="POST" action="{{ route('panel.login') }}">
        @csrf
        <div class="form-group">
            <label>شماره موبایل</label>
            <input type="tel" name="phone" value="{{ old('phone') }}" required placeholder="09xxxxxxxxx" style="direction:ltr;text-align:right;">
        </div>
        <div class="form-group">
            <label>رمز عبور</label>
            <input type="password" name="password" required placeholder="رمز عبور">
        </div>
        <div style="margin-bottom:1rem;">
            <label style="display:flex;align-items:center;gap:0.5rem;color:#aaa;font-size:0.85rem;cursor:pointer;">
                <input type="checkbox" name="remember" style="width:auto;"> مرا به خاطر بسپار
            </label>
        </div>
        <button type="submit" class="btn btn-primary">ورود</button>
    </form>
</div>

<div class="panel-link">
    عضو نیستید؟ <a href="{{ route('panel.register') }}">ثبت‌نام</a>
</div>
@endsection
