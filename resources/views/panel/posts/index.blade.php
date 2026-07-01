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
    <div style="display:flex;flex-direction:column;">
        @foreach($posts as $post)
        <a href="{{ route('panel.posts.show', $post) }}" style="display:flex;gap:1rem;padding:1.1rem 0;border-bottom:1px solid var(--bg-mute);text-decoration:none;color:inherit;">
            <div style="flex:1;min-width:0;">
                <div style="font-size:1.02rem;font-weight:800;line-height:1.5;">{{ $post->title }}</div>
                <div style="font-size:0.82rem;color:var(--ink-mid);line-height:1.85;margin-top:0.5rem;">{{ \Illuminate\Support\Str::limit($post->summary, 90) }}</div>
                <div style="font-size:0.72rem;color:var(--ink-faint);margin-top:0.7rem;">
                    {{ pdate($post->published_at ?? $post->created_at, 'l j F') }}
                </div>
            </div>
            @if($post->cover_src)
                <img src="{{ $post->cover_src }}" alt="" style="width:96px;height:96px;border-radius:16px;object-fit:cover;flex-shrink:0;background:var(--green-soft);">
            @endif
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
