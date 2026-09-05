@extends('panel.layouts.app')
@section('title', 'داشبورد')

@section('content')
{{-- هدر یکپارچه: خوش‌آمد + زنگوله + حلقهٔ لایهٔ عضویت + نردبان --}}
@php
    $h = (int) \Carbon\Carbon::now('Asia/Tehran')->format('H');
    $greet = $h < 5 ? 'شب بخیر،' : ($h < 12 ? 'صبح بخیر،' : ($h < 17 ? 'ظهر بخیر،' : ($h < 20 ? 'عصر بخیر،' : 'شب بخیر،')));

    // ── محاسبات لایهٔ عضویت (منطق بدون تغییر؛ فقط از پایین صفحه به این هدر منتقل شده) ──
    $score = $member->score;
    $allLayers = \App\Models\Layer::active()->orderBy('min_score')->get();

    // لایه فعلی را بر اساس امتیاز واقعی محاسبه کن (نه layer_id ذخیره‌شده که ممکن است عقب باشد)
    $layer = $allLayers->filter(fn($l) => $l->min_score <= $score)->sortByDesc('min_score')->first() ?? $allLayers->first();

    $nextLayer = $allLayers->where('min_score', '>', $score)->first();
    $currentMin = $layer?->min_score ?? 0;
    $nextMin = $nextLayer?->min_score;
    if ($nextMin && $nextMin > $currentMin) {
        $progress = min(100, (($score - $currentMin) / ($nextMin - $currentMin)) * 100);
        $toNext = $nextMin - $score;
    } else {
        $progress = 100;
        $toNext = null;
    }
    // محاسبه dashoffset برای حلقه (محیط دایره r=76 → 477.5)
    $circumference = 477.5;
    $dashoffset = $circumference * (1 - $progress / 100);

    // ایندکس لایه فعلی برای نردبان (بر اساس امتیاز)
    $currentIndex = $layer ? $allLayers->search(fn($l) => $l->id === $layer->id) : -1;

    // ── گیاه عضویت: محاسبات سمت سرور (day / absence → state → پیام پایدارِ روز) ──
    $plantDay     = $member->daysSinceJoin();
    $__prevSeen = request()->attributes->get('member_previous_seen');
    $plantAbsence = $__prevSeen ? (int) floor($__prevSeen->diffInDays(now())) : 0;
    $plantCycle   = intdiv($plantDay, 180) + 1;

    if ($plantAbsence >= 8) {
        $plantState = 'parched';   // خشکیده و دلتنگ
    } elseif ($plantAbsence >= 3) {
        $plantState = 'wilting';   // کمی پژمرده
    } else {                       // حاضر (غیبت < ۳): بر اساس فصل
        $plantT = $plantDay % 180;
        if ($plantT >= 58 && $plantT < 90) {
            $plantState = 'bloom';    // فصل شکوفه
        } elseif ($plantT >= 90) {
            $plantState = 'winter';   // پاییز/زمستان
        } else {
            $plantState = 'fresh';    // شاداب و در حال رشد
        }
    }

    $plantMessages = \App\Support\PlantMessages::STATES[$plantState];
    $plantMsg      = $plantMessages[$plantDay % 40];
@endphp
<div class="pk-hero">
    <span class="pk-hero__deco pk-hero__deco--a" aria-hidden="true"></span>
    <span class="pk-hero__deco pk-hero__deco--b" aria-hidden="true"></span>

    {{-- گیاه عضویت: لایهٔ زمینه‌ای پشتِ محتوای هدر (فقط تزئینی) --}}
    <canvas id="pk-plant-cv" aria-hidden="true"
        style="position:absolute;inset:0;width:100%;height:100%;z-index:1;opacity:.5;pointer-events:none;"></canvas>

    {{-- خوش‌آمد + زنگوله --}}
    <div class="pk-hero__top">
        <div class="pk-hero__greet">
            <div class="pk-hero__hi">{{ $greet }}</div>
            <div class="pk-hero__name">{{ $member->full_name }}</div>
        </div>
        <a href="{{ route('panel.messages.index') }}" class="bell-btn {{ ($unreadMessages ?? 0) > 0 ? 'has-unread' : '' }}" aria-label="پیام‌ها">
            @if(($unreadMessages ?? 0) > 0)
                <span class="bell-badge">{{ fa($unreadMessages) }}</span>
            @endif
            <svg class="bell-svg" width="21" height="21" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><path d="M6 8a6 6 0 0 1 12 0c0 7 3 9 3 9H3s3-2 3-9"/><path d="M10.3 21a1.94 1.94 0 0 0 3.4 0"/></svg>
        </a>
    </div>

    {{-- حلقهٔ پیشرفت لایه --}}
    <div class="pk-hero__ringwrap">
        <svg class="pk-hero__ring" width="152" height="152" viewBox="0 0 188 188" aria-hidden="true">
            <circle cx="94" cy="94" r="76" fill="none" stroke="rgba(255,255,255,0.18)" stroke-width="8"/>
            <circle cx="94" cy="94" r="76" fill="none" stroke="#e9f7ef" stroke-width="8" stroke-linecap="round"
                stroke-dasharray="477.5" stroke-dashoffset="{{ $dashoffset }}" transform="rotate(-90 94 94)"
                style="animation:pkring 1.4s cubic-bezier(.5,0,.1,1) .3s both;"/>
        </svg>
        <div class="pk-hero__ringtext">
            <div class="pk-hero__ringlabel">لایهٔ عضویت</div>
            <div class="pk-hero__tier">{{ $layer?->name ?? 'مهمان' }}</div>
            <div class="pk-hero__score"><b>{{ fa(number_format($score)) }}</b><span>امتیاز</span></div>
        </div>
    </div>

    {{-- امتیاز تا لایهٔ بعد --}}
    @if($toNext && $nextLayer)
        <div class="pk-hero__next">{{ fa(number_format($toNext)) }} امتیاز تا لایهٔ <b>{{ $nextLayer->name }}</b></div>
    @else
        <div class="pk-hero__next pk-hero__next--top">بالاترین لایه 🏆</div>
    @endif

    {{-- نردبان لایه‌ها --}}
    @if($allLayers->count() > 1)
    <div class="pk-hero__ladder">
        @foreach($allLayers as $i => $l)
            @php
                $isPast = $i < $currentIndex;
                $isCurrent = $i === $currentIndex;
            @endphp
            {{-- نقطه --}}
            <div class="pk-hero__step">
                @if($isCurrent)
                    <span class="pk-hero__dot pk-hero__dot--current"></span>
                    <span class="pk-hero__steplabel pk-hero__steplabel--current">{{ $l->name }}</span>
                @else
                    <span class="pk-hero__dot {{ $isPast ? 'pk-hero__dot--done' : '' }}"></span>
                    <span class="pk-hero__steplabel">{{ $l->name }}</span>
                @endif
            </div>
            {{-- خط اتصال --}}
            @if(!$loop->last)
                @php
                    if ($i < $currentIndex) { $lineBg = '#ffffff'; }
                    elseif ($i === $currentIndex) { $lineBg = 'linear-gradient(270deg,#ffffff ' . round($progress) . '%,rgba(255,255,255,0.22) ' . round($progress) . '%)'; }
                    else { $lineBg = 'rgba(255,255,255,0.22)'; }
                @endphp
                <div class="pk-hero__line" style="background:{{ $lineBg }};"></div>
            @endif
        @endforeach
    </div>
    @endif
</div>

{{-- کارت وضعیت گیاه عضویت (سفید، زیرِ هدر — هم‌سبک با نوار آمار) --}}
<div class="pk-plant-card">
    <span class="pk-plant-card__chip" aria-hidden="true">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="var(--pine)" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22V12"/><path d="M12 12C12 8 9 5 3 5c0 6 3 8 9 7z"/><path d="M12 10c0-3 3-6 9-6 0 5-3 7-9 6z"/></svg>
    </span>
    <p class="pk-plant-card__text">درخت تو {{ fa($plantDay) }} روزه که پیش ماست. باهم مراقبش هستیم. {{ $plantMsg }}</p>
</div>

@push('styles')
<style>
    .bell-btn {
        width: 44px; height: 44px; border-radius: 15px; background: var(--surface);
        border: 1px solid var(--border); display: flex; align-items: center; justify-content: center;
        color: var(--ink); position: relative; text-decoration: none; flex-shrink: 0;
        box-shadow: 0 2px 8px rgba(40,60,50,0.06); transition: transform 0.2s, box-shadow 0.2s;
    }
    .bell-btn:active { transform: scale(0.94); }
    .bell-svg { transform-origin: 50% 4px; }

    /* تکون ملایم همیشگی */
    @keyframes bell-sway {
        0%, 88%, 100% { transform: rotate(0); }
        91% { transform: rotate(9deg); }
        94% { transform: rotate(-7deg); }
        97% { transform: rotate(4deg); }
    }
    .bell-svg { animation: bell-sway 4s ease-in-out infinite; }

    /* حالت پیام خوانده‌نشده — با رنگ پالت */
    .bell-btn.has-unread {
        background: linear-gradient(145deg, var(--pine), var(--pine-deep));
        border-color: var(--pine-deep);
        color: #fff;
        box-shadow: 0 6px 18px rgba(47,93,80,0.35);
    }
    .bell-btn.has-unread .bell-svg {
        animation: bell-ring 2.2s ease-in-out infinite;
    }
    @keyframes bell-ring {
        0%, 60%, 100% { transform: rotate(0); }
        66% { transform: rotate(12deg); }
        72% { transform: rotate(-10deg); }
        78% { transform: rotate(7deg); }
        84% { transform: rotate(-4deg); }
        90% { transform: rotate(0); }
    }

    /* نشان تعداد پیام */
    .bell-badge {
        position: absolute; top: -6px; left: -6px;
        min-width: 20px; height: 20px; padding: 0 5px;
        background: var(--burnt); color: #fff;
        font-size: 0.66rem; font-weight: 800; border-radius: 99px;
        display: flex; align-items: center; justify-content: center;
        border: 2px solid var(--bg); z-index: 2;
        box-shadow: 0 2px 8px rgba(194,85,47,0.4);
        animation: badge-pop 0.4s cubic-bezier(.5,1.6,.5,1) both;
    }
    @keyframes badge-pop { from { transform: scale(0); } to { transform: scale(1); } }
    @keyframes pulse-ring { 0% { transform: scale(0.8); opacity: 0.7; } 80%, 100% { transform: scale(2.2); opacity: 0; } }
    .stat-tick { transition: opacity 0.25s; }

    /* ── هدر یکپارچهٔ سبز (همه‌چیز زیر .pk-hero اسکوپ شده) ── */
    .pk-hero {
        position: relative;
        overflow: hidden;
        border-radius: 28px;
        margin: 0 0 1.1rem;
        padding: 20px 20px 24px;
        background: linear-gradient(158deg, #356450, #2b5241);
        box-shadow: 0 18px 40px -18px rgba(31,77,64,0.55);
        color: #fff;
    }
    .pk-hero__deco {
        position: absolute; border-radius: 50%; pointer-events: none;
        background: rgba(255,255,255,0.06);
    }
    .pk-hero__deco--a { top: -45px; left: -30px; width: 150px; height: 150px; }
    .pk-hero__deco--b { bottom: -55px; right: -25px; width: 120px; height: 120px; background: rgba(255,255,255,0.045); }

    .pk-hero__top { position: relative; display: flex; align-items: center; justify-content: space-between; }
    .pk-hero__hi { font-size: 0.8rem; color: rgba(255,255,255,0.72); font-weight: 500; }
    .pk-hero__name { font-size: 1.4rem; font-weight: 700; color: #fff; line-height: 1.15; letter-spacing: -0.02em; margin-top: 2px; }

    /* زنگوله روی زمینهٔ سبز — نسخهٔ روشن و نیمه‌شفاف */
    .pk-hero .bell-btn {
        background: rgba(255,255,255,0.18);
        border-color: rgba(255,255,255,0.28);
        color: #fff;
        box-shadow: 0 4px 14px rgba(20,40,32,0.18);
    }
    .pk-hero .bell-btn.has-unread {
        background: rgba(255,255,255,0.28);
        border-color: rgba(255,255,255,0.40);
        color: #fff;
        box-shadow: 0 6px 18px rgba(20,40,32,0.25);
    }

    .pk-hero__ringwrap { position: relative; width: 152px; height: 152px; margin: 1.1rem auto 0; }
    .pk-hero__ring { display: block; }
    .pk-hero__ringtext { position: absolute; inset: 0; display: flex; flex-direction: column; align-items: center; justify-content: center; }
    .pk-hero__ringlabel { font-size: 0.6rem; letter-spacing: 0; color: rgba(255,255,255,0.6); font-weight: 700; }
    .pk-hero__tier { font-size: 1.7rem; font-weight: 800; color: #fff; line-height: 1.05; margin-top: 3px; letter-spacing: -0.5px; }
    .pk-hero__score { display: flex; align-items: baseline; gap: 4px; margin-top: 4px; color: #fff; }
    .pk-hero__score b { font-size: 1.05rem; }
    .pk-hero__score span { font-size: 0.68rem; color: rgba(255,255,255,0.7); }

    .pk-hero__next { position: relative; text-align: center; margin-top: 10px; font-size: 0.78rem; color: rgba(255,255,255,0.72); }
    .pk-hero__next b { color: #fff; font-weight: 800; }
    .pk-hero__next--top { color: #fff; font-weight: 600; }

    .pk-hero__ladder { position: relative; margin-top: 1.15rem; display: flex; align-items: center; }
    .pk-hero__step { display: flex; flex-direction: column; align-items: center; gap: 6px; flex: 1; }
    .pk-hero__dot { width: 11px; height: 11px; border-radius: 50%; background: rgba(255,255,255,0.35); }
    .pk-hero__dot--done { background: #fff; }
    .pk-hero__dot--current { width: 16px; height: 16px; background: #fff; box-shadow: 0 0 0 4px rgba(255,255,255,0.22); }
    .pk-hero__steplabel { font-size: 0.62rem; color: rgba(255,255,255,0.6); }
    .pk-hero__steplabel--current { color: #fff; font-weight: 800; }
    .pk-hero__line { height: 2px; flex: 1; margin-bottom: 17px; border-radius: 2px; }

    /* ── گیاه عضویت: محتوای هدر باید بالای بوم (canvas) بماند و خوانا باشد ── */
    .pk-hero__top,
    .pk-hero__ringwrap,
    .pk-hero__next,
    .pk-hero__ladder { z-index: 3; }
    .pk-hero__name,
    .pk-hero__tier { text-shadow: 0 1px 10px rgba(20,40,32,0.28); }
    .pk-hero__hi,
    .pk-hero__ringlabel,
    .pk-hero__score,
    .pk-hero__next,
    .pk-hero__steplabel { text-shadow: 0 1px 6px rgba(20,40,32,0.22); }

    /* کارت وضعیت گیاه — سفید، هم‌سبک با نوار آمار زنده */
    .pk-plant-card {
        display: flex;
        align-items: center;
        gap: 0.6rem;
        background: var(--bg-mute);

        border-radius: 14px;
        padding: 0.7rem 0.8rem;
        margin: 0 0 1.1rem;

    }
    .pk-plant-card__chip {
        width: 30px; height: 30px; flex-shrink: 0;
        border-radius: 9px;
        background: var(--green-tint);
        display: flex; align-items: center; justify-content: center;
    }
    .pk-plant-card__text {
        margin: 0;
        min-width: 0;
        font-size: 0.82rem;
        font-weight: 600;
        line-height: 1.7;
        color: var(--ink-mid);
        text-align: justify;
    }
</style>
@endpush

{{-- نوار آمار زنده --}}
<div style="display:flex;gap:0.6rem;margin-bottom:1.1rem;">
    <div style="flex:1;display:flex;align-items:center;gap:0.55rem;background:var(--bg-mute);border-radius:14px;padding:0.6rem 0.7rem;">
        <span style="position:relative;display:flex;width:8px;height:8px;flex-shrink:0;">
            <span style="position:absolute;width:100%;height:100%;border-radius:50%;background:#3fb27f;opacity:0.6;animation:pulse-ring 2s infinite;"></span>
            <span style="position:relative;width:8px;height:8px;border-radius:50%;background:#3fb27f;"></span>
        </span>
        <div style="min-width:0;">
            <div id="stat-online" style="font-size:0.92rem;font-weight:800;color:var(--ink);line-height:1.1;">۰</div>
            <div style="font-size:0.63rem;color:var(--ink-faint);">نفر آنلاین‌اند</div>
        </div>
    </div>
    <div style="flex:1;display:flex;align-items:center;gap:0.55rem;background:var(--bg-mute);border-radius:14px;padding:0.6rem 0.7rem;">
        <div style="width:26px;height:26px;border-radius:8px;background:var(--green-tint);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="var(--pine)" stroke-width="1.8"><rect x="2" y="3" width="20" height="14" rx="2"/><path d="M8 21h8M12 17v4"/></svg>
        </div>
        <div style="min-width:0;">
            <div id="stat-watching" style="font-size:0.92rem;font-weight:800;color:var(--ink);line-height:1.1;">۰</div>
            <div style="font-size:0.63rem;color:var(--ink-faint);">در حال دیدن فیلم هفته</div>
        </div>
    </div>
</div>

{{-- باکس‌های پادکست و فیلم هفته --}}
@php
    // فیلمِ هفتهٔ جاری از روی تخصیصِ فعالِ همین هفته (هم‌راستا با صفحهٔ فیلم هفته).
    $weekStart = (new \App\Services\WeeklyMovie\WeeklyMovieWeekResolver)->currentWeek()['start']->toDateString();
    $todayFilm = optional(
        \App\Models\WeeklyMovieAssignment::active()->forWeek($weekStart)->with('film')->first()
    )->film;
@endphp
<div style="margin-top:1.4rem;display:flex;gap:0.7rem;">
    {{-- پادکست‌زده --}}
    <a href="{{ route('panel.podcast') }}" style="flex:1;position:relative;overflow:hidden;text-decoration:none;color:#fff;border-radius:20px;background:linear-gradient(145deg,var(--pine-bright),var(--pine-deep));padding:1.1rem 1.05rem;min-height:128px;display:flex;flex-direction:column;box-shadow:0 10px 26px -12px rgba(47,93,80,0.55);">
        <div style="position:absolute;top:-30px;left:-25px;width:90px;height:90px;border-radius:50%;background:rgba(255,255,255,0.07);"></div>
        <div style="position:absolute;bottom:-35px;right:-15px;width:80px;height:80px;border-radius:50%;background:rgba(255,255,255,0.05);"></div>
        <div style="position:relative;display:flex;align-items:center;justify-content:space-between;">
            <div style="width:40px;height:40px;border-radius:13px;background:rgba(255,255,255,0.18);display:flex;align-items:center;justify-content:center;backdrop-filter:blur(6px);">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="1.7"><path d="M12 2a3 3 0 0 0-3 3v7a3 3 0 0 0 6 0V5a3 3 0 0 0-3-3z"/><path d="M19 10v2a7 7 0 0 1-14 0v-2M12 19v3"/></svg>
            </div>
            <div style="width:30px;height:30px;border-radius:50%;background:#fff;display:flex;align-items:center;justify-content:center;">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="var(--pine)"><path d="M8 5v14l11-7z"/></svg>
            </div>
        </div>
        <div style="position:relative;margin-top:auto;">
            <div style="font-size:1rem;font-weight:800;">پادکست</div>
            <div style="font-size:0.72rem;color:rgba(234,243,239,0.85);margin-top:2px;">عدم قطعیت / هژمونی</div>
        </div>
    </a>

    {{-- فیلم هفته --}}
    <a href="{{ $todayFilm ? route('panel.film.today') : '#' }}" style="flex:1;position:relative;overflow:hidden;text-decoration:none;color:#fff;border-radius:20px;background:linear-gradient(145deg,#d06236,#a8431f);padding:1.1rem 1.05rem;min-height:128px;display:flex;flex-direction:column;box-shadow:0 10px 26px -12px rgba(168,67,31,0.5);{{ $todayFilm ? '' : 'opacity:0.92;' }}">
        <div style="position:absolute;top:-30px;right:-25px;width:90px;height:90px;border-radius:50%;background:rgba(255,255,255,0.08);"></div>
        <div style="position:absolute;bottom:-35px;left:-15px;width:80px;height:80px;border-radius:50%;background:rgba(255,255,255,0.05);"></div>
        <div style="position:relative;display:flex;align-items:center;justify-content:space-between;">
            <div style="width:40px;height:40px;border-radius:13px;background:rgba(255,255,255,0.18);display:flex;align-items:center;justify-content:center;backdrop-filter:blur(6px);">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="1.7"><rect x="2" y="2" width="20" height="20" rx="2.5"/><path d="M7 2v20M17 2v20M2 12h20M2 7h5M2 17h5M17 17h5M17 7h5"/></svg>
            </div>
        </div>
        <div style="position:relative;margin-top:auto;">
            <div style="font-size:1rem;font-weight:800;">فیلم هفته</div>
            <div style="font-size:0.72rem;color:rgba(255,255,255,0.85);margin-top:2px;">{{ $todayFilm ? \Illuminate\Support\Str::limit($todayFilm->title, 16) : 'به‌زودی' }}</div>
        </div>
    </a>
</div>

{{-- منوی دسترسی سریع --}}
<div class="section-head"><div class="section-title">دسترسی سریع</div></div>
<div style="display:grid;grid-template-columns:1fr 1fr;gap:0.8rem;">
    @php
        $menuItems = [
            ['دورهمی‌ها', 'مشاهده و ثبت‌نام', route('panel.events.index'), 'M2 4h20v16H2zM7 4v16M17 4v16M2 9h5M2 15h5M17 9h5M17 15h5', 'green'],
            ['بلیت‌های من', 'بلیت‌های فعال', route('panel.tickets.index'), 'M3 9a2 2 0 0 0 0 6v2a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-2a2 2 0 0 1 0-6V7a2 2 0 0 0-2-2H5a2 2 0 0 0-2 2z', 'burnt'],
            ['کیف پول', fa(number_format($member->wallet_balance)) . ' ت', route('panel.wallet'), 'M2 5h20v14H2zM2 10h20', 'green'],
            ['پیام‌ها', ($unreadMessages ?? 0) > 0 ? (fa($unreadMessages) . ' پیام جدید') : 'بدون پیام جدید', route('panel.messages.index'), 'M2 4h20v16H2zM3 7l9 6 9-6', 'burnt'],
        ];
    @endphp
    @foreach($menuItems as [$title, $desc, $url, $icon, $color])
    <a href="{{ $url }}" style="background:var(--surface);border:1px solid var(--border);border-radius:18px;padding:1.1rem;text-decoration:none;color:inherit;box-shadow:0 4px 20px rgba(40,60,50,0.07);">
        <div style="width:44px;height:44px;border-radius:13px;display:flex;align-items:center;justify-content:center;margin-bottom:0.8rem;
            {{ $color === 'green' ? 'background:var(--green-soft);color:var(--pine);' : 'background:#fbeae4;color:var(--burnt);' }}">
            <svg width="23" height="23" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><path d="{{ $icon }}"/></svg>
        </div>
        <div style="font-size:0.96rem;font-weight:700;color:var(--ink);">{{ $title }}</div>
        <div style="font-size:0.72rem;color:var(--ink-dim);margin-top:2px;">{{ $desc }}</div>
    </a>
    @endforeach
</div>

{{-- دورهمی پیشنهادی --}}
@php
    $suggested = \App\Models\Event::where('status', 'active')->where('starts_at', '>', now())->visibleTo($member)->orderBy('starts_at')->first();

    // آیا عضو در این دورهمی ثبت‌نام کرده؟ (هم‌منطق با EventController@show)
    $suggestedIsRegistered = false;
    $suggestedTicket = null;
    if ($suggested) {
        $suggestedIsRegistered = $suggested->registrations()
            ->where('member_id', $member->id)
            ->whereIn('attendance_status', ['registered', 'attended'])
            ->exists();
        if ($suggestedIsRegistered) {
            // بلیت مخصوص همین دورهمی برای همین عضو
            $suggestedTicket = \App\Models\Ticket::where('member_id', $member->id)
                ->where('event_id', $suggested->id)
                ->where('status', '!=', 'cancelled')
                ->latest()
                ->first();
        }
    }
@endphp
@if($suggested)
<div class="section-head">
    <div class="section-title">دورهمی پیشنهادی</div>
    <a href="{{ route('panel.events.index') }}" class="see-all">دیدن همه ›</a>
</div>
<a href="{{ route('panel.events.show', $suggested) }}" style="display:block;text-decoration:none;color:inherit;background:var(--surface);border:1px solid var(--border);border-radius:22px;overflow:hidden;box-shadow:0 4px 20px rgba(40,60,50,0.07);">
    <div style="height:130px;position:relative;background:linear-gradient(135deg,var(--pine-bright),var(--pine-deep));">
        @if($suggested->image)
            <img src="{{ Storage::url($suggested->image) }}" style="width:100%;height:100%;object-fit:cover;">
            <div style="position:absolute;inset:0;background:linear-gradient(to top,rgba(22,24,26,0.4),transparent 60%);"></div>
        @else
            <div style="position:absolute;inset:0;display:flex;align-items:center;justify-content:center;color:rgba(255,255,255,0.25);">
                <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.2"><rect x="2" y="4" width="20" height="16" rx="2"/><path d="M7 4v16M17 4v16M2 9h5M2 15h5M17 9h5M17 15h5"/></svg>
            </div>
        @endif
        <span style="position:absolute;top:0.8rem;right:0.8rem;background:var(--surface);color:var(--pine);font-size:0.68rem;font-weight:700;padding:4px 11px;border-radius:99px;display:inline-flex;align-items:center;gap:4px;">
            <svg width="11" height="11" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2l2.6 6.6L21 9.2l-5 4.5 1.5 7L12 17l-5.5 3.7L8 13.7l-5-4.5 6.4-.6z"/></svg>
            ویژهٔ لایهٔ شما
        </span>
    </div>
    <div style="padding:1.1rem 1.2rem 1.3rem;">
        <div style="font-size:1.2rem;font-weight:800;color:var(--ink);letter-spacing:-0.02em;">{{ $suggested->title }}</div>
        @if($suggested->subtitle)
            <div style="font-size:0.8rem;color:var(--ink-dim);margin-top:2px;">{{ $suggested->subtitle }}</div>
        @endif
        <div style="display:flex;align-items:center;gap:1rem;margin-top:0.85rem;font-size:0.76rem;color:var(--ink-dim);">
            <span style="display:flex;align-items:center;gap:4px;">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="var(--pine)" stroke-width="1.8"><rect x="3" y="4" width="18" height="18" rx="2"/><path d="M16 2v4M8 2v4M3 10h18"/></svg>
                {{ fa(\Morilog\Jalali\Jalalian::fromDateTime($suggested->starts_at)->format('j F')) }} · {{ fa($suggested->starts_at->format('H:i')) }}
            </span>
            @if($suggested->venue)
            <span style="display:flex;align-items:center;gap:4px;">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="var(--pine)" stroke-width="1.8"><path d="M21 10c0 7-9 12-9 12s-9-5-9-12a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
                {{ $suggested->venue->name }}
            </span>
            @endif
        </div>
        @unless($suggestedIsRegistered)
            @php $price = $suggested->priceForMember($member); $discount = $suggested->discountForLayer($layer); @endphp
            <div style="height:1px;background:var(--border);margin:1rem 0;"></div>
            <div style="display:flex;align-items:center;justify-content:space-between;">
                <div>
                    @if($discount > 0)
                        <span style="font-size:0.72rem;color:var(--ink-faint);text-decoration:line-through;">{{ fa(number_format($suggested->base_price)) }}</span>
                    @endif
                    <div style="font-size:1.3rem;font-weight:800;color:var(--ink);">{{ fa(number_format($price)) }} <span style="font-size:0.7rem;font-weight:400;color:var(--ink-dim);">تومان</span></div>
                </div>
                <span class="btn btn-primary" style="width:auto;padding:0.7rem 1.5rem;">ثبت‌نام
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 12H5M12 5l-7 7 7 7"/></svg>
                </span>
            </div>
        @endunless
        @if($suggestedIsRegistered)
            <div style="height:1px;background:var(--border);margin:1rem 0;"></div>
            <span onclick="event.preventDefault();event.stopPropagation();window.location='{{ $suggestedTicket ? route('panel.tickets.show', $suggestedTicket) : route('panel.tickets.index') }}';"
               style="display:flex;align-items:center;justify-content:center;gap:7px;padding:0.6rem 1rem;background:var(--pine);color:#fff;font-size:0.86rem;font-weight:700;letter-spacing:-0.2px;border-radius:14px;box-shadow:0 6px 18px -9px rgba(47,93,80,0.5);cursor:pointer;">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6L9 17l-5-5"/></svg>
                در این دورهمی باهم خواهیم بود
            </span>
        @endif
    </div>
</a>
@endif

{{-- مجله / وبلاگ --}}
@php
    $latestPosts = \App\Models\Post::where('is_published', true)
        ->orderByDesc('published_at')->orderByDesc('created_at')
        ->limit(4)->get();
@endphp
@if($latestPosts->isNotEmpty())
<div class="section-head" style="margin-top:1.8rem;">
    <div class="section-title">مجله پرده‌خوان</div>
    <a href="{{ route('panel.posts.index') }}" style="font-size:0.78rem;color:var(--pine);font-weight:700;text-decoration:none;">همه</a>
</div>

<div style="margin-top:0.6rem;background:#fff;border:1px solid var(--border);border-radius:20px;padding:0 1rem;box-shadow:0 3px 16px rgba(40,60,50,0.05);">
    @foreach($latestPosts as $post)
    <a href="{{ route('panel.posts.show', $post) }}" style="display:flex;gap:0.85rem;align-items:flex-start;padding:0.95rem 0;{{ !$loop->last ? 'border-bottom:1.5px dashed #f2f3f2;' : '' }}text-decoration:none;color:inherit;">
        {{-- کاور سمت راست --}}
        @if($post->cover_src)
            <img src="{{ $post->cover_src }}" alt="" style="width:82px;height:112px;border-radius:12px;object-fit:cover;flex:0 0 82px;background:var(--green-soft);">
        @else
            <div style="width:82px;height:112px;border-radius:12px;background:var(--green-soft);display:flex;align-items:center;justify-content:center;flex:0 0 82px;">
                <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="var(--pine)" stroke-width="1.5"><path d="M4 4h16v16H4zM4 9h16M9 4v16"/></svg>
            </div>
        @endif
        <div style="flex:1;min-width:0;">
            <div style="font-size:0.96rem;font-weight:800;line-height:1.4;">{{ $post->title }}</div>
            <div style="font-size:0.79rem;color:var(--ink-mid);line-height:1.65;margin-top:0.35rem;text-align:justify;">{{ \Illuminate\Support\Str::limit($post->summary, 88) }}</div>
            <div style="display:flex;align-items:center;gap:0.5rem;font-size:0.68rem;color:var(--ink-faint);margin-top:0.45rem;">
                <span>تاریخ انتشار این مطلب: {{ pdate($post->published_at ?? $post->created_at, 'j F') }}</span>
                @if($post->author)
                    <span style="width:3px;height:3px;border-radius:50%;background:var(--ink-faint);"></span>
                    <span>{{ $post->author }}</span>
                @endif
            </div>
        </div>
    </a>
    @endforeach
</div>
@endif

{{-- باکس دعوت به گفتگو --}}
<div style="position:relative;overflow:hidden;background:var(--bg-mute);border:1px solid var(--border);border-radius:22px;padding:1.4rem 1.3rem;margin-top:1.5rem;">
    <svg viewBox="0 0 24 24" fill="none" stroke="var(--pine)" stroke-width="1.4" style="position:absolute;left:-14px;bottom:-14px;width:120px;height:120px;opacity:0.07;transform:rotate(-8deg);"><path d="M12 2L2 22h20z" stroke-linejoin="round"/></svg>
    <div style="position:relative;z-index:2;">
        <div style="font-size:0.72rem;font-weight:800;color:var(--pine);letter-spacing:-0.2px;margin-bottom:0.55rem;">اولین قدم</div>
        <div style="font-size:1.12rem;font-weight:800;line-height:1.55;color:var(--ink);letter-spacing:-0.4px;">آماده‌اید با هم<br>اولین قدم را برداریم؟</div>
        <div style="font-size:0.82rem;color:var(--ink-mid);margin-top:0.6rem;line-height:1.7;">تو چه کتاب یا چه موضوعی رو پیشنهاد می‌دی؟</div>
        <a href="{{ route('panel.messages.index') }}" style="display:inline-flex;align-items:center;gap:8px;margin-top:1.15rem;background:var(--pine);color:#fff;font-weight:800;font-size:0.9rem;padding:0.8rem 1.5rem;border-radius:14px;text-decoration:none;">
            <span>پیام بده</span>
            <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 12H5M12 19l-7-7 7-7"/></svg>
        </a>
    </div>
</div>

{{-- بنر باشگاه کتاب‌خوانی --}}
<a href="{{ route('panel.books.index') }}" style="display:flex;align-items:center;gap:0.9rem;background:var(--bg-mute);border:1px solid var(--border);border-radius:22px;padding:1.1rem 1.2rem;margin-top:1.1rem;text-decoration:none;color:inherit;">
    <div style="width:44px;height:44px;border-radius:13px;background:var(--green-soft);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
        <svg width="23" height="23" viewBox="0 0 24 24" fill="none" stroke="var(--pine)" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/></svg>
    </div>
    <div style="flex:1;min-width:0;">
        <div style="font-size:0.98rem;font-weight:800;color:var(--ink);letter-spacing:-0.3px;">باشگاه کتاب‌خوانی</div>
        <div style="font-size:0.76rem;color:var(--ink-mid);margin-top:2px;line-height:1.6;">کتاب‌های خوب را باهم به اشتراک بگذاریم و در موردشان حرف بزنیم</div>
    </div>
    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="var(--ink-faint)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="flex-shrink:0;"><path d="M15 18l-6-6 6-6"/></svg>
</a>

{{-- بنر دعوت به ارتباط --}}
<a href="{{ route('panel.messages.index') }}" style="display:block;text-decoration:none;margin-top:1.1rem;margin-bottom:1.1rem;position:relative;overflow:hidden;border-radius:18px;background:linear-gradient(145deg,var(--pine-bright),var(--pine-deep));box-shadow:0 10px 26px -10px rgba(47,93,80,0.5);">
    {{-- بافت تزئینی --}}
    <div style="position:absolute;top:-40px;left:-20px;width:120px;height:120px;border-radius:50%;background:rgba(255,255,255,0.06);"></div>
    <div style="position:absolute;bottom:-50px;left:60px;width:90px;height:90px;border-radius:50%;background:rgba(255,255,255,0.04);"></div>
    <div style="position:relative;display:flex;align-items:center;gap:0.9rem;padding:1rem 1.15rem;">
        <div style="width:42px;height:42px;border-radius:13px;background:rgba(255,255,255,0.15);display:flex;align-items:center;justify-content:center;flex-shrink:0;backdrop-filter:blur(6px);">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z"/></svg>
        </div>
        <div style="flex:1;">
            <div style="font-size:0.92rem;font-weight:700;color:#fff;line-height:1.4;">ما در یک روایت مشترکیم</div>
            <div style="font-size:0.74rem;color:rgba(234,243,239,0.8);margin-top:1px;">با هم در تماس بمانیم</div>
        </div>
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="rgba(255,255,255,0.7)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="flex-shrink:0;"><path d="M15 18l-6-6 6-6"/></svg>
    </div>
</a>

{{-- کارت حال امروز — نارنجیِ گرم و ظریف (آخرین کارت داشبورد) --}}
@php
    $moodLabels = \App\Models\DailyMood::LABELS; // [5=>..., 1=>...]
    $currentMood = $todayMood?->mood;
@endphp
<div class="pk-mood @if($currentMood) is-done-avail @endif" id="pk-mood-card" data-done="{{ $currentMood ? '1' : '0' }}">
    {{-- حالت تشکر (وقتی حالِ امروز قبلاً ثبت شده) — قابل تغییر با زدن دوباره --}}
    <div class="pk-mood__thanks @if(!$currentMood) is-hidden @endif" id="pk-mood-thanks">
        <span class="pk-mood__thanks-chip" aria-hidden="true">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6L9 17l-5-5"/></svg>
        </span>
        <p class="pk-mood__thanks-text">مرسی که از حالت هممونو باخبر کردی. به امید دیدارت.</p>
        <button type="button" class="pk-mood__change" id="pk-mood-change" aria-label="تغییر حال امروز" title="تغییر حال امروز"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M23 4v6h-6M1 20v-6h6"/><path d="M3.51 9a9 9 0 0 1 14.85-3.36L23 10M1 14l4.64 4.36A9 9 0 0 0 20.49 15"/></svg></button>
    </div>

    <form method="POST" action="{{ route('panel.mood.store') }}" class="pk-mood__form @if($currentMood) is-hidden @endif" id="pk-mood-form">
        @csrf
        <input type="hidden" name="mood" id="pk-mood-value" value="{{ $currentMood ?? '' }}">
        <div class="pk-mood__head">
            <span class="pk-mood__icon" aria-hidden="true">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="9"/><path d="M8.5 14.5s1.3 1.5 3.5 1.5 3.5-1.5 3.5-1.5"/><path d="M9 9.5h.01M15 9.5h.01"/></svg>
            </span>
            <div class="pk-mood__titles">
                <div class="pk-mood__title">وضعیت حال الانت چیه؟</div>
                <div class="pk-mood__sub">ما همه حواسمون به هم هست. پس هر چی بگی یه جایی دیده میشه، مطمئن باش.</div>
            </div>
        </div>
        <div class="pk-mood__row" role="radiogroup" aria-label="حال امروز">
            @php
                $faces = [
                    5 => '<circle cx="12" cy="12" r="9.2"/><path d="M8 14.2s1.5 2.2 4 2.2 4-2.2 4-2.2" stroke-linecap="round"/><path d="M8.6 9.2s.5-.7 1.2-.7 1.2.7 1.2.7M13 9.2s.5-.7 1.2-.7 1.2.7 1.2.7" stroke-linecap="round"/>',
                    4 => '<circle cx="12" cy="12" r="9.2"/><path d="M8.5 14s1.4 1.7 3.5 1.7 3.5-1.7 3.5-1.7" stroke-linecap="round"/><path d="M9.2 9.5h.01M14.8 9.5h.01" stroke-linecap="round"/>',
                    3 => '<circle cx="12" cy="12" r="9.2"/><path d="M9 14.6h6" stroke-linecap="round"/><path d="M9.2 9.5h.01M14.8 9.5h.01" stroke-linecap="round"/>',
                    2 => '<circle cx="12" cy="12" r="9.2"/><path d="M8.7 15.4s1.3-1.6 3.3-1.6 3.3 1.6 3.3 1.6" stroke-linecap="round"/><path d="M9.2 9.7h.01M14.8 9.7h.01" stroke-linecap="round"/>',
                    1 => '<circle cx="12" cy="12" r="9.2"/><path d="M8 16s1.5-2.4 4-2.4 4 2.4 4 2.4" stroke-linecap="round"/><path d="M9 10.4l1-1M10 10.4l-1-1M14 10.4l1-1M15 10.4l-1-1" stroke-linecap="round"/>',
                ];
            @endphp
            @foreach($moodLabels as $val => $label)
            <button type="submit" name="mood" value="{{ $val }}"
                class="pk-mood__opt @if($currentMood === $val) is-selected @endif"
                data-mood="{{ $val }}"
                role="radio" aria-checked="{{ $currentMood === $val ? 'true' : 'false' }}"
                aria-label="{{ $label }}">
                <span class="pk-mood__face" aria-hidden="true">
                    <svg width="30" height="30" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">{!! $faces[$val] !!}</svg>
                </span>
                <span class="pk-mood__label">{{ $label }}</span>
            </button>
            @endforeach
        </div>
    </form>
</div>
@endsection

@section('nav')
    @include('panel.partials.bottom-nav', ['active' => 'home'])
@endsection

{{-- ── استایل کارت حال امروز (نارنجیِ گرم و ظریف) ── --}}
@push('styles')
<style>
    .pk-mood {
        --warm-orange: #c2552f;      /* هم‌رنگ با آیکن نارنجیِ فیلم هفته */
        --warm-orange-deep: #a8431f;
        --warm-tint: #fbeee7;        /* پس‌زمینهٔ خیلی روشنِ نارنجی */
        position: relative;
        overflow: hidden;
        background: var(--warm-tint);
        border: 1px solid #f2d9cb;
        border-radius: 18px;
        padding: 0.85rem 0.95rem;
        margin-top: 1.1rem;
        margin-bottom: 1.1rem;
    }
    .pk-mood__head { display: flex; align-items: flex-start; gap: 0.55rem; }
    .pk-mood__icon {
        width: 26px; height: 26px; flex-shrink: 0;
        border-radius: 8px;
        background: rgba(194,85,47,0.10);
        color: var(--warm-orange);
        display: flex; align-items: center; justify-content: center;
    }
    .pk-mood__titles { min-width: 0; }
    .pk-mood__title { font-size: 0.86rem; font-weight: 800; color: var(--ink); letter-spacing: -0.2px; line-height: 1.4; }
    .pk-mood__sub { font-size: 0.66rem; color: var(--ink-faint); line-height: 1.6; margin-top: 2px; }

    .pk-mood__row {
        display: flex; gap: 0.3rem; margin-top: 0.7rem;
    }
    .pk-mood__opt {
        flex: 1; min-width: 0;
        display: flex; flex-direction: column; align-items: center; gap: 3px;
        background: transparent; border: 1px solid transparent;
        border-radius: 12px; padding: 0.4rem 0.15rem;
        cursor: pointer; font-family: inherit;
        color: var(--ink-faint);
        transition: transform 0.15s, background 0.2s, border-color 0.2s, color 0.2s;
        -webkit-tap-highlight-color: transparent;
    }
    .pk-mood__opt:active { transform: scale(0.92); }
    .pk-mood__face { display: flex; align-items: center; justify-content: center; }
    .pk-mood__label { font-size: 0.6rem; font-weight: 600; line-height: 1.3; text-align: center; color: var(--ink-mid); }
    .pk-mood__opt.is-selected {
        background: #fff;
        border-color: var(--warm-orange);
        color: var(--warm-orange);
        box-shadow: 0 3px 12px -4px rgba(194,85,47,0.4);
    }
    .pk-mood__opt.is-selected .pk-mood__label { color: var(--warm-orange-deep); font-weight: 800; }

    /* حالت تشکر */
    .pk-mood__thanks { display: flex; align-items: center; gap: 0.55rem; flex-wrap: wrap; }
    .pk-mood__thanks-chip {
        width: 26px; height: 26px; flex-shrink: 0;
        border-radius: 50%;
        background: var(--warm-orange); color: #fff;
        display: flex; align-items: center; justify-content: center;
    }
    .pk-mood__thanks-text {
        margin: 0; flex: 1; min-width: 120px;
        font-size: 0.8rem; font-weight: 700; line-height: 1.7; color: var(--warm-orange-deep);
    }
    .pk-mood__change {
        background: transparent; border: none; cursor: pointer; font-family: inherit;
        font-size: 0.68rem; font-weight: 700; color: var(--ink-faint);
        text-decoration: underline; padding: 2px 4px;
    }
    .pk-mood .is-hidden { display: none; }
</style>
@endpush

{{-- ── منطق ثبت حال امروز (AJAX با fallback به ارسال عادی) ── --}}
@push('scripts')
<script>
(function () {
    var card = document.getElementById('pk-mood-card');
    if (!card) return;
    var form = document.getElementById('pk-mood-form');
    var thanks = document.getElementById('pk-mood-thanks');
    var changeBtn = document.getElementById('pk-mood-change');
    var valueInput = document.getElementById('pk-mood-value');
    var opts = form ? form.querySelectorAll('.pk-mood__opt') : [];

    function showThanks() {
        if (thanks) thanks.classList.remove('is-hidden');
        if (form) form.classList.add('is-hidden');
    }
    function showForm() {
        if (thanks) thanks.classList.add('is-hidden');
        if (form) form.classList.remove('is-hidden');
    }

    // دکمهٔ «تغییر حال امروز» → نمایش دوبارهٔ گزینه‌ها
    if (changeBtn) {
        changeBtn.addEventListener('click', function () { showForm(); });
    }

    opts.forEach(function (btn) {
        btn.addEventListener('click', function (e) {
            e.preventDefault();
            var mood = btn.getAttribute('data-mood');

            // انتخابِ ظاهری
            opts.forEach(function (o) {
                var on = o === btn;
                o.classList.toggle('is-selected', on);
                o.setAttribute('aria-checked', on ? 'true' : 'false');
            });
            if (valueInput) valueInput.value = mood;

            var token = form.querySelector('input[name="_token"]');
            var csrf = token ? token.value : '';
            var body = new URLSearchParams();
            body.append('mood', mood);
            body.append('_token', csrf);

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
                if (data && data.ok) {
                    showThanks();
                } else {
                    throw new Error('not ok');
                }
            }).catch(function () {
                // در صورت خطای شبکه/AJAX → ارسال عادی فرم (POST + redirect)
                showThanks();
            });
        });
    });
})();
</script>
@endpush

@push('scripts')
<script>
(function () {
    var faDigits = ['۰','۱','۲','۳','۴','۵','۶','۷','۸','۹'];
    // جداکننده را هم فارسی کن
    function faNum(n) {
        var s = String(n).replace(/\B(?=(\d{3})+(?!\d))/g, ',');
        return s.replace(/\d/g, function (d) { return faDigits[d]; }).replace(/,/g, '٬');
    }

    // منبعِ حقیقت: موتورِ شبیه‌سازِ سمتِ سرور. کلاینت دیگر عدد نمی‌سازد؛
    // فقط مقدارِ سرور را می‌گیرد و بینِ هر واکشی یک نوسانِ کاملاً ظاهریِ ±۱
    // (گاهی ۰) دورِ همان مقدار نشان می‌دهد تا حسِ «زنده بودنِ» فعلی حفظ شود.
    var online = {
        el: document.getElementById('stat-online'),
        server: null,   // آخرین مقدارِ واقعیِ سرور
        value: 0        // مقدارِ نمایش‌داده‌شده (server + آفستِ ظاهری در بازهٔ [-۱,+۱])
    };
    var watching = {
        el: document.getElementById('stat-watching'),
        server: null,
        value: 0
    };

    function render(s) {
        if (!s.el) return;
        s.el.classList.add('stat-tick');
        s.el.style.opacity = '0.45';
        setTimeout(function () {
            s.el.textContent = faNum(s.value);
            s.el.style.opacity = '1';
        }, 200);
    }

    // واکشیِ مقدارِ سرور و لنگرگذاریِ مجددِ نمایش روی مقدارِ واقعی
    var STATS_URL = '{{ route('panel.stats.live') }}';
    function poll() {
        return fetch(STATS_URL, { credentials: 'same-origin', headers: { 'Accept': 'application/json' } })
            .then(function (r) { return r.json(); })
            .then(function (d) {
                if (!d || d.ok !== true) return; // خطای داخلی → مقدارِ قبلی حفظ می‌شود
                online.server = d.online;
                online.value  = d.online;
                watching.server = d.watching;
                watching.value  = d.watching;
                render(online);
                render(watching);
            })
            .catch(function () { /* خطای شبکه → مقدارِ قبلی حفظ، تلاش در تیکِ بعد */ });
    }

    // نوسانِ کاملاً ظاهری: آفست در {-۱, ۰, +۱} دورِ مقدارِ سرور، هرگز بیش از ±۱.
    function cosmeticTick(s) {
        if (s.server === null) return; // پیش از اولین واکشیِ موفق، همان placeholderِ ۰
        var offsets = [-1, 0, 1];
        var next = s.server + offsets[Math.floor(Math.random() * offsets.length)];
        if (next < 0) next = 0;
        s.value = next;
        render(s);
    }

    // واکشیِ اولیه، سپس نوسانِ ظاهری با همان کادنسِ فعلی (۳۵۰۰ / ۵۲۰۰ms)
    // و لنگرگذاریِ مجدد با واکشیِ متناوب (آنلاین هر ~۱۵s، تماشا هر ~۲۰s).
    poll();
    setInterval(function () { cosmeticTick(online); }, 3500);
    setInterval(function () { cosmeticTick(watching); }, 5200);
    setInterval(poll, 15000);
    setInterval(poll, 20000);
})();
</script>
@endpush

@push('scripts')
{{-- مقادیر سمت سرورِ گیاه عضویت --}}
<script>
  window.__plant = {
    seed: {{ (int) $member->id }},
    day: {{ (int) $plantDay }},
    absence: {{ (int) $plantAbsence }}
  };
</script>

{{--
  گیاه عضویت — Space Colonization + چرخهٔ فصلیِ ۱۸۰ روزه + پژمردگی.
  نسخهٔ production: بدون حلقهٔ rAF. درخت یک‌بار ساخته و یک‌بار (و روی resizeِ debounce) کشیده می‌شود.
--}}
<script>
(function () {
  var cv = document.getElementById('pk-plant-cv');
  if (!cv || !cv.getContext) return;
  var ctx = cv.getContext('2d');

  // ───────────── ابزار پایه ─────────────
  function mulberry32(a){return function(){a|=0;a=a+0x6D2B79F5|0;let t=Math.imul(a^a>>>15,1|a);t=t+Math.imul(t^t>>>7,61|t)^t;return((t^t>>>14)>>>0)/4294967296;};}

  const VW=700, VH=520;
  const ROOT={x:70, y:VH-24};   // بذر: گوشهٔ پایین-چپ

  // ───── الگوریتم Space Colonization ─────
  function growTree(seed){
    const rng=mulberry32(seed);
    const attractors=[];
    const N=2600;
    for(let i=0;i<N;i++){
      const ry=Math.pow(rng(),0.72);
      const y=VH*0.06 + ry*(VH*0.86);
      const x=20 + Math.pow(rng(),0.9)*(VW-40);
      attractors.push({x,y,dead:false});
    }
    const INF=88, KILL=9, STEP=7;
    const nodes=[{x:ROOT.x,y:ROOT.y,parent:-1}];
    const maxIter=760;
    for(let it=0;it<maxIter;it++){
      const pull=new Map(); let live=0;
      for(const a of attractors){
        if(a.dead) continue; live++;
        let best=-1,bd=INF*INF;
        for(let n=0;n<nodes.length;n++){
          const dx=a.x-nodes[n].x, dy=a.y-nodes[n].y, d=dx*dx+dy*dy;
          if(d<bd){bd=d;best=n;}
        }
        if(best>=0){
          const dx=a.x-nodes[best].x, dy=a.y-nodes[best].y, L=Math.hypot(dx,dy)||1;
          const e=pull.get(best)||{x:0,y:0,c:0};
          e.x+=dx/L; e.y+=dy/L; e.c++; pull.set(best,e);
        }
      }
      if(live===0||pull.size===0) break;
      let grew=false;
      for(const [ni,e] of pull){
        let dx=e.x/e.c, dy=e.y/e.c;
        dy-=0.14; dx+=(rng()-0.5)*0.28; dy+=(rng()-0.5)*0.28;
        const L=Math.hypot(dx,dy)||1;
        const nx=nodes[ni].x+dx/L*STEP, ny=nodes[ni].y+dy/L*STEP;
        nodes.push({x:nx,y:ny,parent:ni}); grew=true;
      }
      if(!grew) break;
      for(const a of attractors){
        if(a.dead) continue;
        for(let n=0;n<nodes.length;n++){
          const dx=a.x-nodes[n].x, dy=a.y-nodes[n].y;
          if(dx*dx+dy*dy < KILL*KILL){a.dead=true;break;}
        }
      }
    }
    const nn=nodes.length;
    const sub=new Array(nn).fill(1);
    for(let i=nn-1;i>=0;i--){ if(nodes[i].parent>=0) sub[nodes[i].parent]+=sub[i]; }
    const dist=new Array(nn).fill(0), depth=new Array(nn).fill(0);
    for(let i=1;i<nn;i++){
      const p=nodes[i].parent;
      dist[i]=dist[p]+Math.hypot(nodes[i].x-nodes[p].x, nodes[i].y-nodes[p].y);
      depth[i]=depth[p]+1;
    }
    const maxDist=Math.max(...dist,1);
    const segs=[];
    for(let i=1;i<nn;i++){
      const p=nodes[i].parent;
      segs.push({x1:nodes[p].x,y1:nodes[p].y,x2:nodes[i].x,y2:nodes[i].y,
        order:dist[i]/maxDist, sub:sub[i], depth:depth[i]});
    }
    const tips=[];
    for(let i=1;i<nn;i++){
      if(sub[i]<=4 && depth[i]>3){
        tips.push({x:nodes[i].x,y:nodes[i].y,order:dist[i]/maxDist,
          dirx:nodes[i].x-nodes[nodes[i].parent].x, diry:nodes[i].y-nodes[nodes[i].parent].y});
      }
    }
    const maxSub=Math.max(...segs.map(s=>s.sub),1);
    return {segs,tips,maxSub};
  }

  // ───── مدل فصلی (چرخهٔ بی‌پایان ۱۸۰ روزه) ─────
  const CYCLE=180;
  function seasonModel(day){
    const cycle=Math.floor(day/CYCLE);
    const t=day%CYCLE;
    let leafFill,bloom,phase;
    if(t<90){ leafFill=Math.min(1,t/60); bloom=Math.max(0,Math.min(1,(t-40)/38)); phase=t<28?'رویش':(t<58?'پرشدن':'شکوفه'); }
    else{ const w=(t-90)/90; leafFill=Math.max(0.08,1-w*0.94); bloom=Math.max(0,(1-w)*0.3); phase=w<0.5?'پاییز':'زمستان'; }
    const structure=Math.min(1,(1-Math.exp(-day/220))*1.06);
    const lush=Math.min(1.35, 0.85+cycle*0.18);
    return {cycle,t,leafFill,bloom,phase,structure,lush};
  }

  // ───── رندر ─────
  // seedVal = member id ; day = daysSinceJoin ; absence = daysSinceSeen  (از سرور)
  // نسخهٔ production: بدونِ حلقهٔ rAF. dispStruct/dispLeaf مستقیم = هدف. draw() یک‌بار.
  let TREE=null;
  var P = window.__plant || {};
  let seedVal = (P.seed|0) || 1;
  let day     = (P.day|0)  || 0;
  let absence = (P.absence|0) || 0;

  function build(){ TREE=growTree(seedVal); }
  function fit(){
    const r=cv.getBoundingClientRect(); const dpr=Math.min(2,window.devicePixelRatio||1);
    cv.width=Math.max(1,r.width*dpr); cv.height=Math.max(1,r.height*dpr);
    ctx.setTransform(dpr,0,0,dpr,0,0); return {w:r.width,h:r.height};
  }
  function draw(){
    const {w,h}=fit();
    ctx.clearRect(0,0,w,h);
    const sx=w/VW, sy=h/VH;
    const S=seasonModel(day);
    const health=Math.max(0,Math.min(1,1-Math.max(0,absence-2)/14));
    const struct=S.structure, leafFill=S.leafFill;   // production: بدون تویین

    const droop=(1-health);
    ctx.save();
    ctx.translate(ROOT.x*sx,ROOT.y*sy);
    ctx.rotate(droop*0.14);
    ctx.translate(-ROOT.x*sx,-ROOT.y*sy);

    const gA=`rgba(${190-30*(1-health)},${205-24*(1-health)},${190-40*(1-health)},`;
    const dry=`rgba(150,140,120,`;
    const branchRGBA = absence>5 ? dry : gA;

    for(const s of TREE.segs){
      if(s.order>struct) continue;
      const isFine = s.sub<=3 && s.depth>5;
      if(isFine){ if(leafFill<0.35 && s.order>0.55) continue; }
      const local=Math.min(1,(struct-s.order)/0.05);
      const x1=s.x1*sx, y1=s.y1*sy;
      const x2=(s.x1+(s.x2-s.x1)*local)*sx, y2=(s.y1+(s.y2-s.y1)*local)*sy;
      const wdt=0.35 + 2.0*Math.pow(s.sub/TREE.maxSub,0.5);
      let alpha = 0.28 + 0.5*Math.min(1,s.sub/40);
      if(isFine) alpha*= (0.4+0.6*leafFill);
      ctx.strokeStyle=branchRGBA+alpha.toFixed(3)+')';
      ctx.lineWidth=wdt; ctx.lineCap='round';
      ctx.beginPath(); ctx.moveTo(x1,y1); ctx.lineTo(x2,y2); ctx.stroke();
    }

    const leafRng=mulberry32(seedVal*7+13);
    const lush=S.lush;
    for(const tp of TREE.tips){
      if(tp.order>struct) continue;
      const cnt=Math.round((3+9*leafFill)*lush);
      for(let k=0;k<cnt;k++){
        const rr=leafRng();
        if(rr>leafFill*0.98) continue;
        const spread=7+9*leafFill;
        const ox=(leafRng()-0.5)*spread + tp.dirx*0.5, oy=(leafRng()-0.5)*spread + tp.diry*0.5;
        const x=(tp.x+ox)*sx, y=(tp.y+oy)*sy;
        const r=(0.5+leafRng()*1.2);
        let col;
        if(absence>5){ col=`rgba(${175-leafRng()*35},${152-leafRng()*30},${92+leafRng()*32},`; }
        else { const g=198+leafRng()*40; col=`rgba(${g-22},${g},${g-26},`; }
        ctx.fillStyle=col+((0.42*leafFill)*(0.45+0.55*leafRng())).toFixed(3)+')';
        ctx.beginPath(); ctx.ellipse(x,y,r*1.25,r*0.78,leafRng()*3.14,0,6.283); ctx.fill();
      }
    }

    const bRng=mulberry32(seedVal*11+29);
    const bloomHealth=S.bloom*health;
    const pal=[[242,170,188],[240,158,132],[248,214,222],[238,180,158],[244,175,196]];
    if(bloomHealth>0.02){
      for(const tp of TREE.tips){
        if(tp.order>struct) continue;
        if(bRng() > bloomHealth*0.28) continue;
        const c=pal[(bRng()*pal.length)|0];
        const x=tp.x*sx, y=tp.y*sy, r=(1.1+1.5*S.bloom);
        ctx.fillStyle=`rgba(${c[0]},${c[1]},${c[2]},${(0.5+0.35*bloomHealth).toFixed(3)})`;
        ctx.beginPath(); ctx.arc(x,y,r,0,6.283); ctx.fill();
      }
    }
    ctx.restore();
  }

  build();
  draw();
  let _t; window.addEventListener('resize',function(){ clearTimeout(_t); _t=setTimeout(draw,150); });
})();
</script>
@endpush
