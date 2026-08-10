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
    <div style="display:flex;align-items:center;gap:0.9rem;flex-wrap:wrap;margin-top:0.8rem;padding-bottom:1.2rem;border-bottom:1px solid var(--bg-mute);font-size:0.74rem;color:var(--ink-faint);">
        <span style="display:flex;align-items:center;gap:5px;">
            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><rect x="3" y="4" width="18" height="18" rx="2"/><path d="M16 2v4M8 2v4M3 10h18"/></svg>
            {{ pdate($post->published_at ?? $post->created_at, 'l j F Y') }}
        </span>
        @if($post->author)
        <span style="display:flex;align-items:center;gap:5px;">
            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><circle cx="12" cy="8" r="3.5"/><path d="M5.5 20a6.5 6.5 0 0 1 13 0"/></svg>
            {{ $post->author }}
        </span>
        @endif
        <span style="display:flex;align-items:center;gap:5px;">
            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7z"/><circle cx="12" cy="12" r="3"/></svg>
            {{ fa($post->views) }} بازدید
        </span>
    </div>

    {{-- متن (HTML ذخیره‌شده از ویرایشگر، پاک‌سازی‌شده برای جلوگیری از XSS) --}}
    <div class="pk-prose">
        {!! clean($post->body) !!}
    </div>
</article>

<div style="height:1.5rem;"></div>
@endsection

@section('nav')
    @include('panel.partials.bottom-nav', ['active' => 'home'])
@endsection

@push('styles')
<style>
    /* استایل متن مجله — فقط داخل .pk-prose (بدون نشت به بقیه‌ی اپ) */
    .pk-prose {
        font-size: 0.97rem;
        color: var(--ink-mid);
        line-height: 1.9;
        margin-top: 1.3rem;
        text-align: justify;
        word-break: break-word;
        overflow-wrap: anywhere;
    }
    .pk-prose > :first-child { margin-top: 0; }
    .pk-prose p { margin: 0 0 1.05rem; }
    .pk-prose h2, .pk-prose h3, .pk-prose h4 {
        color: var(--ink);
        font-weight: 800;
        line-height: 1.5;
        margin: 1.9rem 0 0.7rem;
        text-align: right;
    }
    .pk-prose h2 { font-size: 1.28rem; }
    .pk-prose h3 { font-size: 1.12rem; }
    .pk-prose h4 { font-size: 1rem; }
    .pk-prose ul, .pk-prose ol {
        margin: 0 0 1.05rem;
        padding-inline-start: 1.5rem;
    }
    .pk-prose ul { list-style: disc; }
    .pk-prose ol { list-style: decimal; }
    .pk-prose li { margin: 0.35rem 0; padding-inline-start: 0.2rem; }
    .pk-prose li::marker { color: var(--pine); }
    .pk-prose a {
        color: var(--pine);
        text-decoration: underline;
        text-underline-offset: 3px;
        font-weight: 600;
        word-break: break-word;
    }
    .pk-prose blockquote {
        margin: 1.2rem 0;
        padding: 0.85rem 1.1rem;
        border-inline-start: 3px solid var(--pine);
        background: var(--green-tint);
        border-radius: 0 12px 12px 0;
        color: var(--ink-mid);
    }
    .pk-prose blockquote p:last-child { margin-bottom: 0; }
    .pk-prose img {
        max-width: 100%;
        height: auto;
        border-radius: 14px;
        margin: 1.1rem 0;
        display: block;
    }
    .pk-prose strong, .pk-prose b { font-weight: 800; color: var(--ink); }
    .pk-prose hr { border: none; border-top: 1px solid var(--border); margin: 1.6rem 0; }
    .pk-prose code {
        background: var(--bg-mute);
        padding: 0.12em 0.4em;
        border-radius: 6px;
        font-size: 0.88em;
    }
    .pk-prose pre {
        background: var(--ink-2);
        color: #f4f4f4;
        padding: 1rem;
        border-radius: 12px;
        overflow-x: auto;
        margin: 1.1rem 0;
        direction: ltr;
        text-align: left;
    }
    .pk-prose pre code { background: none; padding: 0; }
</style>
@endpush
