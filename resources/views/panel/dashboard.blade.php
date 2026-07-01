@extends('panel.layouts.app')
@section('title', 'داشبورد')

@section('content')
{{-- هدر --}}
<div class="topbar">
    <div class="greeting">
        @php
            $h = (int) \Carbon\Carbon::now('Asia/Tehran')->format('H');
            $greet = $h < 5 ? 'شب بخیر،' : ($h < 12 ? 'صبح بخیر،' : ($h < 17 ? 'ظهر بخیر،' : ($h < 20 ? 'عصر بخیر،' : 'شب بخیر،')));
        @endphp
        <div class="hi">{{ $greet }}</div>
        <div class="name">{{ $member->full_name }}</div>
    </div>
    <a href="{{ route('panel.messages.index') }}" class="bell-btn {{ ($unreadMessages ?? 0) > 0 ? 'has-unread' : '' }}">
        @if(($unreadMessages ?? 0) > 0)
            <span class="bell-badge">{{ fa($unreadMessages) }}</span>
        @endif
        <svg class="bell-svg" width="21" height="21" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><path d="M6 8a6 6 0 0 1 12 0c0 7 3 9 3 9H3s3-2 3-9"/><path d="M10.3 21a1.94 1.94 0 0 0 3.4 0"/></svg>
    </a>
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
</style>
@endpush

{{-- بنر دعوت به ارتباط --}}
<a href="{{ route('panel.messages.index') }}" style="display:block;text-decoration:none;margin-bottom:1.1rem;position:relative;overflow:hidden;border-radius:18px;background:linear-gradient(145deg,var(--pine-bright),var(--pine-deep));box-shadow:0 10px 26px -10px rgba(47,93,80,0.5);">
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

{{-- نوار آمار زنده --}}
<div style="display:flex;gap:0.6rem;margin-bottom:1.1rem;">
    <div style="flex:1;display:flex;align-items:center;gap:0.55rem;background:#fff;border:1px solid var(--border);border-radius:14px;padding:0.6rem 0.7rem;">
        <span style="position:relative;display:flex;width:8px;height:8px;flex-shrink:0;">
            <span style="position:absolute;width:100%;height:100%;border-radius:50%;background:#3fb27f;opacity:0.6;animation:pulse-ring 2s infinite;"></span>
            <span style="position:relative;width:8px;height:8px;border-radius:50%;background:#3fb27f;"></span>
        </span>
        <div style="min-width:0;">
            <div id="stat-online" style="font-size:0.92rem;font-weight:800;color:var(--ink);line-height:1.1;">۰</div>
            <div style="font-size:0.63rem;color:var(--ink-faint);">نفر آنلاین‌اند</div>
        </div>
    </div>
    <div style="flex:1;display:flex;align-items:center;gap:0.55rem;background:#fff;border:1px solid var(--border);border-radius:14px;padding:0.6rem 0.7rem;">
        <div style="width:26px;height:26px;border-radius:8px;background:var(--green-tint);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="var(--pine)" stroke-width="1.8"><rect x="2" y="3" width="20" height="14" rx="2"/><path d="M8 21h8M12 17v4"/></svg>
        </div>
        <div style="min-width:0;">
            <div id="stat-watching" style="font-size:0.92rem;font-weight:800;color:var(--ink);line-height:1.1;">۰</div>
            <div style="font-size:0.63rem;color:var(--ink-faint);">در حال دیدن فیلم هفته</div>
        </div>
    </div>
</div>
@php
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
@endphp
<div style="border:1px solid #ededeb;border-radius:28px;padding:1.75rem 1.4rem 1.5rem;display:flex;flex-direction:column;align-items:center;background:linear-gradient(180deg,#ffffff,#fbfcfb);box-shadow:0 1px 0 #fff,0 20px 40px -34px rgba(47,93,80,0.5);margin-bottom:1rem;">
    {{-- حلقه --}}
    <div style="position:relative;width:188px;height:188px;">
        <svg width="188" height="188" viewBox="0 0 188 188">
            <defs><linearGradient id="pkg" x1="0" y1="0" x2="1" y2="1"><stop offset="0" stop-color="#3f7a68"/><stop offset="1" stop-color="#1f4d40"/></linearGradient></defs>
            <circle cx="94" cy="94" r="76" fill="none" stroke="#eef1ef" stroke-width="12"/>
            <circle cx="94" cy="94" r="76" fill="none" stroke="url(#pkg)" stroke-width="12" stroke-linecap="round"
                stroke-dasharray="477.5" stroke-dashoffset="{{ $dashoffset }}" transform="rotate(-90 94 94)"
                style="animation:pkring 1.4s cubic-bezier(.5,0,.1,1) .3s both;"/>
        </svg>
        <div style="position:absolute;inset:0;display:flex;flex-direction:column;align-items:center;justify-content:center;">
            <div style="font-size:0.62rem;letter-spacing:3px;color:var(--ink-faint);font-weight:700;">لایهٔ عضویت</div>
            <div style="font-size:2rem;font-weight:800;color:var(--pine);line-height:1.05;margin-top:3px;letter-spacing:-0.5px;">{{ $layer?->name ?? 'مهمان' }}</div>
            <div style="display:flex;align-items:baseline;gap:4px;margin-top:4px;">
                <b style="font-size:1.15rem;">{{ fa(number_format($score)) }}</b>
                <span style="font-size:0.7rem;color:var(--ink-dim);">امتیاز</span>
            </div>
        </div>
    </div>

    @if($toNext && $nextLayer)
        <div style="margin-top:6px;font-size:0.78rem;color:var(--ink-dim);">{{ fa(number_format($toNext)) }} امتیاز تا لایهٔ <b style="color:var(--pine);">{{ $nextLayer->name }}</b></div>
    @else
        <div style="margin-top:6px;font-size:0.78rem;color:var(--pine);font-weight:600;">بالاترین لایه 🏆</div>
    @endif

    {{-- نردبان لایه‌ها --}}
    @if($allLayers->count() > 1)
    <div style="margin-top:1.1rem;width:100%;display:flex;align-items:center;">
        @foreach($allLayers as $i => $l)
            @php
                $isPast = $i < $currentIndex;
                $isCurrent = $i === $currentIndex;
                $dotColor = ($isPast || $isCurrent) ? 'var(--pine)' : '#dfe3e1';
            @endphp
            {{-- نقطه --}}
            <div style="display:flex;flex-direction:column;align-items:center;gap:6px;flex:1;">
                @if($isCurrent)
                    <span style="width:16px;height:16px;border-radius:50%;background:var(--pine);border:4px solid #d3e3dd;box-shadow:0 0 0 1px var(--pine);"></span>
                    <span style="font-size:0.62rem;color:var(--pine);font-weight:800;">{{ $l->name }}</span>
                @else
                    <span style="width:11px;height:11px;border-radius:50%;background:{{ $dotColor }};"></span>
                    <span style="font-size:0.62rem;color:var(--ink-faint);">{{ $l->name }}</span>
                @endif
            </div>
            {{-- خط اتصال --}}
            @if(!$loop->last)
                @php
                    if ($i < $currentIndex) { $lineBg = 'var(--pine)'; }
                    elseif ($i === $currentIndex) { $lineBg = 'linear-gradient(270deg,var(--pine) ' . round($progress) . '%,#e4e7e5 ' . round($progress) . '%)'; }
                    else { $lineBg = '#e4e7e5'; }
                @endphp
                <div style="height:2px;flex:1;background:{{ $lineBg }};margin-bottom:17px;border-radius:2px;"></div>
            @endif
        @endforeach
    </div>
    @endif
</div>

{{-- باکس‌های پادکست و فیلم امروز --}}
@php
    $todayFilm = \App\Models\DailyFilm::where('is_active', true)->latest('show_date')->first();
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
            <div style="font-size:0.72rem;color:rgba(234,243,239,0.85);margin-top:2px;">عدم قطعیت</div>
        </div>
    </a>

    {{-- فیلم امروز --}}
    <a href="{{ $todayFilm ? route('panel.film.today') : '#' }}" style="flex:1;position:relative;overflow:hidden;text-decoration:none;color:#fff;border-radius:20px;background:linear-gradient(145deg,#d06236,#a8431f);padding:1.1rem 1.05rem;min-height:128px;display:flex;flex-direction:column;box-shadow:0 10px 26px -12px rgba(168,67,31,0.5);{{ $todayFilm ? '' : 'opacity:0.92;' }}">
        <div style="position:absolute;top:-30px;right:-25px;width:90px;height:90px;border-radius:50%;background:rgba(255,255,255,0.08);"></div>
        <div style="position:absolute;bottom:-35px;left:-15px;width:80px;height:80px;border-radius:50%;background:rgba(255,255,255,0.05);"></div>
        <div style="position:relative;display:flex;align-items:center;justify-content:space-between;">
            <div style="width:40px;height:40px;border-radius:13px;background:rgba(255,255,255,0.18);display:flex;align-items:center;justify-content:center;backdrop-filter:blur(6px);">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="1.7"><rect x="2" y="2" width="20" height="20" rx="2.5"/><path d="M7 2v20M17 2v20M2 12h20M2 7h5M2 17h5M17 17h5M17 7h5"/></svg>
            </div>
        </div>
        <div style="position:relative;margin-top:auto;">
            <div style="font-size:1rem;font-weight:800;">فیلم امروز</div>
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
    <a href="{{ route('panel.posts.show', $post) }}" style="display:flex;gap:0.85rem;align-items:flex-start;padding:0.95rem 0;{{ !$loop->last ? 'border-bottom:1.5px dashed #eceeec;' : '' }}text-decoration:none;color:inherit;">
        {{-- کاور سمت راست --}}
        @if($post->cover_src)
            <img src="{{ $post->cover_src }}" alt="" style="width:100px;height:140px;border-radius:12px;object-fit:cover;flex:0 0 100px;background:var(--green-soft);">
        @else
            <div style="width:100px;height:140px;border-radius:12px;background:var(--green-soft);display:flex;align-items:center;justify-content:center;flex:0 0 100px;">
                <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="var(--pine)" stroke-width="1.5"><path d="M4 4h16v16H4zM4 9h16M9 4v16"/></svg>
            </div>
        @endif
        <div style="flex:1;min-width:0;">
            <div style="font-size:0.96rem;font-weight:800;line-height:1.4;">{{ $post->title }}</div>
            <div style="font-size:0.79rem;color:var(--ink-mid);line-height:1.65;margin-top:0.35rem;text-align:justify;">{{ \Illuminate\Support\Str::limit($post->summary, 88) }}</div>
            <div style="display:flex;align-items:center;gap:0.5rem;font-size:0.68rem;color:var(--ink-faint);margin-top:0.45rem;">
                <span>{{ pdate($post->published_at ?? $post->created_at, 'j F') }}</span>
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
@endsection

@section('nav')
    @include('panel.partials.bottom-nav', ['active' => 'home'])
@endsection

@push('scripts')
<script>
(function () {
    var faDigits = ['۰','۱','۲','۳','۴','۵','۶','۷','۸','۹'];
    function toFa(n) {
        return String(n).replace(/\d/g, function (d) { return faDigits[d]; })
                        .replace(/\B(?=(\d{3})+(?!\d))/g, '٬'); // جداکننده هزارگان فارسی
    }
    // جداکننده را هم فارسی کن
    function faNum(n) {
        var s = String(n).replace(/\B(?=(\d{3})+(?!\d))/g, ',');
        return s.replace(/\d/g, function (d) { return faDigits[d]; }).replace(/,/g, '٬');
    }

    // ── الگوریتم پایه بر اساس ساعت روز (وقت تهران) ──
    function baseFor(hour, peak, low) {
        // منحنی نرم: کف ساعت ۵ صبح، اوج ساعت ۲۲
        var t = (hour - 5 + 24) % 24;
        var frac = Math.sin((t / 24) * Math.PI); // 0..1..0
        var val = low + (peak - low) * frac;

        // ساعت ۲ تا ۹ صبح به وقت ایران: همه خواب‌اند → آمار خیلی کم
        if (hour >= 2 && hour < 9) {
            // عمیق‌ترین کف حدود ۵-۶ صبح
            var deep = 1 - Math.sin(((hour - 2) / 7) * Math.PI) * 0.7; // 1..0.3..1
            val = val * 0.18 * deep + low * 0.12;
        }
        return Math.round(val);
    }

    var now = new Date();
    var hour = {{ (int) \Carbon\Carbon::now('Asia/Tehran')->format('G') }}; // ساعت تهران از سرور

    // اعداد پایه (فیک ولی معقول) — می‌توانی بعداً peak/low را تغییر دهی
    var online = {
        el: document.getElementById('stat-online'),
        value: baseFor(hour, 480, 320),
        jitter: 5   // دامنه نوسان هر تیک
    };
    var watching = {
        el: document.getElementById('stat-watching'),
        value: baseFor(hour, 65, 35),
        jitter: 3
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

    // مقدار اولیه
    online.el && (online.el.textContent = faNum(online.value));
    watching.el && (watching.el.textContent = faNum(watching.value));

    // ── تغییر زنده و آرام ──
    function step(s, floor) {
        // حرکت آرام: بیشتر اوقات +/- کم، گاهی صفر
        var delta = Math.round((Math.random() - 0.45) * s.jitter);
        s.value = Math.max(floor, s.value + delta);
        render(s);
    }

    // هر چند ثانیه یکی را به‌روز کن (نه هم‌زمان، تا طبیعی باشد)
    // کف نوسان نسبت به مقدار اولیه تنظیم می‌شود (تا در ساعات خلوت هم منطقی بماند)
    var onlineFloor = Math.max(20, Math.round(online.value * 0.85));
    var watchingFloor = Math.max(5, Math.round(watching.value * 0.8));

    setInterval(function () { step(online, onlineFloor); }, 3500);
    setInterval(function () { step(watching, watchingFloor); }, 5200);
})();
</script>
@endpush
