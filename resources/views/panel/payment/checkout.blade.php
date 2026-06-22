@extends('panel.layouts.app')
@section('title', 'پرداخت')

@push('styles')
<style>
    .pay-option {
        border:1px solid var(--border); border-radius:18px; padding:0.95rem 1rem;
        background:#fff; display:flex; align-items:center; gap:0.8rem; cursor:pointer;
        transition:border-color 0.2s, background 0.2s; margin-top:0.8rem;
    }
    .pay-option.selected { border:2px solid var(--pine); background:#f4f8f6; }
    .pay-option.disabled { opacity:0.55; cursor:not-allowed; }
    .pay-radio { width:22px; height:22px; border-radius:50%; border:2px solid #d8dcda; flex-shrink:0; display:flex; align-items:center; justify-content:center; transition:0.2s; }
    .pay-option.selected .pay-radio { border-color:var(--pine); background:var(--pine); }
    .pay-detail {
        max-height:0; opacity:0; overflow:hidden;
        transition:max-height 0.4s ease, opacity 0.35s ease, margin 0.3s;
    }
    .pay-detail.open { max-height:600px; opacity:1; margin-top:0.9rem; }
</style>
@endpush

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

<div style="font-size:0.95rem;font-weight:800;margin-top:1.5rem;">روش پرداخت</div>

{{-- ۱. کیف پول --}}
<div class="pay-option {{ $canUseWallet ? '' : 'disabled' }}" id="opt-wallet" @if($canUseWallet) onclick="selectPay('wallet')" @endif>
    <div style="width:42px;height:42px;border-radius:12px;background:{{ $canUseWallet ? 'var(--green-soft)' : 'var(--bg-soft)' }};display:flex;align-items:center;justify-content:center;flex-shrink:0;">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="{{ $canUseWallet ? 'var(--pine)' : 'var(--ink-mid)' }}" stroke-width="1.6"><path d="M4 8h13a2 2 0 0 1 2 2v6a2 2 0 0 1-2 2H4z"/><path d="M4 8V6.5A1.5 1.5 0 0 1 5.5 5H16"/><circle cx="16" cy="13" r="1.3" fill="{{ $canUseWallet ? 'var(--pine)' : 'var(--ink-mid)' }}" stroke="none"/></svg>
    </div>
    <div style="flex:1;">
        <div style="font-size:0.9rem;font-weight:700;">کیف پول پرده‌خوان</div>
        <div style="font-size:0.76rem;color:var(--ink-dim);margin-top:2px;">موجودی: {{ fa(number_format($walletBalance)) }} تومان</div>
    </div>
    <span class="pay-radio">
        <svg class="tick" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" style="display:none;"><path d="M5 12l5 5 9-10"/></svg>
    </span>
</div>
@unless($canUseWallet)
    <div style="font-size:0.76rem;color:var(--burnt);margin-top:0.5rem;padding-right:0.3rem;">موجودی کافی نیست. کارت‌به‌کارت را انتخاب کنید یا کیف پول را شارژ کنید.</div>
@endunless

{{-- جزئیات کیف پول --}}
@if($canUseWallet)
<div class="pay-detail" id="detail-wallet">
    <form method="POST" action="{{ route('panel.payment.wallet', $event) }}" onsubmit="return confirm('پرداخت {{ number_format($price) }} تومان از کیف پول؟');">
        @csrf
        <button type="submit" class="btn btn-primary">پرداخت {{ fa(number_format($price)) }} تومان از کیف پول</button>
    </form>
</div>
@endif

{{-- ۲. کارت به کارت --}}
@if($cardToCardEnabled && $cardNumber)
<div class="pay-option" id="opt-card" onclick="selectPay('card')">
    <div style="width:42px;height:42px;border-radius:12px;background:var(--bg-soft);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="var(--ink-mid)" stroke-width="1.6"><rect x="3" y="6" width="18" height="13" rx="2.5"/><path d="M3 10h18"/></svg>
    </div>
    <div style="flex:1;">
        <div style="font-size:0.9rem;font-weight:700;">کارت به کارت</div>
        <div style="font-size:0.76rem;color:var(--ink-dim);margin-top:2px;">واریز و ثبت کد پیگیری</div>
    </div>
    <span class="pay-radio">
        <svg class="tick" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" style="display:none;"><path d="M5 12l5 5 9-10"/></svg>
    </span>
</div>

{{-- جزئیات کارت به کارت --}}
<div class="pay-detail" id="detail-card">
    {{-- کارت بانکی گرافیکی --}}
    <div style="position:relative;border-radius:20px;padding:1.3rem 1.3rem 1.2rem;background:linear-gradient(135deg,#34685a,#1f4538);overflow:hidden;box-shadow:0 16px 36px -20px rgba(47,93,80,0.7);">
        {{-- بافت تزئینی --}}
        <div style="position:absolute;top:-50px;left:-30px;width:150px;height:150px;border-radius:50%;background:rgba(255,255,255,0.05);"></div>
        <div style="position:absolute;bottom:-60px;right:-30px;width:160px;height:160px;border-radius:50%;background:rgba(255,255,255,0.04);"></div>

        {{-- ردیف بالا: چیپ + برچسب --}}
        <div style="position:relative;display:flex;justify-content:space-between;align-items:center;">
            <div style="width:38px;height:28px;border-radius:7px;background:linear-gradient(135deg,#e8d9a8,#c9b274);position:relative;overflow:hidden;">
                <div style="position:absolute;top:50%;left:0;right:0;height:1px;background:rgba(0,0,0,0.15);"></div>
                <div style="position:absolute;top:30%;bottom:30%;left:35%;width:1px;background:rgba(0,0,0,0.15);"></div>
                <div style="position:absolute;top:30%;bottom:30%;left:60%;width:1px;background:rgba(0,0,0,0.15);"></div>
            </div>
            <span style="font-size:0.68rem;color:rgba(234,243,239,0.7);letter-spacing:1px;">کارت مقصد</span>
        </div>

        {{-- شماره کارت --}}
        <div id="card-num" style="position:relative;font-size:1.3rem;font-weight:700;letter-spacing:3px;margin-top:1.1rem;direction:ltr;text-align:left;color:#fff;font-family:'Vazirmatn',monospace;">{{ $cardNumber }}</div>

        {{-- ردیف پایین: نام + کپی --}}
        <div style="position:relative;display:flex;justify-content:space-between;align-items:flex-end;margin-top:1rem;">
            <div>
                @if($cardHolder)
                    <div style="font-size:0.62rem;color:rgba(234,243,239,0.6);">به نام</div>
                    <div style="font-size:0.85rem;color:#fff;font-weight:600;margin-top:2px;">{{ $cardHolder }}</div>
                @endif
            </div>
            <button type="button" onclick="copyCard()" id="copy-btn" style="display:flex;align-items:center;gap:5px;font-size:0.72rem;color:#fff;font-weight:700;background:rgba(255,255,255,0.15);border:1px solid rgba(255,255,255,0.2);border-radius:10px;padding:6px 12px;cursor:pointer;backdrop-filter:blur(6px);">
                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="9" y="9" width="13" height="13" rx="2"/><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"/></svg>
                کپی
            </button>
        </div>
    </div>

    {{-- مبلغ و فیلد --}}
    <div style="margin-top:1rem;background:#fff;border:1px solid var(--border);border-radius:18px;padding:1.15rem;">
        <div style="display:flex;align-items:center;gap:8px;padding:0.7rem 0.9rem;background:var(--green-tint);border-radius:12px;font-size:0.82rem;color:var(--pine-deep);line-height:1.7;">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="var(--pine)" stroke-width="1.8" style="flex-shrink:0;"><circle cx="12" cy="12" r="9"/><path d="M12 8v5M12 16h.01" stroke-linecap="round"/></svg>
            <span>مبلغ <strong>{{ fa(number_format($price)) }} تومان</strong> را واریز و کد پیگیری را وارد کنید</span>
        </div>

        <form method="POST" action="{{ route('panel.payment.card', $event) }}">
            @csrf
            <div style="font-size:0.78rem;color:var(--ink-mid);font-weight:600;margin-top:1rem;margin-bottom:0.4rem;">شمارهٔ پیگیری / کد رهگیری</div>
            <input type="text" name="tracking_number" required placeholder="مثلاً ۱۲۳۴۵۶" style="width:100%;background:var(--bg-soft);border:1px solid var(--border);border-radius:12px;padding:0.9rem 1rem;font-family:inherit;direction:ltr;text-align:right;transition:border-color 0.2s;" onfocus="this.style.borderColor='var(--pine)';this.style.background='#fff';" onblur="this.style.borderColor='var(--border)';">
            <button type="submit" class="btn btn-primary" style="margin-top:1rem;">ثبت پرداخت و انتظار تایید</button>
        </form>
    </div>
</div>
@endif

<div style="height:1.5rem;"></div>
@endsection

@push('scripts')
<script>
    function selectPay(which) {
        ['wallet', 'card'].forEach(function (k) {
            var opt = document.getElementById('opt-' + k);
            var detail = document.getElementById('detail-' + k);
            if (!opt) return;
            if (k === which) {
                opt.classList.add('selected');
                if (detail) detail.classList.add('open');
                var tick = opt.querySelector('.tick');
                if (tick) tick.style.display = 'block';
            } else {
                opt.classList.remove('selected');
                if (detail) detail.classList.remove('open');
                var tick = opt.querySelector('.tick');
                if (tick) tick.style.display = 'none';
            }
        });
    }
    function copyCard() {
        var num = document.getElementById('card-num').innerText.replace(/\s/g, '');
        navigator.clipboard.writeText(num).then(function () {
            var btn = document.getElementById('copy-btn');
            var original = btn.innerHTML;
            btn.innerHTML = 'کپی شد ✓';
            setTimeout(function () { btn.innerHTML = original; }, 1500);
        });
    }
    // پیش‌فرض: اگر کیف پول قابل استفاده است انتخابش کن، وگرنه کارت
    document.addEventListener('DOMContentLoaded', function () {
        @if($canUseWallet)
            selectPay('wallet');
        @elseif($cardToCardEnabled && $cardNumber)
            selectPay('card');
        @endif
    });
</script>
@endpush
