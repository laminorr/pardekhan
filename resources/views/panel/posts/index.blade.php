@extends('panel.layouts.app')
@section('title', 'مجله پرده‌خوان')

@section('content')
{{-- هدر --}}
<div style="display:flex;align-items:center;gap:0.75rem;margin-bottom:1.3rem;">
    <a href="{{ route('panel.dashboard') }}" class="icon-btn" style="flex-shrink:0;">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 6l6 6-6 6"/></svg>
    </a>
    <div style="flex:1;">
        <div style="font-size:1.4rem;font-weight:800;letter-spacing:-0.5px;">مجله پرده‌خوان</div>
        <div style="font-size:0.78rem;color:var(--ink-faint);">یادداشت‌ها و خبرها</div>
    </div>
</div>

@if($posts->isEmpty())
    <div style="text-align:center;padding:3rem 1rem;color:var(--ink-dim);">
        <div style="width:64px;height:64px;border-radius:20px;background:var(--green-soft);display:flex;align-items:center;justify-content:center;margin:0 auto 1rem;">
            <svg width="30" height="30" viewBox="0 0 24 24" fill="none" stroke="var(--pine)" stroke-width="1.5"><path d="M4 4h16v16H4zM4 9h16M9 4v16"/></svg>
        </div>
        <div style="font-size:0.95rem;">هنوز مطلبی منتشر نشده است</div>
    </div>
@else
    <div style="background:#fff;border:1px solid var(--border);border-radius:20px;padding:0 1rem;box-shadow:0 3px 16px rgba(40,60,50,0.05);">
        @foreach($posts as $post)
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
                <div style="font-size:0.98rem;font-weight:800;line-height:1.4;">{{ $post->title }}</div>
                <div style="font-size:0.8rem;color:var(--ink-mid);line-height:1.65;margin-top:0.35rem;text-align:justify;">{{ \Illuminate\Support\Str::limit($post->summary, 95) }}</div>
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

    @if($posts->hasPages())
        <div style="margin-top:1.5rem;">{{ $posts->links() }}</div>
    @endif
@endif

<div style="height:1rem;"></div>
@endsection

@section('nav')
    @include('panel.partials.bottom-nav', ['active' => 'home'])
@endsection
