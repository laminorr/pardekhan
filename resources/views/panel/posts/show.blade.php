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

{{-- کاور --}}
@if($post->cover_src)
    <div style="border-radius:20px;overflow:hidden;box-shadow:0 14px 36px -16px rgba(0,0,0,0.35);margin-bottom:1.3rem;">
        <img src="{{ $post->cover_src }}" alt="{{ $post->title }}" style="width:100%;display:block;">
    </div>
@endif

{{-- عنوان --}}
<h1 style="font-size:1.5rem;font-weight:800;letter-spacing:-0.5px;line-height:1.4;">{{ $post->title }}</h1>

{{-- متادیتا --}}
<div style="display:flex;align-items:center;gap:0.8rem;margin-top:0.7rem;font-size:0.75rem;color:var(--ink-faint);">
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
<div style="font-size:0.95rem;color:var(--ink-mid);line-height:2.1;margin-top:1.5rem;text-align:justify;">
    {!! nl2br(e($post->body)) !!}
</div>

<div style="height:1.5rem;"></div>
@endsection

@section('nav')
    @include('panel.partials.bottom-nav', ['active' => 'home'])
@endsection
