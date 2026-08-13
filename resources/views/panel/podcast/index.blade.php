@extends('panel.layouts.app')
@section('title', 'پادکست‌ها')

@push('styles')
<style>
    .pod-hero { position:relative; overflow:hidden; border-radius:24px; background:linear-gradient(150deg,var(--pine),#1f4538); padding:1.5rem 1.4rem; box-shadow:0 18px 40px -20px rgba(47,93,80,0.7); text-decoration:none; display:block; transition:transform 0.25s, box-shadow 0.25s; }
    .pod-hero:hover { transform:translateY(-3px); box-shadow:0 22px 46px -20px rgba(47,93,80,0.8); }
    .pod-hero:active { transform:translateY(-1px); }
    .pod-hero .deco { position:absolute; border-radius:50%; background:rgba(255,255,255,0.06); }
    .pod-cover { width:88px; height:88px; border-radius:20px; object-fit:cover; box-shadow:0 10px 24px -10px rgba(0,0,0,0.5); flex-shrink:0; background:rgba(255,255,255,0.1); }

    /* ورود کارت‌ها */
    .pod-hero { opacity:0; transform:translateY(16px); animation:podIn 0.5s ease forwards; }
    @keyframes podIn { to { opacity:1; transform:translateY(0); } }
</style>
@endpush

@section('content')
{{-- هدر --}}
<div style="display:flex;align-items:flex-start;gap:0.75rem;margin-bottom:1.3rem;">
    <a href="{{ route('panel.dashboard') }}" class="icon-btn" style="flex-shrink:0;margin-top:2px;">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 6l6 6-6 6"/></svg>
    </a>
    <div style="font-size:1.4rem;font-weight:800;letter-spacing:-0.5px;">پادکست</div>
</div>

{{-- بنرهای پادکست --}}
<div style="display:flex;flex-direction:column;gap:1.1rem;">
    @foreach($podcasts as $i => $pod)
    @php $show = $pod['show']; @endphp
    <a href="{{ route('panel.podcast.show', $pod['slug']) }}" class="pod-hero" style="animation-delay:{{ $i * 0.08 }}s;">
        <div class="deco" style="top:-40px;left:-30px;width:140px;height:140px;"></div>
        <div class="deco" style="bottom:-50px;right:-20px;width:120px;height:120px;"></div>
        <div style="position:relative;display:flex;align-items:center;gap:1.1rem;">
            @if($show && !empty($show['image']))
                <img src="{{ $show['image'] }}" alt="{{ $show['title'] ?? $pod['default_title'] }}" class="pod-cover">
            @else
                <div class="pod-cover" style="display:flex;align-items:center;justify-content:center;">
                    <svg width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="rgba(255,255,255,0.8)" stroke-width="1.5"><path d="M12 2a3 3 0 0 0-3 3v7a3 3 0 0 0 6 0V5a3 3 0 0 0-3-3z"/><path d="M19 10v2a7 7 0 0 1-14 0v-2M12 19v3"/></svg>
                </div>
            @endif
            <div style="flex:1;min-width:0;color:#fff;">
                <div style="font-size:1.3rem;font-weight:800;line-height:1.2;">{{ $show['title'] ?? $pod['default_title'] }}</div>
                <div style="font-size:0.76rem;color:rgba(234,243,239,0.85);margin-top:5px;line-height:1.7;">
                    {{ $show && !empty($show['description']) ? \Illuminate\Support\Str::limit(trim(strip_tags($show['description'])), 90) : 'روایت‌هایی درباره‌ی نادانسته‌ها' }}
                </div>
                <div style="display:inline-flex;align-items:center;gap:5px;margin-top:9px;font-size:0.7rem;font-weight:700;background:rgba(255,255,255,0.15);padding:4px 11px;border-radius:99px;backdrop-filter:blur(6px);">
                    @if($pod['count'] > 0)
                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2"><path d="M12 2a3 3 0 0 0-3 3v7a3 3 0 0 0 6 0V5a3 3 0 0 0-3-3z"/><path d="M19 10v2a7 7 0 0 1-14 0v-2"/></svg>
                        {{ fa($pod['count']) }} قسمت
                    @else
                        به‌زودی
                    @endif
                </div>
            </div>
            {{-- فلش ورود --}}
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="rgba(255,255,255,0.7)" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" style="flex-shrink:0;"><path d="M15 6l-6 6 6 6"/></svg>
        </div>
    </a>
    @endforeach
</div>

<div style="height:1rem;"></div>
@endsection

@section('nav')
    @include('panel.partials.bottom-nav', ['active' => 'home'])
@endsection
