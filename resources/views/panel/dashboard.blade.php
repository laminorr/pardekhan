@extends('panel.layouts.app')
@section('title', 'داشبورد')

@section('content')
{{-- هدر --}}
<div class="topbar">
    <div class="brand-text">
        <div class="name">پرده‌خوان</div>
        <div class="sub">باشگاه اعضا</div>
    </div>
    <div class="top-actions">
        <a href="{{ route('panel.messages.index') }}" class="icon-btn">
            @if(($unreadMessages ?? 0) > 0)<span class="ndot"></span>@endif
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round">
                <rect x="2" y="4" width="20" height="16" rx="2.5"/><path d="M3 7l9 6 9-6"/>
            </svg>
        </a>
    </div>
</div>

{{-- کارت عضویت --}}
@php
    $layer = $member->layer;
    $score = $member->score;
    $nextLayer = \App\Models\Layer::active()->where('min_score', '>', $score)->orderBy('min_score')->first();
    $currentMin = $layer?->min_score ?? 0;
    $nextMin = $nextLayer?->min_score;
    if ($nextMin && $nextMin > $currentMin) {
        $progress = min(100, round((($score - $currentMin) / ($nextMin - $currentMin)) * 100));
        $toNext = $nextMin - $score;
    } else {
        $progress = 100;
        $toNext = null;
    }
@endphp
<div class="card card-gold" style="border-radius:24px;padding:1.5rem;position:relative;overflow:hidden;">
    <div style="display:flex;align-items:center;gap:1rem;margin-bottom:1.5rem;position:relative;z-index:1;">
        <div style="width:62px;height:62px;border-radius:20px;padding:2px;background:linear-gradient(140deg,var(--gold-1),var(--gold-3));box-shadow:0 6px 22px rgba(212,175,106,0.3);">
            <div style="width:100%;height:100%;border-radius:18px;background:var(--surface);display:flex;align-items:center;justify-content:center;font-size:1.5rem;font-weight:800;color:var(--gold-1);overflow:hidden;">
                @if($member->avatar && $member->avatar_approved)
                    <img src="{{ Storage::url($member->avatar) }}" style="width:100%;height:100%;object-fit:cover;">
                @else
                    {{ mb_substr($member->first_name, 0, 1) }}
                @endif
            </div>
        </div>
        <div style="flex:1;">
            <div style="font-size:1.15rem;font-weight:700;color:#fff;">{{ $member->full_name }}</div>
            <span style="display:inline-flex;align-items:center;gap:5px;margin-top:5px;font-size:0.78rem;color:var(--gold-1);background:rgba(212,175,106,0.1);padding:3px 10px;border-radius:99px;border:1px solid var(--gold-border);">
                <svg width="13" height="13" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2l2.6 6.6L21 9.2l-5 4.5 1.5 7L12 17l-5.5 3.7L8 13.7l-5-4.5 6.4-.6z"/></svg>
                {{ $layer?->name ?? 'بدون لایه' }}
            </span>
        </div>
    </div>
    <div style="position:relative;z-index:1;">
        <div style="display:flex;justify-content:space-between;align-items:flex-end;margin-bottom:0.65rem;">
            <span style="font-size:0.72rem;color:var(--text-dim);">امتیاز شما</span>
            <span style="font-size:1.5rem;font-weight:800;color:var(--gold-1);line-height:1;">{{ number_format($score) }}<small style="font-size:0.68rem;color:var(--text-faint);font-weight:400;margin-right:2px;">امتیاز</small></span>
        </div>
        <div style="height:7px;background:rgba(0,0,0,0.3);border-radius:99px;overflow:hidden;border:1px solid rgba(255,255,255,0.04);">
            <div style="height:100%;width:{{ $progress }}%;background:linear-gradient(90deg,var(--gold-deep),var(--gold-1));border-radius:99px;"></div>
        </div>
        @if($toNext)
        <div style="display:flex;justify-content:flex-end;margin-top:0.5rem;">
            <span style="font-size:0.68rem;color:var(--gold-2);">{{ number_format($toNext) }} امتیاز تا {{ $nextLayer->name }}</span>
        </div>
        @endif
    </div>
</div>

{{-- منو --}}
<div class="section-head">
    <div class="section-title"><span class="bar"></span> دسترسی سریع</div>
</div>
<div style="display:grid;grid-template-columns:1fr 1fr;gap:0.85rem;">
    <a href="{{ route('panel.events.index') }}" class="card" style="margin-bottom:0;padding:1.15rem;text-decoration:none;color:inherit;">
        <div style="width:46px;height:46px;border-radius:14px;background:rgba(212,175,106,0.09);border:1px solid var(--gold-border);display:flex;align-items:center;justify-content:center;color:var(--gold-1);margin-bottom:0.85rem;">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="4" width="20" height="16" rx="2"/><path d="M7 4v16M17 4v16M2 9h5M2 15h5M17 9h5M17 15h5"/></svg>
        </div>
        <div style="font-size:0.98rem;font-weight:700;color:#fff;">دورهمی‌ها</div>
        <div style="font-size:0.72rem;color:var(--text-dim);margin-top:3px;">مشاهده و ثبت‌نام</div>
    </a>
    <a href="{{ route('panel.tickets.index') }}" class="card" style="margin-bottom:0;padding:1.15rem;text-decoration:none;color:inherit;">
        <div style="width:46px;height:46px;border-radius:14px;background:rgba(212,175,106,0.09);border:1px solid var(--gold-border);display:flex;align-items:center;justify-content:center;color:var(--gold-1);margin-bottom:0.85rem;">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><path d="M3 9a2 2 0 0 0 0 6v2a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-2a2 2 0 0 1 0-6V7a2 2 0 0 0-2-2H5a2 2 0 0 0-2 2z"/><path d="M13 5v14" stroke-dasharray="2 2"/></svg>
        </div>
        <div style="font-size:0.98rem;font-weight:700;color:#fff;">بلیت‌های من</div>
        <div style="font-size:0.72rem;color:var(--text-dim);margin-top:3px;">مشاهده بلیت‌ها</div>
    </a>
    <a href="#" class="card" style="margin-bottom:0;padding:1.15rem;text-decoration:none;color:inherit;">
        <div style="width:46px;height:46px;border-radius:14px;background:rgba(212,175,106,0.09);border:1px solid var(--gold-border);display:flex;align-items:center;justify-content:center;color:var(--gold-1);margin-bottom:0.85rem;">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="5" width="20" height="14" rx="2.5"/><path d="M2 10h20"/><path d="M16 15h2"/></svg>
        </div>
        <div style="font-size:0.98rem;font-weight:700;color:#fff;">کیف پول</div>
        <div style="font-size:0.72rem;color:var(--text-dim);margin-top:3px;">{{ number_format($member->wallet_balance) }} تومان</div>
    </a>
    <a href="{{ route('panel.profile') }}" class="card" style="margin-bottom:0;padding:1.15rem;text-decoration:none;color:inherit;">
        <div style="width:46px;height:46px;border-radius:14px;background:rgba(212,175,106,0.09);border:1px solid var(--gold-border);display:flex;align-items:center;justify-content:center;color:var(--gold-1);margin-bottom:0.85rem;">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="8" r="4"/><path d="M4 21a8 8 0 0 1 16 0"/></svg>
        </div>
        <div style="font-size:0.98rem;font-weight:700;color:#fff;">پروفایل من</div>
        <div style="font-size:0.72rem;color:var(--text-dim);margin-top:3px;">ویرایش اطلاعات</div>
    </a>
</div>

{{-- خروج --}}
<form method="POST" action="{{ route('panel.logout') }}" style="margin-top:1.5rem;">
    @csrf
    <button type="submit" class="back-link" style="width:100%;border:1px solid var(--border);background:var(--surface);cursor:pointer;font-family:inherit;">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4M16 17l5-5-5-5M21 12H9"/></svg>
        خروج از حساب
    </button>
</form>
@endsection
