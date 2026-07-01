@extends('panel.layouts.app')
@section('title', $post->title)

@section('content')
{{-- هدر --}}
<div style="display:flex;align-items:center;gap:0.75rem;margin-bottom:1.2rem;">
    <a href="{{ route('panel.posts.index') }}" class="icon-btn" style="flex-shrink:0;">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 6l6 6-6 6"/></svg>
    </a>
    <div style="font-size:1.05rem;font-weight:800;">مجله</div>
</div>

{{-- کارت محتوا --}}
<article style="background:#fff;border:1px solid var(--border);border-radius:24px;padding:1.6rem 1.4rem;box-shadow:0 6px 26px rgba(40,60,50,0.06);">
    {{-- عنوان --}}
    <h1 style="font-size:1.45rem;font-weight:800;letter-spacing:-0.5px;line-height:1.5;color:var(--ink);">{{ $post->title }}</h1>

    {{-- متادیتا --}}
    <div style="display:flex;align-items:center;gap:0.9rem;margin-top:0.8rem;padding-bottom:1.2rem;border-bottom:1px solid var(--bg-mute);font-size:0.74rem;color:var(--ink-faint);">
        <span style="display:flex;align-items:center;gap:5px;">
            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><rect x="3" y="4" width="18" height="18" rx="2"/><path d="M16 2v4M8 2v4M3 10h18"/></svg>
            {{ pdate($post->published_at ?? $post->created_at, 'l j F Y') }}
        </span>
        <span style="display:flex;align-items:center;gap:5px;">
            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7z"/><circle cx="12" cy="12" r="3"/></svg>
            {{ fa($post->views) }} بازدید
        </span>
    </div>

    {{-- متن --}}
    <div style="font-size:0.95rem;color:var(--ink-mid);line-height:2.15;margin-top:1.3rem;text-align:justify;">
        {!! nl2br(e($post->body)) !!}
    </div>
</article>

<div style="height:1.5rem;"></div>
@endsection

@section('nav')
    @include('panel.partials.bottom-nav', ['active' => 'home'])
@endsection
