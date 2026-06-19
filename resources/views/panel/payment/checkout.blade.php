@extends('panel.layouts.app')
@section('title', 'پرداخت')

@section('content')
<div class="page-head" style="display:flex;align-items:center;gap:0.75rem;">
    <a href="{{ route('panel.events.show', $event) }}" class="icon-btn" style="flex-shrink:0;">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M9 18l6-6-6-6"/></svg>
    </a>
    <div class="page-title" style="font-size:1.2rem;">پرداخت و ثبت‌نام</div>
</div>

@if (session('error'))
    <div class="alert alert-danger">{{ session('error') }}</div>
@endif

{{-- خلاصه --}}
<div class="card card-gold">
    <div style="font-size:0.95rem;font-weight:700;color:#fff;margin-bottom:0.5rem;">{{ $event->title }}</div>
    <div style="display:flex;justify-content:space-between;align-items:center;">
        <span style="color:var(--text-dim);font-size:0.85rem;">مبلغ قابل پرداخت</span>
        <span style="color:var(--gold-1);font-size:1.4rem;font-weight:800;">{{ number_format($price) }} <small style="font-size:0.7rem;color:var(--text-dim);font-weight:400;">تومان</small></span>
    </div>
</div>

<div class="section-head"><div class="section-title"><span class="bar"></span> روش پرداخت</div></div>

{{-- کیف پول --}}
<div class="card">
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:0.85rem;">
        <div style="display:flex;align-items:center;gap:0.6rem;">
            <div style="width:40px;height:40px;border-radius:12px;background:rgba(212,175,106,0.09);border:1px solid var(--gold-border);display:flex;align-items:center;justify-content:center;color:var(--gold-1);">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="5" width="20" height="14" rx="2.5"/><path d="M2 10h20"/></svg>
            </div>
            <div>
                <div style="font-weight:700;color:#fff;font-size:0.92rem;">کیف پول</div>
                <div style="font-size:0.75rem;color:var(--text-dim);">موجودی: {{ number_format($walletBalance) }} تومان</div>
            </div>
        </div>
    </div>
    @if($canUseWallet)
        <form method="POST" action="{{ route('panel.payment.wallet', $event) }}">
            @csrf
            <button type="submit" class="btn btn-gold">پرداخت از کیف پول</button>
        </form>
    @else
        <div style="text-align:center;padding:0.7rem;background:#0d0d0f;border-radius:10px;color:var(--text-faint);font-size:0.82rem;">
            موجودی کافی نیست
        </div>
    @endif
</div>

{{-- کارت به کارت --}}
@if($cardToCardEnabled && $cardNumber)
<div class="card">
    <div style="display:flex;align-items:center;gap:0.6rem;margin-bottom:1rem;">
        <div style="width:40px;height:40px;border-radius:12px;background:rgba(212,175,106,0.09);border:1px solid var(--gold-border);display:flex;align-items:center;justify-content:center;color:var(--gold-1);">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="5" width="20" height="14" rx="2.5"/><path d="M2 10h20"/><path d="M6 15h4"/></svg>
        </div>
        <div style="font-weight:700;color:#fff;font-size:0.92rem;">کارت به کارت</div>
    </div>

    <div style="background:#0d0d0f;border:1px solid var(--border);border-radius:12px;padding:1rem;margin-bottom:1rem;text-align:center;">
        <div style="font-size:0.75rem;color:var(--text-dim);margin-bottom:0.4rem;">شماره کارت</div>
        <div style="font-size:1.2rem;font-weight:700;color:var(--gold-1);direction:ltr;letter-spacing:2px;">{{ $cardNumber }}</div>
        @if($cardHolder)<div style="font-size:0.8rem;color:var(--text-dim);margin-top:0.4rem;">{{ $cardHolder }}</div>@endif
    </div>

    <p style="font-size:0.8rem;color:var(--text-dim);line-height:1.7;margin-bottom:1rem;">
        لطفاً مبلغ <strong style="color:var(--gold-1);">{{ number_format($price) }} تومان</strong> را به کارت بالا واریز کرده و شماره پیگیری را وارد کنید.
    </p>

    <form method="POST" action="{{ route('panel.payment.card', $event) }}">
        @csrf
        <div class="field">
            <label>شماره پیگیری / کد رهگیری</label>
            <input type="text" name="tracking_number" required placeholder="شماره پیگیری تراکنش" style="direction:ltr;text-align:right;">
        </div>
        <button type="submit" class="btn btn-gold">ثبت پرداخت</button>
    </form>
    <p style="font-size:0.72rem;color:var(--text-faint);text-align:center;margin-top:0.6rem;">ثبت‌نام شما قطعی می‌شود و پرداخت پس از بررسی تایید می‌گردد</p>
</div>
@endif

{{-- درگاه (فعلاً غیرفعال) --}}
@if($gatewayEnabled)
<div class="card">
    <a href="#" class="btn btn-ghost">پرداخت با درگاه بانکی</a>
</div>
@endif
@endsection
