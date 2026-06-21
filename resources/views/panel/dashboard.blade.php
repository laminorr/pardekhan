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

{{-- کارت عضویت با حلقه پیشرفت --}}
@php
    $layer = $member->layer;
    $score = $member->score;
    $allLayers = \App\Models\Layer::active()->orderBy('min_score')->get();
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

    // ایندکس لایه فعلی برای نردبان
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

{{-- آمار سریع --}}
@php
    $eventsAttended = $member->registrations()->where('attendance_status', 'attended')->count();
    $activeTickets = \App\Models\Ticket::where('member_id', $member->id)->where('status', 'active')->count();
@endphp
@php
    $stats = [
        ['val' => fa($eventsAttended), 'unit' => '', 'cap' => 'دورهمی'],
        ['val' => fa($activeTickets), 'unit' => '', 'cap' => 'بلیت فعال'],
        ['val' => fa(number_format($member->wallet_balance / 1000)), 'unit' => ' هزار', 'cap' => 'کیف پول'],
    ];
@endphp
<div style="margin-top:1.5rem;display:flex;align-items:stretch;text-align:center;">
    @foreach($stats as $i => $s)
        <div style="flex:1;display:flex;flex-direction:column;justify-content:center;min-height:54px;">
            <div style="font-size:1.45rem;font-weight:800;letter-spacing:-0.5px;color:var(--ink);line-height:1;text-shadow:0 2px 6px rgba(47,93,80,0.1);">{{ $s['val'] }}@if($s['unit'])<span style="font-size:0.64rem;color:var(--ink-dim);font-weight:600;text-shadow:none;">{{ $s['unit'] }}</span>@endif</div>
            <div style="font-size:0.7rem;color:var(--ink-dim);margin-top:7px;line-height:1;">{{ $s['cap'] }}</div>
        </div>
        @if($i < 2)
            <div style="width:1px;background:linear-gradient(180deg,transparent,#c4ccc8 18%,#b6beba 50%,#c4ccc8 82%,transparent);"></div>
        @endif
    @endforeach
</div>

{{-- منوی دسترسی سریع --}}
<div class="section-head"><div class="section-title">دسترسی سریع</div></div>
<div style="display:grid;grid-template-columns:1fr 1fr;gap:0.8rem;">
    @php
        $menuItems = [
            ['دورهمی‌ها', 'مشاهده و ثبت‌نام', route('panel.events.index'), 'M2 4h20v16H2zM7 4v16M17 4v16M2 9h5M2 15h5M17 9h5M17 15h5', 'green'],
            ['بلیت‌های من', 'بلیت‌های فعال', route('panel.tickets.index'), 'M3 9a2 2 0 0 0 0 6v2a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-2a2 2 0 0 1 0-6V7a2 2 0 0 0-2-2H5a2 2 0 0 0-2 2z', 'burnt'],
            ['کیف پول', fa(number_format($member->wallet_balance)) . ' ت', '#', 'M2 5h20v14H2zM2 10h20', 'green'],
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
    $suggested = \App\Models\Event::where('status', 'active')->where('starts_at', '>', now())->orderBy('starts_at')->first();
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
@endsection

@section('nav')
    @include('panel.partials.bottom-nav', ['active' => 'home'])
@endsection
