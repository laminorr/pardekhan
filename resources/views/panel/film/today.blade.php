@extends('panel.layouts.app')
@section('title', $film->title)

@section('content')
{{-- هدر --}}
<div style="display:flex;align-items:center;gap:0.75rem;margin-bottom:1.2rem;">
    <a href="{{ route('panel.dashboard') }}" class="icon-btn" style="flex-shrink:0;">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 6l6 6-6 6"/></svg>
    </a>
    <div style="font-size:1.05rem;font-weight:800;">فیلم امروز</div>
</div>

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

{{-- عنوان --}}
<div style="font-size:1.5rem;font-weight:800;letter-spacing:-0.5px;line-height:1.3;">{{ $film->title }}</div>
@if($film->original_title)
    <div style="font-size:0.85rem;color:var(--ink-dim);margin-top:3px;direction:ltr;text-align:right;">{{ $film->original_title }}</div>
@endif

{{-- اطلاعات پایه --}}
@if($film->year || $film->director || $film->genre)
<div style="display:flex;flex-wrap:wrap;gap:0.5rem;margin-top:1rem;">
    @if($film->year)
        <span style="font-size:0.75rem;font-weight:700;color:var(--pine);background:var(--green-soft);padding:5px 12px;border-radius:99px;">{{ fa($film->year) }}</span>
    @endif
    @if($film->genre)
        <span style="font-size:0.75rem;font-weight:700;color:var(--ink-mid);background:var(--bg-soft);padding:5px 12px;border-radius:99px;">{{ $film->genre }}</span>
    @endif
    @if($film->director)
        <span style="font-size:0.75rem;font-weight:700;color:var(--ink-mid);background:var(--bg-soft);padding:5px 12px;border-radius:99px;">کارگردان: {{ $film->director }}</span>
    @endif
</div>
@endif

{{-- توضیح --}}
@if($film->description)
    <div style="font-size:0.9rem;color:var(--ink-mid);line-height:2;margin-top:1.3rem;text-align:justify;">
        {!! nl2br(e($film->description)) !!}
    </div>
@endif

{{-- لینک --}}
@if($film->link)
    <a href="{{ $film->link }}" target="_blank" rel="noopener" class="btn btn-primary" style="margin-top:1.5rem;">
        <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
        تماشا / اطلاعات بیشتر
    </a>
@endif

<div style="height:1rem;"></div>
@endsection

@section('nav')
    @include('panel.partials.bottom-nav', ['active' => 'home'])
@endsection
