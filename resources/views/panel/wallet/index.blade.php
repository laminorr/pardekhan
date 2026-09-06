@extends('panel.layouts.app')
@section('title', 'کیف پول')

@section('content')
{{-- هدر --}}
<div style="display:flex;align-items:flex-start;gap:0.75rem;margin-bottom:1.3rem;">
    <a href="{{ route('panel.dashboard') }}" class="icon-btn" style="flex-shrink:0;margin-top:2px;">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 6l6 6-6 6"/></svg>
    </a>
    <div style="font-size:1.5rem;font-weight:800;letter-spacing:-0.5px;">کیف پول</div>
</div>

{{-- کارت موجودی سبز --}}
<div style="border-radius:24px;padding:1.6rem 1.5rem;background:linear-gradient(140deg,var(--pine),#1f4538);color:var(--green-tint);position:relative;overflow:hidden;box-shadow:0 24px 48px -28px rgba(47,93,80,0.7);">
    <div style="position:absolute;top:-40px;left:-30px;width:160px;height:160px;border-radius:50%;background:rgba(255,255,255,0.06);"></div>
    <div style="position:absolute;bottom:-50px;right:-20px;width:140px;height:140px;border-radius:50%;background:rgba(255,255,255,0.05);"></div>
    <div style="position:relative;font-size:0.75rem;letter-spacing:1px;color:#a7ccc0;">موجودی قابل استفاده</div>
    <div style="position:relative;margin-top:0.6rem;display:flex;align-items:baseline;gap:8px;">
        <span style="font-size:2.5rem;font-weight:800;letter-spacing:-1px;">{{ fa(number_format($member->wallet_balance)) }}</span>
        <span style="font-size:0.88rem;color:#a7ccc0;">تومان</span>
    </div>
    <button onclick="document.getElementById('charge-box').scrollIntoView({behavior:'smooth'})" style="position:relative;margin-top:1.25rem;width:100%;background:var(--bg);color:var(--pine);border:none;font-family:inherit;font-size:0.9rem;font-weight:800;padding:0.85rem;border-radius:14px;cursor:pointer;display:flex;align-items:center;justify-content:center;gap:8px;">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="var(--pine)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 5v14M5 12h14"/></svg>
        شارژ کیف پول
    </button>
</div>

{{-- باکس شارژ --}}
<div id="charge-box" style="margin-top:1.25rem;">
    {{-- درگاه بانکی (غیرفعال) --}}
    <div style="border:1px solid var(--border);border-radius:18px;padding:1.1rem 1.15rem;background:#fff;display:flex;align-items:center;gap:0.85rem;opacity:0.65;">
        <div style="width:42px;height:42px;border-radius:12px;background:var(--bg-soft);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="var(--ink-mid)" stroke-width="1.6"><rect x="2" y="5" width="20" height="14" rx="2.5"/><path d="M2 10h20"/></svg>
        </div>
        <div style="flex:1;">
            <div style="font-size:0.9rem;font-weight:700;">شارژ از طریق درگاه بانکی</div>
            <div style="font-size:0.74rem;color:var(--ink-dim);margin-top:2px;">به‌زودی فعال می‌شود</div>
        </div>
        <span style="font-size:0.68rem;color:var(--ink-mid);background:var(--bg-mute);padding:4px 10px;border-radius:99px;">غیرفعال</span>
    </div>

    {{-- کارت به کارت --}}
    @if($cardNumber)
    <div style="margin-top:1rem;border:1px solid var(--border);border-radius:20px;padding:1.25rem;background:#fff;">
        <div style="font-size:0.95rem;font-weight:800;margin-bottom:0.4rem;">شارژ با کارت به کارت</div>
        <div style="font-size:0.8rem;color:var(--ink-mid);line-height:1.9;text-align:justify;">مبلغ دلخواه را به کارت زیر واریز کرده و سپس با زدن دکمهٔ «اطلاع پرداختی به ادمین»، مبلغ و شمارهٔ پیگیری را ثبت کنید تا پس از تأیید، کیف پولتان شارژ شود.</div>
        <div style="background:var(--bg-soft);border-radius:14px;padding:1rem;text-align:center;margin-top:1rem;">
            <div style="font-size:0.72rem;color:var(--ink-dim);margin-bottom:0.4rem;">شماره کارت</div>
            <div style="font-size:1.2rem;font-weight:700;color:var(--pine);direction:ltr;letter-spacing:2px;">{{ $cardNumber }}</div>
            @if($cardHolder)<div style="font-size:0.78rem;color:var(--ink-dim);margin-top:0.4rem;">{{ $cardHolder }}</div>@endif
        </div>

        {{-- پیام موفقیت (fallbackِ بدون JS: فلش پس از ثبت فرم) --}}
        <div id="report-inline-success" class="alert alert-success" style="margin-top:1rem;{{ session('payment_report_saved') ? '' : 'display:none;' }}">
            گزارش پرداخت ثبت شد؛ پس از تأیید مدیریت، کیف پول شما شارژ می‌شود.
        </div>

        {{-- لینکِ باز کردن پاپ‌آپ — با #hash کار می‌کند حتی بدون JS (تکنیک :target) --}}
        <a href="#report-modal" id="open-report-modal" class="btn btn-primary" style="margin-top:1rem;">اطلاع پرداختی به ادمین</a>
    </div>
    @endif
</div>

@if($cardNumber)
{{-- ─────────── پاپ‌آپِ اطلاع پرداختی (RTL، برند پرده‌خوان) ─────────── --}}
{{-- بدون JS: لینکِ #report-modal با :target پاپ‌آپ را باز می‌کند و فرم به‌صورت POST معمولی ثبت می‌شود. --}}
<div id="report-modal" class="report-modal">
    <a href="#charge-box" class="report-modal__backdrop" data-close-report aria-label="بستن"></a>
    <div class="report-modal__panel" role="dialog" aria-modal="true" aria-labelledby="report-modal-title">
        <div class="report-modal__head">
            <div id="report-modal-title" style="font-size:1.05rem;font-weight:800;">اطلاع پرداختی به ادمین</div>
            <a href="#charge-box" class="report-modal__close" data-close-report aria-label="بستن">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round"><path d="M18 6 6 18M6 6l12 12"/></svg>
            </a>
        </div>
        <div style="font-size:0.8rem;color:var(--ink-mid);line-height:1.9;margin-bottom:1rem;">مبلغ واریزی و شمارهٔ پیگیری تراکنش را وارد کنید تا پس از تأیید مدیریت، کیف پولتان شارژ شود.</div>

        <form method="POST" action="{{ route('panel.wallet.report-payment') }}" id="report-form">
            @csrf
            <div id="report-form-error" class="alert alert-danger" style="display:none;"></div>

            <div class="field">
                <label for="report-amount">مبلغ واریزی (تومان)</label>
                <input type="number" name="amount" id="report-amount" inputmode="numeric" min="1000" step="1000" required
                       value="{{ old('amount') }}" placeholder="مثلاً ۵۰۰۰۰">
            </div>
            <div class="field">
                <label for="report-tracking">شماره پیگیری</label>
                <input type="text" name="tracking_number" id="report-tracking" required
                       value="{{ old('tracking_number') }}" placeholder="شمارهٔ پیگیری تراکنش">
            </div>

            <div style="display:flex;gap:0.6rem;margin-top:0.4rem;">
                <button type="submit" class="btn btn-primary" id="report-submit" style="flex:1;">ارسال</button>
                <a href="#charge-box" class="btn btn-ghost" data-close-report style="flex:0 0 auto;padding-inline:1.2rem;">انصراف</a>
            </div>
        </form>
    </div>
</div>

@push('styles')
<style>
    .report-modal{position:fixed;inset:0;z-index:1000;display:none;}
    .report-modal:target{display:block;}   /* fallbackِ بدون JS */
    .report-modal.is-open{display:block;}   /* مسیرِ JS */
    /* هنگام باز بودن مودال، نویگیشن پایین را مخفی کن (هم مسیر JS، هم fallbackِ :target) */
    body:has(#report-modal:target) .bottom-nav,
    body.report-open .bottom-nav{display:none;}
    .report-modal__backdrop{position:absolute;inset:0;background:rgba(22,24,26,0.5);backdrop-filter:blur(2px);}
    .report-modal__panel{
        position:absolute;left:50%;bottom:0;transform:translateX(-50%);
        width:100%;max-width:430px;background:var(--surface);
        border-radius:22px 22px 0 0;padding:1.4rem 1.2rem calc(1.2rem + env(safe-area-inset-bottom));
        box-shadow:0 -18px 48px -20px rgba(40,60,50,0.4);
        animation:report-slide-up .22s ease;
        max-height:90dvh;overflow-y:auto;
    }
    @keyframes report-slide-up{from{transform:translate(-50%,100%);}to{transform:translate(-50%,0);}}
    .report-modal__head{display:flex;align-items:center;justify-content:space-between;margin-bottom:0.8rem;}
    .report-modal__close{
        width:34px;height:34px;border-radius:11px;border:1px solid var(--border);
        background:var(--surface);color:var(--ink-mid);display:flex;align-items:center;justify-content:center;cursor:pointer;
    }
    @media (prefers-reduced-motion: reduce){ .report-modal__panel{animation:none;} }
</style>
@endpush

@push('scripts')
<script>
(function () {
    var modal   = document.getElementById('report-modal');
    var openBtn = document.getElementById('open-report-modal');
    var form    = document.getElementById('report-form');
    if (!modal || !openBtn || !form) return;

    var submitBtn = document.getElementById('report-submit');
    var errorBox  = document.getElementById('report-form-error');
    var success   = document.getElementById('report-inline-success');
    var token     = form.querySelector('input[name="_token"]');

    function openModal() {
        modal.classList.add('is-open');
        document.body.classList.add('report-open');
        var amount = document.getElementById('report-amount');
        if (amount) setTimeout(function () { amount.focus(); }, 50);
    }
    function closeModal() {
        modal.classList.remove('is-open');
        document.body.classList.remove('report-open');
        // اگر hash باقی مانده (مثلاً از fallback)، پاک کن تا :target دوباره باز نکند
        if (location.hash === '#report-modal') {
            history.replaceState(null, '', location.pathname + location.search);
        }
    }
    function showError(msg) {
        if (!errorBox) return;
        errorBox.textContent = msg;
        errorBox.style.display = '';
    }
    function clearError() {
        if (errorBox) { errorBox.textContent = ''; errorBox.style.display = 'none'; }
    }

    openBtn.addEventListener('click', function (e) { e.preventDefault(); openModal(); });
    modal.querySelectorAll('[data-close-report]').forEach(function (el) {
        el.addEventListener('click', function (e) { e.preventDefault(); closeModal(); });
    });
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape' && modal.classList.contains('is-open')) closeModal();
    });

    form.addEventListener('submit', function (e) {
        e.preventDefault();
        clearError();

        var amount   = document.getElementById('report-amount');
        var tracking = document.getElementById('report-tracking');
        if (!amount.value || parseInt(amount.value, 10) < 1000) {
            showError('مبلغ واریزی باید حداقل ۱۰۰۰ تومان باشد.');
            return;
        }
        if (!tracking.value.trim()) {
            showError('شمارهٔ پیگیری را وارد کنید.');
            return;
        }

        var body = new URLSearchParams();
        body.append('amount', amount.value);
        body.append('tracking_number', tracking.value.trim());
        body.append('_token', token ? token.value : '');

        if (submitBtn) { submitBtn.disabled = true; submitBtn.textContent = 'در حال ارسال…'; }

        fetch(form.action, {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
                'Content-Type': 'application/x-www-form-urlencoded'
            },
            body: body.toString(),
            credentials: 'same-origin'
        }).then(function (r) {
            if (!r.ok) throw new Error('bad status');
            return r.json();
        }).then(function (data) {
            if (!data || !data.ok) throw new Error('not ok');
            closeModal();
            form.reset();
            if (success) { success.style.display = ''; success.scrollIntoView({ behavior: 'smooth', block: 'center' }); }
        }).catch(function () {
            showError('ثبت گزارش با خطا مواجه شد. دوباره تلاش کنید.');
        }).finally(function () {
            if (submitBtn) { submitBtn.disabled = false; submitBtn.textContent = 'ارسال'; }
            // چون این مسیر AJAX است و ناوبری‌ای رخ نمی‌دهد، لودرِ لِی‌اوت را پنهان کن
            var loader = document.getElementById('pk-loader');
            if (loader) loader.classList.add('pk-hide');
        });
    });
})();
</script>
@endpush
@endif

{{-- تراکنش‌ها --}}
<div style="margin-top:1.75rem;font-size:1.05rem;font-weight:800;">تراکنش‌ها</div>
@if($transactions->isEmpty())
    <div style="text-align:center;padding:2.5rem 1rem;color:var(--ink-dim);">
        <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="var(--ink-faint)" stroke-width="1.3" style="margin:0 auto 0.75rem;"><rect x="2" y="5" width="20" height="14" rx="2.5"/><path d="M2 10h20"/></svg>
        <div style="font-size:0.88rem;">هنوز تراکنشی ندارید</div>
    </div>
@else
<div style="margin-top:0.5rem;display:flex;flex-direction:column;">
    @foreach($transactions as $txn)
    @php
        $isCredit = in_array($txn->type, ['recharge', 'refund']);
    @endphp
    <div style="display:flex;align-items:center;gap:0.85rem;padding:0.9rem 0;border-bottom:1px solid var(--bg-mute);">
        <div style="width:42px;height:42px;border-radius:13px;background:{{ $isCredit ? 'var(--green-soft)' : '#f4f5f5' }};display:flex;align-items:center;justify-content:center;flex-shrink:0;">
            @if($isCredit)
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="var(--pine)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 19V5M6 11l6-6 6 6"/></svg>
            @else
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="var(--ink-mid)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 5v14M6 13l6 6 6-6"/></svg>
            @endif
        </div>
        <div style="flex:1;">
            <div style="font-size:0.88rem;font-weight:700;">{{ $txn->description ?? $txn->typeLabel() }}</div>
            <div style="font-size:0.7rem;color:var(--ink-dim);margin-top:2px;">{{ fa(\Morilog\Jalali\Jalalian::fromDateTime($txn->created_at)->format('j F · H:i')) }}</div>
        </div>
        <div style="font-size:0.95rem;font-weight:800;color:{{ $isCredit ? 'var(--pine)' : 'var(--ink)' }};">{{ fa(number_format($txn->amount)) }}{{ $isCredit ? '+' : '−' }}</div>
    </div>
    @endforeach
</div>
@endif

<div style="height:1rem;"></div>
@endsection

@section('nav')
    @include('panel.partials.bottom-nav', ['active' => 'home'])
@endsection
