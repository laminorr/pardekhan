@extends('panel.layouts.app')
@section('title', $film->title ?? 'فیلم هفته')

@section('content')
{{-- هدر --}}
<div style="display:flex;align-items:center;gap:0.75rem;margin-bottom:1.2rem;">
    <a href="{{ route('panel.dashboard') }}" class="icon-btn" style="flex-shrink:0;">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 6l6 6-6 6"/></svg>
    </a>
    <div>
        <div style="font-size:1.05rem;font-weight:800;">فیلم هفته</div>
        <div style="font-size:0.75rem;color:var(--ink-dim);margin-top:2px;">
            شنبه {{ pdate($week['start'], 'j F') }} تا جمعه {{ pdate($week['end'], 'j F') }}
        </div>
    </div>
</div>

@if($film)
    {{-- کاور --}}
    @if($film->cover_src)
        <div style="border-radius:20px;overflow:hidden;box-shadow:0 14px 36px -16px rgba(0,0,0,0.4);margin-bottom:1.3rem;">
            <img src="{{ $film->cover_src }}" alt="{{ $film->title }}" style="width:100%;display:block;">
        </div>
    @else
        <div style="border-radius:20px;height:200px;background:linear-gradient(135deg,var(--burnt),#a8431f);display:flex;align-items:center;justify-content:center;margin-bottom:1.3rem;">
            <svg width="56" height="56" viewBox="0 0 24 24" fill="none" stroke="rgba(255,255,255,0.7)" stroke-width="1.3"><rect x="2" y="2" width="20" height="20" rx="2.5"/><path d="M7 2v20M17 2v20M2 12h20M2 7h5M2 17h5M17 17h5M17 7h5"/></svg>
        </div>
    @endif

    {{-- عنوان فارسی --}}
    @if($film->title)
        <div style="font-size:1.5rem;font-weight:800;letter-spacing:-0.5px;line-height:1.3;">{{ $film->title }}</div>
    @endif
    {{-- عنوان انگلیسی --}}
    @if($film->original_title)
        <div style="font-size:0.85rem;color:var(--ink-dim);margin-top:3px;direction:ltr;text-align:right;">{{ $film->original_title }}</div>
    @endif

    {{-- سال --}}
    @if($film->year)
        <div style="display:flex;flex-wrap:wrap;gap:0.5rem;margin-top:1rem;">
            <span style="font-size:0.75rem;font-weight:700;color:var(--pine);background:var(--green-soft);padding:5px 12px;border-radius:99px;">{{ fa($film->year) }}</span>
        </div>
    @endif

    {{-- خلاصهٔ داستان --}}
    @if($film->description)
        <div style="font-size:0.82rem;font-weight:800;color:var(--ink);margin-top:1.5rem;margin-bottom:0.5rem;">خلاصهٔ داستان</div>
        <div style="font-size:0.9rem;color:var(--ink-mid);line-height:2;text-align:justify;">
            {!! nl2br(e($film->description)) !!}
        </div>
    @endif

    {{-- CTA: تماشا در فیلیمو --}}
    @if($film->filimo_url)
        <a href="{{ $film->filimo_url }}" target="_blank" rel="noopener" class="btn btn-primary" style="margin-top:1.5rem;">
            <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="5 3 19 12 5 21 5 3"/></svg>
            تماشا در فیلیمو
        </a>
    @endif

    {{-- لینک IMDb --}}
    @if($film->imdb_url)
        <a href="{{ $film->imdb_url }}" target="_blank" rel="noopener" class="btn btn-ghost" style="margin-top:0.7rem;">
            <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/><path d="M15 3h6v6"/><path d="M10 14 21 3"/></svg>
            صفحهٔ IMDb
        </a>
    @endif
@else
    {{-- حالتِ خالیِ دوستانه (بدون 404) --}}
    <div style="text-align:center;padding:3rem 1rem;">
        <div style="width:88px;height:88px;border-radius:24px;background:var(--green-soft);display:flex;align-items:center;justify-content:center;margin:0 auto 1.3rem;">
            <svg width="42" height="42" viewBox="0 0 24 24" fill="none" stroke="var(--pine)" stroke-width="1.4"><rect x="2" y="2" width="20" height="20" rx="2.5"/><path d="M7 2v20M17 2v20M2 12h20M2 7h5M2 17h5M17 17h5M17 7h5"/></svg>
        </div>
        <div style="font-size:1.05rem;font-weight:800;color:var(--ink);">این هفته فیلمی معرفی نشده</div>
        <div style="font-size:0.85rem;color:var(--ink-dim);margin-top:0.5rem;line-height:1.9;">
            به‌زودی فیلمِ هفتهٔ جاری این‌جا قرار می‌گیرد.
        </div>
    </div>
@endif

<div style="height:1rem;"></div>
@endsection

@section('nav')
    @include('panel.partials.bottom-nav', ['active' => 'home'])
@endsection
