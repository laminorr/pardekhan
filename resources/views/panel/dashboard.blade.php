@extends('panel.layouts.app')
@section('title', 'داشبورد')

@section('content')
{{-- هدر --}}
<div class="topbar">
    <div class="greeting">
        <div class="hi">{{ now()->hour < 12 ? 'صبح بخیر' : (now()->hour < 18 ? 'عصر بخیر' : 'شب بخیر') }}</div>
        <div class="name">{{ $member->full_name }}</div>
    </div>
    <a href="{{ route('panel.messages.index') }}" class="icon-btn">
        @if(($unreadMessages ?? 0) > 0)<span class="ndot"></span>@endif
        <svg width="21" height="21" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><path d="M6 8a6 6 0 0 1 12 0c0 7 3 9 3 9H3s3-2 3-9"/><path d="M10.3 21a1.94 1.94 0 0 0 3.4 0"/></svg>
    </a>
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
<div style="background:var(--dark);border-radius:24px;padding:1.4rem;color:var(--paper);position:relative;overflow:hidden;box-shadow:0 14px 36px rgba(28,24,20,0.32);">
    {{-- هاله شرابی --}}
    <div style="position:absolute;inset:0;background:radial-gradient(120% 80% at 100% 0%, rgba(142,36,32,0.4), transparent 60%);"></div>

    <div style="position:relative;display:flex;justify-content:space-between;align-items:flex-start;">
        <div>
            <div style="font-size:0.7rem;letter-spacing:3px;color:var(--gold);font-weight:600;">لایهٔ عضویت</div>
            <div class="serif" style="font-size:2.5rem;font-weight:600;line-height:1;margin-top:4px;color:var(--paper);">{{ $layer?->name ?? 'مهمان' }}</div>
        </div>
        {{-- آواتار با حلقه طلایی --}}
        <div style="width:56px;height:56px;border-radius:50%;border:1.5px solid var(--gold);display:flex;align-items:center;justify-content:center;overflow:hidden;flex-shrink:0;">
            @if($member->avatar && $member->avatar_approved)
                <img src="{{ Storage::url($member->avatar) }}" style="width:100%;height:100%;object-fit:cover;border-radius:50%;">
            @else
                <span class="serif" style="font-size:1.7rem;color:#d9b884;">{{ mb_substr($member->first_name, 0, 1) }}</span>
            @endif
        </div>
    </div>

    {{-- خط جداکننده --}}
    <div style="position:relative;height:1px;background:linear-gradient(90deg,transparent,#5a4a36,transparent);margin:1rem 0;"></div>

    {{-- امتیاز --}}
    <div style="position:relative;display:flex;align-items:baseline;gap:8px;">
        <span style="font-size:2.1rem;font-weight:800;color:var(--paper);">{{ number_format($score) }}</span>
        <span style="font-size:0.82rem;color:var(--gold-2);">امتیاز</span>
        @if($toNext)
            <span style="margin-right:auto;font-size:0.75rem;color:var(--gold);">{{ number_format($toNext) }} تا {{ $nextLayer->name }}</span>
        @endif
    </div>

    {{-- نوار پیشرفت --}}
    @if($toNext)
    <div style="position:relative;margin-top:0.85rem;height:6px;background:rgba(255,255,255,0.1);border-radius:99px;overflow:hidden;">
        <div style="height:100%;width:{{ $progress }}%;background:linear-gradient(90deg,var(--burnt),var(--gold));border-radius:99px;"></div>
    </div>
    @endif
</div>

{{-- آمار سریع --}}
@php
    $eventsAttended = $member->registrations()->where('attendance_status', 'attended')->count();
    $activeTickets = \App\Models\Ticket::where('member_id', $member->id)->where('status', 'active')->count();
@endphp
<div style="margin-top:1rem;display:flex;background:var(--cream-2);border:1px solid var(--border);border-radius:18px;overflow:hidden;">
    <div style="flex:1;padding:0.9rem 0.5rem;text-align:center;">
        <div style="font-size:1.4rem;font-weight:800;color:var(--ink);">{{ $eventsAttended }}</div>
        <div style="font-size:0.68rem;color:var(--ink-dim);margin-top:2px;">دورهمی رفته</div>
    </div>
    <div style="width:1px;background:var(--border);"></div>
    <div style="flex:1;padding:0.9rem 0.5rem;text-align:center;">
        <div style="font-size:1.4rem;font-weight:800;color:var(--ink);">{{ $activeTickets }}</div>
        <div style="font-size:0.68rem;color:var(--ink-dim);margin-top:2px;">بلیت فعال</div>
    </div>
    <div style="width:1px;background:var(--border);"></div>
    <div style="flex:1;padding:0.9rem 0.5rem;text-align:center;">
        <div style="font-size:1.4rem;font-weight:800;color:var(--ink);">{{ number_format($member->wallet_balance / 1000) }}<span style="font-size:0.6rem;font-weight:500;color:var(--ink-dim);"> هزار</span></div>
        <div style="font-size:0.68rem;color:var(--ink-dim);margin-top:2px;">کیف پول</div>
    </div>
</div>

{{-- منوی دسترسی سریع --}}
<div class="section-head"><div class="section-title">دسترسی سریع</div></div>
<div style="display:grid;grid-template-columns:1fr 1fr;gap:0.8rem;">
    @php
        $menuItems = [
            ['دورهمی‌ها', 'مشاهده و ثبت‌نام', route('panel.events.index'), 'M2 4h20v16H2zM7 4v16M17 4v16M2 9h5M2 15h5M17 9h5M17 15h5', 'green'],
            ['بلیت‌های من', 'بلیت‌های فعال', route('panel.tickets.index'), 'M3 9a2 2 0 0 0 0 6v2a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-2a2 2 0 0 1 0-6V7a2 2 0 0 0-2-2H5a2 2 0 0 0-2 2z', 'wine'],
            ['کیف پول', number_format($member->wallet_balance) . ' ت', '#', 'M2 5h20v14H2zM2 10h20', 'burnt'],
            ['پیام‌ها', ($unreadMessages ?? 0) > 0 ? ($unreadMessages . ' جدید') : 'بدون پیام جدید', route('panel.messages.index'), 'M2 4h20v16H2zM3 7l9 6 9-6', 'green'],
        ];
    @endphp
    @foreach($menuItems as [$title, $desc, $url, $icon, $color])
    <a href="{{ $url }}" style="background:var(--cream-2);border:1px solid var(--border);border-radius:18px;padding:1.1rem;text-decoration:none;color:inherit;position:relative;">
        <div style="width:44px;height:44px;border-radius:13px;display:flex;align-items:center;justify-content:center;margin-bottom:0.8rem;
            @if($color==='green') background:#e0ece4;color:var(--green);
            @elseif($color==='wine') background:#f7e3e0;color:var(--wine);
            @else background:#fbe8da;color:var(--burnt); @endif">
            <svg width="23" height="23" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><path d="{{ $icon }}"/></svg>
        </div>
        <div style="font-size:0.98rem;font-weight:700;color:var(--ink);">{{ $title }}</div>
        <div style="font-size:0.72rem;color:var(--ink-dim);margin-top:2px;">{{ $desc }}</div>
    </a>
    @endforeach
</div>

{{-- دورهمی پیشنهادی --}}
@php
    $suggested = \App\Models\Event::where('status', 'active')
        ->where('starts_at', '>', now())
        ->orderBy('starts_at')
        ->first();
@endphp
@if($suggested)
<div class="section-head">
    <div class="section-title">دورهمی پیشنهادی</div>
    <a href="{{ route('panel.events.index') }}" class="see-all">دیدن همه ›</a>
</div>
<a href="{{ route('panel.events.show', $suggested) }}" style="display:block;text-decoration:none;color:inherit;background:var(--cream-2);border:1px solid var(--border);border-radius:22px;overflow:hidden;">
    <div style="height:130px;position:relative;background:linear-gradient(135deg,var(--green-deep),var(--green));">
        @if($suggested->image)
            <img src="{{ Storage::url($suggested->image) }}" style="width:100%;height:100%;object-fit:cover;">
            <div style="position:absolute;inset:0;background:linear-gradient(to top,rgba(28,24,20,0.5),transparent 60%);"></div>
        @endif
        <span style="position:absolute;top:0.8rem;right:0.8rem;background:var(--cream);color:var(--green);font-size:0.68rem;font-weight:700;padding:4px 11px;border-radius:99px;display:inline-flex;align-items:center;gap:4px;">
            <svg width="11" height="11" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2l2.6 6.6L21 9.2l-5 4.5 1.5 7L12 17l-5.5 3.7L8 13.7l-5-4.5 6.4-.6z"/></svg>
            ویژهٔ لایهٔ شما
        </span>
    </div>
    <div style="padding:1.1rem 1.2rem 1.3rem;">
        <div class="serif" style="font-size:1.4rem;font-weight:600;color:var(--ink);line-height:1.3;">{{ $suggested->title }}</div>
        @if($suggested->subtitle)
            <div style="font-size:0.8rem;color:var(--ink-dim);margin-top:2px;">{{ $suggested->subtitle }}</div>
        @endif
        <div style="display:flex;align-items:center;gap:1rem;margin-top:0.85rem;font-size:0.76rem;color:var(--ink-dim);">
            <span style="display:flex;align-items:center;gap:4px;">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="var(--green)" stroke-width="1.8"><rect x="3" y="4" width="18" height="18" rx="2"/><path d="M16 2v4M8 2v4M3 10h18"/></svg>
                {{ \Morilog\Jalali\Jalalian::fromDateTime($suggested->starts_at)->format('j F') }} · {{ $suggested->starts_at->format('H:i') }}
            </span>
            @if($suggested->venue)
            <span style="display:flex;align-items:center;gap:4px;">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="var(--green)" stroke-width="1.8"><path d="M21 10c0 7-9 12-9 12s-9-5-9-12a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
                {{ $suggested->venue->name }}
            </span>
            @endif
        </div>
        @php $price = $suggested->priceForMember($member); $discount = $suggested->discountForLayer($layer); @endphp
        <div style="height:1px;background:var(--border);margin:1rem 0;"></div>
        <div style="display:flex;align-items:center;justify-content:space-between;">
            <div>
                @if($discount > 0)
                    <span style="font-size:0.72rem;color:var(--ink-faint);text-decoration:line-through;">{{ number_format($suggested->base_price) }}</span>
                @endif
                <div style="font-size:1.3rem;font-weight:800;color:var(--ink);">{{ number_format($price) }} <span style="font-size:0.7rem;font-weight:400;color:var(--ink-dim);">تومان</span></div>
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
