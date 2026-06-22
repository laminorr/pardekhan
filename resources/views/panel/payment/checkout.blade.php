@extends('panel.layouts.app')
@section('title', 'پرداخت')

@section('content')
{{-- هدر --}}
<div class="page-head">
    <a href="{{ route('panel.events.show', $event) }}" class="icon-btn" style="flex-shrink:0;">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 6l6 6-6 6"/></svg>
    </a>
    <div class="page-title">پرداخت</div>
</div>

@if (session('error'))
    <div class="alert alert-danger">{{ session('error') }}</div>
@endif

{{-- خلاصه سفارش --}}
<div style="border:1px solid var(--border);border-radius:20px;padding:1.15rem;background:#fff;box-shadow:0 2px 14px rgba(40,60,50,0.04);">
    <div style="display:flex;justify-content:space-between;align-items:center;">
        <span style="font-size:0.92rem;font-weight:700;">{{ $event->title }}</span>
        <span style="font-size:0.76rem;color:var(--ink-dim);">۱ بلیت</span>
    </div>
    <div style="height:1px;background:var(--bg-mute);margin:0.9rem 0;"></div>
    <div style="display:flex;justify-content:space-between;font-size:0.84rem;color:var(--ink-mid);margin-bottom:0.5rem;">
        <span>قیمت پایه</span><span>{{ fa(number_format($basePrice)) }} تومان</span>
    </div>
    @if($discountAmount > 0)
    <div style="display:flex;justify-content:space-between;font-size:0.84rem;color:var(--pine);font-weight:700;margin-bottom:0.5rem;">
        <span>تخفیف لایهٔ {{ auth('member')->user()->layer?->name }} ({{ fa($discount) }}٪)</span>
        <span>{{ fa(number_format($discountAmount)) }}−</span>
    </div>
    @endif
    <div style="height:1px;background:var(--bg-mute);margin:0.8rem 0;"></div>
    <div style="display:flex;justify-content:space-between;align-items:baseline;">
        <span style="font-size:0.9rem;font-weight:800;">مبلغ قابل پرداخت</span>
        <span style="font-size:1.3rem;font-weight:800;color:var(--pine);letter-spacing:-0.5px;">{{ fa(number_format($price)) }}</span>
    </div>
</div>

<div style="font-size:0.95rem;font-weight:800;margin-top:1.5rem;margin-bottom:0.9rem;">روش پرداخت</div>

{{-- کیف پول --}}
<div style="border:{{ $canUseWallet ? '2px solid var(--pine)' : '1px solid var(--border)' }};border-radius:18px;padding:0.95rem 1rem;background:{{ $canUseWallet ? '#f4f8f6' : '#fff' }};display:flex;align-items:center;gap:0.8rem;{{ $canUseWallet ? '' : 'opacity:0.6;' }}">
    <div style="width:42px;height:42px;border-radius:12px;background:#fff;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="var(--pine)" stroke-width="1.6"><path d="M4 8h13a2 2 0 0 1 2 2v6a2 2 0 0 1-2 2H4z"/><path d="M4 8V6.5A1.5 1.5 0 0 1 5.5 5H16"/><circle cx="16" cy="13" r="1.3" fill="var(--pine)" stroke="none"/></svg>
    </div>
    <div style="flex:1;">
        <div style="font-size:0.9rem;font-weight:700;">کیف پول پرده‌خوان</div>
        <div style="font-size:0.76rem;color:var(--ink-dim);margin-top:2px;">موجودی: {{ fa(number_format($walletBalance)) }} تومان</div>
    </div>
    @if($canUseWallet)
        <span style="width:22px;height:22px;border-radius:50%;background:var(--pine);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12l5 5 9-10"/></svg>
        </span>
    @endif
</div>
@if($canUseWallet)
    <form method="POST" action="{{ route('panel.payment.wallet', $event) }}" style="margin-top:0.8rem;" onsubmit="return confirm('پرداخت {{ number_format($price) }} تومان از کیف پول؟');">
        @csrf
        <button type="submit" class="btn btn-primary">پرداخت از کیف پول</button>
    </form>
@else
    <div style="font-size:0.78rem;color:var(--burnt);margin-top:0.5rem;padding-right:0.3rem;">موجودی کافی نیست. از روش دیگری استفاده کنید یا کیف پول را شارژ کنید.</div>
@endif

{{-- کارت به کارت --}}
@if($cardToCardEnabled && $cardNumber)
<div style="margin-top:1rem;border:1px solid var(--border);border-radius:18px;padding:1.15rem;background:#fff;">
    <div style="display:flex;align-items:center;gap:0.8rem;margin-bottom:1rem;">
        <div style="width:42px;height:42px;border-radius:12px;background:var(--green-soft);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="var(--pine)" stroke-width="1.6"><rect x="2" y="5" width="20" height="14" rx="2.5"/><path d="M2 10h20"/></svg>
        </div>
        <div style="font-size:0.9rem;font-weight:700;">کارت به کارت</div>
    </div>

    <div style="background:var(--bg-soft);border-radius:14px;padding:1rem;text-align:center;">
        <div style="font-size:0.72rem;color:var(--ink-dim);margin-bottom:0.4rem;">شماره کارت</div>
        <div style="font-size:1.2rem;font-weight:700;color:var(--pine);direction:ltr;letter-spacing:2px;">{{ $cardNumber }}</div>
        @if($cardHolder)<div style="font-size:0.78rem;color:var(--ink-dim);margin-top:0.4rem;">{{ $cardHolder }}</div>@endif
    </div>

    <div style="font-size:0.8rem;color:var(--ink-mid);margin:1rem 0;line-height:1.9;text-align:justify;">
        لطفاً مبلغ <strong style="color:var(--pine);">{{ fa(number_format($price)) }} تومان</strong> را به کارت بالا واریز کرده و شمارهٔ پیگیری را وارد کنید.
    </div>

    <form method="POST" action="{{ route('panel.payment.card', $event) }}">
        @csrf
        <div class="field">
            <label>شمارهٔ پیگیری / کد رهگیری</label>
            <input type="text" name="tracking_number" required placeholder="مثلاً ۱۲۳۴۵۶" style="direction:ltr;text-align:right;">
        </div>
        <button type="submit" class="btn btn-primary">ثبت پرداخت و انتظار تایید</button>
    </form>
</div>
@endif

<div style="height:1.5rem;"></div>
@endsection
