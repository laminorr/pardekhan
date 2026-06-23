@extends('panel.layouts.app')
@section('title', 'پادکست عدم قطعیت')

@push('styles')
<style>
    .pod-hero { position:relative; overflow:hidden; border-radius:24px; background:linear-gradient(150deg,var(--pine),#1f4538); padding:1.5rem 1.4rem; margin-bottom:1.5rem; box-shadow:0 18px 40px -20px rgba(47,93,80,0.7); }
    .pod-hero .deco { position:absolute; border-radius:50%; background:rgba(255,255,255,0.06); }
    .pod-cover { width:88px; height:88px; border-radius:20px; object-fit:cover; box-shadow:0 10px 24px -10px rgba(0,0,0,0.5); flex-shrink:0; background:rgba(255,255,255,0.1); }

    .ep-card { background:#fff; border:1px solid var(--border); border-radius:20px; padding:1.15rem 1.2rem; box-shadow:0 4px 20px rgba(40,60,50,0.05); transition:transform 0.25s, box-shadow 0.25s; }
    .ep-card:hover { transform:translateY(-2px); box-shadow:0 10px 28px rgba(40,60,50,0.1); }
    .ep-num { width:44px; height:44px; border-radius:14px; background:linear-gradient(135deg,var(--pine),var(--pine-bright)); display:flex; align-items:center; justify-content:center; color:#fff; font-weight:800; font-size:1rem; flex-shrink:0; }

    /* پلیر سفارشی */
    .player { margin-top:0.9rem; background:var(--green-tint); border-radius:14px; padding:0.7rem 0.85rem; display:flex; align-items:center; gap:0.7rem; }
    .play-btn { width:42px; height:42px; border-radius:50%; background:var(--pine); border:none; display:flex; align-items:center; justify-content:center; cursor:pointer; flex-shrink:0; transition:transform 0.15s; box-shadow:0 4px 12px rgba(47,93,80,0.3); }
    .play-btn:active { transform:scale(0.92); }
    .player-mid { flex:1; min-width:0; }
    .progress-wrap { height:6px; background:rgba(47,93,80,0.15); border-radius:99px; cursor:pointer; overflow:hidden; }
    .progress-bar { height:100%; width:0%; background:var(--pine); border-radius:99px; transition:width 0.1s linear; }
    .player-time { display:flex; justify-content:space-between; font-size:0.62rem; color:var(--pine-deep); margin-top:5px; font-variant-numeric:tabular-nums; }

    /* اسلاید ورود اپیزودها */
    .ep-card { opacity:0; transform:translateY(16px); animation:epIn 0.5s ease forwards; }
    @keyframes epIn { to { opacity:1; transform:translateY(0); } }
</style>
@endpush

@section('content')
{{-- هدر --}}
<div style="display:flex;align-items:center;gap:0.75rem;margin-bottom:1.3rem;">
    <a href="{{ route('panel.dashboard') }}" class="icon-btn" style="flex-shrink:0;">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 6l6 6-6 6"/></svg>
    </a>
    <div style="font-size:1.4rem;font-weight:800;letter-spacing:-0.5px;">پادکست</div>
</div>

{{-- هیرو پادکست --}}
<div class="pod-hero">
    <div class="deco" style="top:-40px;left:-30px;width:140px;height:140px;"></div>
    <div class="deco" style="bottom:-50px;right:-20px;width:120px;height:120px;"></div>
    <div style="position:relative;display:flex;align-items:center;gap:1.1rem;">
        @if($show && !empty($show['image']))
            <img src="{{ $show['image'] }}" alt="{{ $show['title'] ?? 'پادکست' }}" class="pod-cover">
        @else
            <div class="pod-cover" style="display:flex;align-items:center;justify-content:center;">
                <svg width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="rgba(255,255,255,0.8)" stroke-width="1.5"><path d="M12 2a3 3 0 0 0-3 3v7a3 3 0 0 0 6 0V5a3 3 0 0 0-3-3z"/><path d="M19 10v2a7 7 0 0 1-14 0v-2M12 19v3"/></svg>
            </div>
        @endif
        <div style="flex:1;min-width:0;color:#fff;">
            <div style="font-size:1.3rem;font-weight:800;line-height:1.2;">{{ $show['title'] ?? 'عدم قطعیت' }}</div>
            <div style="font-size:0.76rem;color:rgba(234,243,239,0.85);margin-top:5px;line-height:1.7;">
                {{ $show && !empty($show['description']) ? \Illuminate\Support\Str::limit(trim(strip_tags($show['description'])), 90) : 'روایت‌هایی درباره‌ی نادانسته‌ها' }}
            </div>
            <div style="display:inline-flex;align-items:center;gap:5px;margin-top:9px;font-size:0.7rem;font-weight:700;background:rgba(255,255,255,0.15);padding:4px 11px;border-radius:99px;backdrop-filter:blur(6px);">
                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2"><path d="M12 2a3 3 0 0 0-3 3v7a3 3 0 0 0 6 0V5a3 3 0 0 0-3-3z"/><path d="M19 10v2a7 7 0 0 1-14 0v-2"/></svg>
                {{ fa(count($episodes)) }} قسمت
            </div>
        </div>
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
        <div class="ep-card" style="animation-delay:{{ $i * 0.06 }}s;">
            <div style="display:flex;align-items:flex-start;gap:0.85rem;">
                <div class="ep-num">{{ fa(count($episodes) - $i) }}</div>
                <div style="flex:1;min-width:0;">
                    <div style="font-size:0.98rem;font-weight:800;line-height:1.45;">{{ $ep['title'] }}</div>
                    <div style="display:flex;align-items:center;gap:0.6rem;margin-top:4px;font-size:0.7rem;color:var(--ink-faint);">
                        @if($ep['pubDate'])<span>{{ fa(\Carbon\Carbon::parse($ep['pubDate'])->format('Y/m/d')) }}</span>@endif
                        @if($ep['duration'])<span>· {{ \App\Services\PodcastService::humanDuration($ep['duration']) }}</span>@endif
                    </div>
                </div>
            </div>

            @if($ep['description'])
                <div style="font-size:0.82rem;color:var(--ink-mid);line-height:1.9;margin-top:0.7rem;text-align:justify;">
                    {{ \Illuminate\Support\Str::limit(trim(strip_tags($ep['description'])), 200) }}
                </div>
            @endif

            @if($ep['audio'])
            {{-- پلیر سفارشی --}}
            <div class="player" data-src="{{ $ep['audio'] }}">
                <button type="button" class="play-btn" aria-label="پخش">
                    <svg class="ic-play" width="18" height="18" viewBox="0 0 24 24" fill="#fff"><path d="M8 5v14l11-7z"/></svg>
                    <svg class="ic-pause" width="18" height="18" viewBox="0 0 24 24" fill="#fff" style="display:none;"><path d="M6 4h4v16H6zM14 4h4v16h-4z"/></svg>
                </button>
                <div class="player-mid">
                    <div class="progress-wrap"><div class="progress-bar"></div></div>
                    <div class="player-time"><span class="t-cur">۰:۰۰</span><span class="t-dur">--:--</span></div>
                </div>
            </div>
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

@push('scripts')
<script>
(function () {
    var faD = ['۰','۱','۲','۳','۴','۵','۶','۷','۸','۹'];
    function fa(s){ return String(s).replace(/\d/g,function(d){return faD[d];}); }
    function fmt(sec){ if(isNaN(sec)||!isFinite(sec))return '--:--'; var m=Math.floor(sec/60), s=Math.floor(sec%60); return fa(m+':'+(s<10?'0'+s:s)); }

    var current = null; // audio در حال پخش

    document.querySelectorAll('.player').forEach(function (player) {
        var btn = player.querySelector('.play-btn');
        var icPlay = player.querySelector('.ic-play');
        var icPause = player.querySelector('.ic-pause');
        var bar = player.querySelector('.progress-bar');
        var wrap = player.querySelector('.progress-wrap');
        var tCur = player.querySelector('.t-cur');
        var tDur = player.querySelector('.t-dur');
        var audio = null;

        function ensureAudio() {
            if (audio) return audio;
            audio = new Audio(player.dataset.src);
            audio.preload = 'metadata';
            audio.addEventListener('loadedmetadata', function () { tDur.textContent = fmt(audio.duration); });
            audio.addEventListener('timeupdate', function () {
                var p = (audio.currentTime / audio.duration) * 100;
                bar.style.width = (p || 0) + '%';
                tCur.textContent = fmt(audio.currentTime);
            });
            audio.addEventListener('ended', function () {
                icPlay.style.display = ''; icPause.style.display = 'none'; bar.style.width = '0%'; tCur.textContent = fmt(0);
            });
            return audio;
        }

        btn.addEventListener('click', function () {
            var a = ensureAudio();
            if (a.paused) {
                // توقف بقیه پلیرها
                if (current && current !== a) {
                    current.pause();
                    document.querySelectorAll('.play-btn').forEach(function (b) {
                        b.querySelector('.ic-play').style.display = '';
                        b.querySelector('.ic-pause').style.display = 'none';
                    });
                }
                a.play();
                current = a;
                icPlay.style.display = 'none'; icPause.style.display = '';
            } else {
                a.pause();
                icPlay.style.display = ''; icPause.style.display = 'none';
            }
        });

        wrap.addEventListener('click', function (e) {
            var a = ensureAudio();
            var rect = wrap.getBoundingClientRect();
            // چون RTL است، از راست حساب می‌کنیم
            var ratio = (rect.right - e.clientX) / rect.width;
            if (a.duration) a.currentTime = ratio * a.duration;
        });
    });
})();
</script>
@endpush
