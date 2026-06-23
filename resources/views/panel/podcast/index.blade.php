@extends('panel.layouts.app')
@section('title', 'پادکست عدم قطعیت')

@push('styles')
<style>
    .ep-card { background:#fff; border:1px solid var(--border); border-radius:18px; padding:1.1rem 1.2rem; box-shadow:0 2px 14px rgba(40,60,50,0.04); }
    .ep-audio { width:100%; margin-top:0.85rem; height:38px; }
    .ep-audio::-webkit-media-controls-panel { background:var(--green-tint); }
</style>
@endpush

@section('content')
{{-- هدر --}}
<div style="display:flex;align-items:center;gap:0.75rem;margin-bottom:1.3rem;">
    <a href="{{ route('panel.dashboard') }}" class="icon-btn" style="flex-shrink:0;">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 6l6 6-6 6"/></svg>
    </a>
    <div style="flex:1;">
        <div style="font-size:1.4rem;font-weight:800;letter-spacing:-0.5px;">پادکست عدم قطعیت</div>
        <div style="font-size:0.78rem;color:var(--ink-faint);">روایت‌هایی درباره‌ی نادانسته‌ها</div>
    </div>
</div>

@if(empty($episodes))
    <div style="text-align:center;padding:3rem 1rem;color:var(--ink-dim);">
        <div style="width:64px;height:64px;border-radius:20px;background:var(--green-soft);display:flex;align-items:center;justify-content:center;margin:0 auto 1rem;">
            <svg width="30" height="30" viewBox="0 0 24 24" fill="none" stroke="var(--pine)" stroke-width="1.5"><path d="M12 2a3 3 0 0 0-3 3v7a3 3 0 0 0 6 0V5a3 3 0 0 0-3-3z"/><path d="M19 10v2a7 7 0 0 1-14 0v-2M12 19v3"/></svg>
        </div>
        <div style="font-size:0.95rem;">هنوز قسمتی منتشر نشده است</div>
        <div style="font-size:0.8rem;color:var(--ink-faint);margin-top:4px;">به‌زودی برمی‌گردیم</div>
    </div>
@else
    <div style="display:flex;flex-direction:column;gap:1rem;">
        @foreach($episodes as $i => $ep)
        <div class="ep-card">
            <div style="display:flex;align-items:flex-start;gap:0.8rem;">
                <div style="width:40px;height:40px;border-radius:12px;background:linear-gradient(135deg,var(--pine),var(--pine-bright));display:flex;align-items:center;justify-content:center;flex-shrink:0;color:#fff;font-weight:800;font-size:0.9rem;">
                    {{ fa(count($episodes) - $i) }}
                </div>
                <div style="flex:1;min-width:0;">
                    <div style="font-size:0.96rem;font-weight:800;line-height:1.4;">{{ $ep['title'] }}</div>
                    <div style="display:flex;align-items:center;gap:0.6rem;margin-top:3px;font-size:0.7rem;color:var(--ink-faint);">
                        @if($ep['pubDate'])<span>{{ fa(\Carbon\Carbon::parse($ep['pubDate'])->format('Y/m/d')) }}</span>@endif
                        @if($ep['duration'])<span>· {{ $ep['duration'] }}</span>@endif
                    </div>
                </div>
            </div>

            @if($ep['description'])
                <div style="font-size:0.82rem;color:var(--ink-mid);line-height:1.85;margin-top:0.7rem;text-align:justify;">
                    {{ \Illuminate\Support\Str::limit($ep['description'], 220) }}
                </div>
            @endif

            @if($ep['audio'])
                <audio class="ep-audio" controls preload="none" src="{{ $ep['audio'] }}"></audio>
            @endif
        </div>
        @endforeach
    </div>
@endif

<div style="height:1rem;"></div>
@endsection

@section('nav')
    @include('panel.partials.bottom-nav', ['active' => 'home'])
@endsection
