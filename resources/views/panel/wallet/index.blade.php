@extends('panel.layouts.app')
@section('title', 'کیف پول')

@section('content')
{{-- هدر --}}
<div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1.3rem;">
    <div style="font-size:1.5rem;font-weight:800;letter-spacing:-0.5px;">کیف پول</div>
    <a href="{{ route('panel.dashboard') }}" class="icon-btn">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 6l6 6-6 6"/></svg>
    </a>
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
        <div style="font-size:0.8rem;color:var(--ink-mid);line-height:1.9;text-align:justify;">مبلغ دلخواه را به کارت زیر واریز کرده و سپس از طریق پیام به مدیریت، مبلغ و شمارهٔ پیگیری را اطلاع دهید تا کیف پولتان شارژ شود.</div>
        <div style="background:var(--bg-soft);border-radius:14px;padding:1rem;text-align:center;margin-top:1rem;">
            <div style="font-size:0.72rem;color:var(--ink-dim);margin-bottom:0.4rem;">شماره کارت</div>
            <div style="font-size:1.2rem;font-weight:700;color:var(--pine);direction:ltr;letter-spacing:2px;">{{ $cardNumber }}</div>
            @if($cardHolder)<div style="font-size:0.78rem;color:var(--ink-dim);margin-top:0.4rem;">{{ $cardHolder }}</div>@endif
        </div>
        <a href="{{ route('panel.messages.index') }}" class="btn btn-primary" style="margin-top:1rem;">اطلاع به مدیریت</a>
    </div>
    @endif
</div>

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
