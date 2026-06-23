@extends('panel.layouts.auth')
@section('title', 'ورود با کد')

@section('content')
<div class="auth-card">
    <h2>ورود با کد یک‌بارمصرف</h2>
    <p class="lead">شماره موبایل خود را وارد کنید تا کد ورود برایتان ارسال شود. نیازی به رمز عبور نیست.</p>

    @if (session('status'))
        <div class="alert alert-success">{{ session('status') }}</div>
    @endif
    @if ($errors->any())
        <div class="alert alert-danger">
            @foreach ($errors->all() as $error)<div>{{ $error }}</div>@endforeach
        </div>
    @endif

    <form method="POST" action="{{ route('panel.login.otp') }}">
        @csrf
        <div class="field">
            <label>شماره موبایل</label>
            <input type="tel" name="phone" value="{{ old('phone') }}" required placeholder="09xxxxxxxxx" style="direction:ltr;text-align:right;">
        </div>
        <button type="submit" class="btn btn-primary">ارسال کد ورود</button>
    </form>

    <div class="auth-foot">
        رمزتان را به یاد آوردید؟ <a href="{{ route('panel.login') }}">ورود با رمز</a>
    </div>
</div>
@endsection
