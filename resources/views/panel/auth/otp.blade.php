@extends('panel.layouts.app')
@section('title', 'تایید شماره موبایل')

@section('content')
<div class="panel-card">
    <h2>تایید شماره موبایل</h2>

    <p style="color:#aaa;font-size:0.9rem;margin-bottom:1.5rem;line-height:1.7;">
        کد ۶ رقمی به شماره <strong style="color:#f59e0b;direction:ltr;display:inline-block;">{{ $phone }}</strong> ارسال شد.
    </p>

    @if (session('success'))
        <div class="success-msg">{{ session('success') }}</div>
    @endif

    @if ($errors->any())
        <div style="color:#ef4444;background:#1f0000;border:1px solid #7f1d1d;border-radius:8px;padding:0.75rem 1rem;margin-bottom:1rem;font-size:0.85rem;">
            @foreach ($errors->all() as $error)
                <div>{{ $error }}</div>
            @endforeach
        </div>
    @endif

    <form method="POST" action="{{ route('panel.otp') }}">
        @csrf
        <div class="form-group">
            <label>کد تایید</label>
            <input type="text" name="code" required placeholder="------" maxlength="6"
                style="direction:ltr;text-align:center;font-size:1.5rem;letter-spacing:8px;">
        </div>
        <button type="submit" class="btn btn-primary">تایید کد</button>
    </form>

    <form method="POST" action="{{ route('panel.otp.resend') }}" style="margin-top:0.75rem;">
        @csrf
        <button type="submit" class="btn btn-secondary">ارسال مجدد کد</button>
    </form>
</div>
@endsection
