@extends('panel.layouts.app')
@section('title', $event->title)

@push('styles')
<style>
    /* هدر جمع‌شونده هنگام اسکرول — تصویر از ۲۶۰ به ~۱۹۵ کوچک می‌شود و بالا می‌چسبد */
    .ev-hero { position:fixed; top:0; left:50%; transform:translateX(-50%); width:100%; max-width:430px; height:260px; z-index:40; overflow:hidden; background:linear-gradient(135deg,#dfe7e3,#cfdbd5); display:flex; align-items:center; justify-content:center; will-change:height; }
    .ev-hero-img { position:absolute; inset:0; width:100%; height:100%; object-fit:cover; transform:scale(1.04); will-change:filter; }
    .ev-hero-shade { position:absolute; inset:0; background:#0d1f1a; opacity:0; pointer-events:none; }
    .ev-hero-curve { position:absolute; left:0; right:0; bottom:0; height:26px; background:var(--bg); border-radius:28px 28px 0 0; pointer-events:none; z-index:1; }
    .ev-hero-empty { font-size:0.85rem; color:#7e948b; letter-spacing:1px; }
    .ev-hero-spacer { height:260px; margin-top:-1.4rem; }
    .ev-hero-actions { position:absolute; top:1rem; right:1.2rem; left:1.2rem; display:flex; justify-content:space-between; z-index:2; }
    .ev-hero-btn { width:44px; height:44px; border-radius:14px; background:rgba(255,255,255,0.92); border:none; display:flex; align-items:center; justify-content:center; backdrop-filter:blur(6px); cursor:pointer; text-decoration:none; }
    .ev-body { position:relative; z-index:1; margin:0 -1.2rem 0; background:var(--bg); border-radius:28px 28px 0 0; padding:1.5rem 1.2rem 0; }
    .ev-desc { font-size:0.86rem; color:var(--ink-dim); margin-top:0.6rem; line-height:1.95; text-align:justify; }
    .ev-desc strong, .ev-desc b { font-weight:800; color:var(--ink); }
    .ev-info-row { display:flex; align-items:center; gap:0.85rem; }
    .ev-info-ico { width:44px; height:44px; border-radius:14px; background:var(--bg-soft); display:flex; align-items:center; justify-content:center; flex-shrink:0; }
    .ev-map { margin-top:1.3rem; height:120px; border-radius:18px; background:var(--green-line); position:relative; overflow:hidden; border:1px solid var(--border-2); }
    .ev-map-grid { position:absolute; inset:0; background-image:linear-gradient(#dde2df 1px,transparent 1px),linear-gradient(90deg,#dde2df 1px,transparent 1px); background-size:26px 26px; }
    .ev-paybar { position:fixed; bottom:0; left:50%; transform:translateX(-50%); width:100%; max-width:430px; background:rgba(252,252,251,0.96); backdrop-filter:blur(10px); border-top:1px solid var(--border); padding:1rem 1.2rem calc(1rem + env(safe-area-inset-bottom)); display:flex; align-items:center; justify-content:space-between; gap:1rem; z-index:60; }

    /* پخش‌کنندهٔ شرح صوتی (بدون کاور) — هم‌سبک با پلیر پادکست */
    .voice-box { margin-top:1.1rem; background:var(--green-tint); border:1px solid var(--green-soft); border-radius:16px; padding:0.85rem 0.9rem; transition:background 0.3s, border-color 0.3s; }
    .voice-box.playing { background:#e3efe9; border-color:var(--pine); box-shadow:0 8px 26px -10px rgba(47,93,80,0.28); }
    .voice-head { display:flex; align-items:center; gap:0.6rem; }
    .voice-ico { width:38px; height:38px; border-radius:12px; background:var(--green-soft); display:flex; align-items:center; justify-content:center; flex-shrink:0; transition:box-shadow 0.3s; }
    .voice-box.playing .voice-ico { box-shadow:0 0 0 3px rgba(47,93,80,0.18); }
    .voice-meta { flex:1; min-width:0; }
    .voice-label { font-size:0.66rem; font-weight:800; color:var(--pine); letter-spacing:0.2px; }
    .voice-title { font-size:0.82rem; font-weight:700; color:var(--ink); margin-top:2px; line-height:1.5; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }
    .voice-nowplaying { display:none; align-items:center; gap:5px; margin-top:4px; }
    .voice-box.playing .voice-nowplaying { display:inline-flex; }
    .voice-eq { display:inline-flex; align-items:flex-end; gap:2px; height:11px; }
    .voice-eq span { width:2.5px; background:var(--pine); border-radius:2px; animation:voiceEq 0.9s ease-in-out infinite; }
    .voice-eq span:nth-child(1){ height:40%; animation-delay:0s; }
    .voice-eq span:nth-child(2){ height:100%; animation-delay:0.2s; }
    .voice-eq span:nth-child(3){ height:60%; animation-delay:0.4s; }
    .voice-eq span:nth-child(4){ height:85%; animation-delay:0.1s; }
    @keyframes voiceEq { 0%,100%{ transform:scaleY(0.4); } 50%{ transform:scaleY(1); } }
    .voice-player { margin-top:0.7rem; display:flex; align-items:center; gap:0.65rem; }
    .voice-play-btn { width:38px; height:38px; border-radius:50%; background:var(--pine); border:none; display:flex; align-items:center; justify-content:center; cursor:pointer; flex-shrink:0; transition:transform 0.15s; box-shadow:0 4px 12px rgba(47,93,80,0.3); }
    .voice-play-btn:active { transform:scale(0.92); }
    .voice-player-mid { flex:1; min-width:0; }
    .voice-progress-wrap { height:5px; background:rgba(47,93,80,0.15); border-radius:99px; cursor:pointer; overflow:hidden; }
    .voice-progress-bar { height:100%; width:0%; background:var(--pine); border-radius:99px; transition:width 0.1s linear; }
    .voice-time { display:flex; justify-content:space-between; font-size:0.6rem; color:var(--pine-deep); margin-top:4px; font-variant-numeric:tabular-nums; }
</style>
@endpush

@section('content')
{{-- تصویر بزرگ (جمع‌شونده هنگام اسکرول) --}}
<div class="ev-hero" id="evHero">
    @if($event->image)
        <img class="ev-hero-img" src="{{ Storage::url($event->image) }}" alt="{{ $event->title }}">
        <div class="ev-hero-shade"></div>
    @else
        <span class="ev-hero-empty">تصویر دورهمی</span>
    @endif
    <div class="ev-hero-curve"></div>
    <div class="ev-hero-actions">
        <a href="{{ route('panel.events.index') }}" class="ev-hero-btn">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#16181a" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 6l6 6-6 6"/></svg>
        </a>
        <div class="ev-hero-btn" style="cursor:default;">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="var(--pine)" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><path d="M12 20s-7-4.5-7-9.5A4 4 0 0 1 12 7a4 4 0 0 1 7 3.5C19 15.5 12 20 12 20z"/></svg>
        </div>
    </div>
</div>
{{-- جای‌گیر ثابت: چون هدر fixed است، این فاصله را در چیدمان نگه می‌دارد تا بدنه بدون پرش اسکرول شود --}}
<div class="ev-hero-spacer" aria-hidden="true"></div>

{{-- محتوا --}}
<div class="ev-body">
    @if($price === 0)
        <span style="display:inline-block;background:var(--green-soft);color:var(--pine);font-size:0.7rem;font-weight:700;padding:6px 13px;border-radius:20px;">ویژهٔ لایهٔ شما · رایگان</span>
    @elseif($price < $event->base_price)
        @if($discount > 0)
            <span style="display:inline-block;background:var(--green-soft);color:var(--pine);font-size:0.7rem;font-weight:700;padding:6px 13px;border-radius:20px;">ویژهٔ لایهٔ شما · {{ fa($discount) }}٪ تخفیف</span>
        @else
            <span style="display:inline-block;background:var(--green-soft);color:var(--pine);font-size:0.7rem;font-weight:700;padding:6px 13px;border-radius:20px;">قیمت ویژهٔ لایهٔ شما</span>
        @endif
    @endif

    <div style="font-size:1.6rem;font-weight:800;line-height:1.25;margin-top:0.8rem;letter-spacing:-0.5px;">{{ $event->title }}</div>
    @if($event->subtitle)
        <div style="font-size:0.92rem;color:var(--pine);margin-top:4px;font-weight:600;">{{ $event->subtitle }}</div>
    @endif
    @if($event->description)
        <div class="ev-desc">{!! $event->description !!}</div>
    @endif

    {{-- شرح صوتی (قسمت پادکست باهم کتاب) — بدون کاور --}}
    @if($event->hasVoice())
    <div class="voice-box">
        <div class="voice-head">
            <div class="voice-ico">
                <svg width="19" height="19" viewBox="0 0 24 24" fill="none" stroke="var(--pine)" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2a3 3 0 0 0-3 3v7a3 3 0 0 0 6 0V5a3 3 0 0 0-3-3z"/><path d="M19 10v2a7 7 0 0 1-14 0v-2M12 19v3"/></svg>
            </div>
            <div class="voice-meta">
                <div class="voice-label">شرح صوتی این دورهمی</div>
                <div class="voice-title">{{ $event->voice_title ?: 'قسمتی از پادکست باهم کتاب' }}</div>
                <div class="voice-nowplaying">
                    <span class="voice-eq"><span></span><span></span><span></span><span></span></span>
                    <span style="font-size:0.62rem;font-weight:800;color:var(--pine);">در حال پخش</span>
                </div>
            </div>
        </div>
        <div class="voice-player" data-src="{{ $event->voice_url }}">
            <button type="button" class="voice-play-btn" aria-label="پخش">
                <svg class="ic-play" width="17" height="17" viewBox="0 0 24 24" fill="#fff"><path d="M8 5v14l11-7z"/></svg>
                <svg class="ic-pause" width="17" height="17" viewBox="0 0 24 24" fill="#fff" style="display:none;"><path d="M6 4h4v16H6zM14 4h4v16h-4z"/></svg>
            </button>
            <div class="voice-player-mid">
                <div class="voice-progress-wrap"><div class="voice-progress-bar"></div></div>
                <div class="voice-time"><span class="t-cur">۰:۰۰</span><span class="t-dur">--:--</span></div>
            </div>
        </div>
    </div>
    @endif

    {{-- اطلاعات --}}
    <div style="margin-top:1.3rem;display:flex;flex-direction:column;gap:0.9rem;">
        <div class="ev-info-row">
            <div class="ev-info-ico">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="var(--pine)" stroke-width="1.6"><rect x="4" y="6" width="16" height="15" rx="2.5"/><path d="M4 10h16M8 3v4M16 3v4"/></svg>
            </div>
            <div>
                <div style="font-size:0.9rem;font-weight:700;">{{ fa(\Morilog\Jalali\Jalalian::fromDateTime($event->starts_at)->format('l j F Y')) }}</div>
                <div style="font-size:0.78rem;color:var(--ink-dim);margin-top:2px;">ساعت {{ fa($event->starts_at->format('H:i')) }}@if($event->ends_at) تا {{ fa($event->ends_at->format('H:i')) }}@endif</div>
            </div>
        </div>
        @if($event->venue)
        <div class="ev-info-row">
            <div class="ev-info-ico">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="var(--pine)" stroke-width="1.6"><path d="M12 21s7-5.5 7-11a7 7 0 1 0-14 0c0 5.5 7 11 7 11z"/><circle cx="12" cy="10" r="2.4"/></svg>
            </div>
            <div>
                <div style="font-size:0.9rem;font-weight:700;">{{ $event->venue->name }}</div>
                @if($event->venue->address)
                    <div style="font-size:0.78rem;color:var(--ink-dim);margin-top:2px;text-align:justify;">{{ $event->venue->address }}</div>
                @endif
            </div>
        </div>
        @endif
        <div class="ev-info-row">
            <div class="ev-info-ico">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="var(--pine)" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75"/></svg>
            </div>
            <div>
                <div style="font-size:0.9rem;font-weight:700;">{{ fa($event->capacity) }} نفر ظرفیت</div>
                <div style="font-size:0.78rem;color:var(--ink-dim);margin-top:2px;">{{ fa($event->remainingCapacity()) }} جای باقی‌مانده</div>
            </div>
        </div>
    </div>

    {{-- نقشه --}}
    @if($event->venue)
    <div class="ev-map">
        <div class="ev-map-grid"></div>
        <div style="position:absolute;top:50%;left:50%;transform:translate(-50%,-50%);display:flex;flex-direction:column;align-items:center;gap:6px;">
            <svg width="26" height="26" viewBox="0 0 24 24" fill="var(--pine)"><path d="M12 2a7 7 0 0 0-7 7c0 5 7 13 7 13s7-8 7-13a7 7 0 0 0-7-7zm0 9.5A2.5 2.5 0 1 1 12 6a2.5 2.5 0 0 1 0 5.5z"/></svg>
            @if($event->venue->map_link)
                <a href="{{ $event->venue->map_link }}" target="_blank" style="font-size:0.7rem;color:var(--pine);font-weight:600;text-decoration:none;">نمایش روی نقشه</a>
            @else
                <span style="font-size:0.7rem;color:var(--ink-mid);font-weight:600;">{{ $event->venue->name }}</span>
            @endif
        </div>
    </div>
    @endif

    {{-- شرکت‌کنندگان --}}
    @if($attendeeAvatars->isNotEmpty())
    <div style="margin-top:1.3rem;display:flex;align-items:center;justify-content:space-between;">
        <div style="display:flex;align-items:center;">
            @foreach($attendeeAvatars->take(4) as $i => $att)
                <div style="width:34px;height:34px;border-radius:50%;border:2.5px solid var(--bg);overflow:hidden;margin-right:{{ $i > 0 ? '-10px' : '0' }};">
                    <img src="{{ Storage::url($att->avatar) }}" style="width:100%;height:100%;object-fit:cover;">
                </div>
            @endforeach
            @if($attendeeAvatars->count() > 4)
                <div style="width:34px;height:34px;border-radius:50%;background:var(--pine);border:2.5px solid var(--bg);margin-right:-10px;display:flex;align-items:center;justify-content:center;font-size:0.66rem;color:#fff;font-weight:700;">{{ fa($attendeeAvatars->count() - 4) }}+</div>
            @endif
        </div>
        <span style="font-size:0.76rem;color:var(--ink-dim);">{{ fa($attendeeAvatars->count()) }} نفر ثبت‌نام کرده‌اند</span>
    </div>
    @endif

    {{-- فاصله برای نوار پایین --}}
    <div style="height:120px;"></div>
</div>

{{-- نوار قیمت ثابت پایین --}}
<div class="ev-paybar">
    <div>
        @if($price < $event->base_price)
            <div style="font-size:0.68rem;color:var(--ink-faint);text-decoration:line-through;">{{ fa(number_format($event->base_price)) }}</div>
        @endif
        <div>
            @if($price === 0)
                <span style="font-size:1.2rem;font-weight:800;letter-spacing:-0.3px;color:var(--pine);">برای شما رایگان شد</span>
            @else
                <span style="font-size:1.4rem;font-weight:800;letter-spacing:-0.5px;color:var(--pine);">{{ fa(number_format($price)) }}</span>
                <span style="font-size:0.7rem;color:var(--ink-dim);margin-right:4px;">تومان</span>
            @endif
        </div>
    </div>

    @if($isRegistered)
        <div style="display:flex;align-items:center;gap:0.6rem;">
            <form method="POST" action="{{ route('panel.events.cancel', $event) }}" onsubmit="return confirm('آیا از انصراف مطمئن هستید؟');" style="display:flex;">
                @csrf
                <button type="submit" title="انصراف" style="width:48px;height:48px;border-radius:14px;background:#fbeae4;border:1px solid #f0cdbe;display:flex;align-items:center;justify-content:center;cursor:pointer;">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="var(--burnt)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6L6 18M6 6l12 12"/></svg>
                </button>
            </form>
            <a href="{{ route('panel.tickets.index') }}" class="btn btn-primary" style="width:auto;padding:0.85rem 1.6rem;">مشاهدهٔ بلیت</a>
        </div>
    @elseif($isWaiting)
        <span style="font-size:0.82rem;color:var(--ink-mid);font-weight:600;background:var(--bg-soft);padding:0.85rem 1.4rem;border-radius:14px;">در لیست انتظار</span>
    @elseif(! $event->isRegistrationOpen() && $event->remainingCapacity() > 0)
        {{-- ثبت‌نام طبق برنامه بسته شده (زمان گذشته یا رویداد غیرفعال) --}}
        <span style="display:flex;align-items:center;gap:7px;font-size:0.82rem;color:var(--ink-mid);font-weight:700;background:var(--bg-soft);padding:0.85rem 1.4rem;border-radius:14px;">
            <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="var(--ink-dim)" stroke-width="1.8"><rect x="5" y="11" width="14" height="10" rx="2"/><path d="M8 11V7a4 4 0 0 1 8 0v4"/></svg>
            ثبت‌نام بسته شد
        </span>
    @elseif($event->remainingCapacity() <= 0)
        <form method="POST" action="{{ route('panel.events.waitlist', $event) }}">
            @csrf
            <button type="submit" class="btn btn-primary" style="width:auto;padding:0.85rem 1.6rem;">لیست انتظار</button>
        </form>
    @else
        <a href="{{ route('panel.payment.checkout', $event) }}" class="btn btn-primary" style="width:auto;padding:0.85rem 1.9rem;">
            ثبت‌نام
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 12H5M12 5l-7 7 7 7"/></svg>
        </a>
    @endif
</div>

@endsection

@push('scripts')
<script>
/* جمع‌شدن نرم هدر هنگام اسکرول: ۲۶۰ → ~۱۹۵px + بلور ملایم، سپس چسبیدن به بالا */
(function () {
    var hero = document.getElementById('evHero');
    if (!hero) return;

    var img = hero.querySelector('.ev-hero-img');
    var shade = hero.querySelector('.ev-hero-shade');
    var MAX = 260, MIN = 195, RANGE = 120, DELTA = MAX - MIN; // بازهٔ اسکرولِ کوچک‌شدن
    var ticking = false;

    function update() {
        ticking = false;
        var y = window.pageYOffset || window.scrollY || 0;
        var p = y / RANGE;
        if (p < 0) p = 0; else if (p > 1) p = 1; // بین ۰ و ۱ محدود می‌شود
        hero.style.height = (MAX - DELTA * p) + 'px';
        if (img) img.style.filter = p > 0 ? 'blur(' + (2 * p).toFixed(2) + 'px)' : '';
        if (shade) shade.style.opacity = (0.16 * p).toFixed(3);
    }

    function onScroll() {
        if (!ticking) { ticking = true; requestAnimationFrame(update); }
    }

    window.addEventListener('scroll', onScroll, { passive: true });
    window.addEventListener('resize', onScroll, { passive: true });
    update();
})();

(function () {
    var player = document.querySelector('.voice-player');
    if (!player) return;

    var faD = ['۰','۱','۲','۳','۴','۵','۶','۷','۸','۹'];
    function fa(s){ return String(s).replace(/\d/g,function(d){return faD[d];}); }
    function fmt(sec){ if(isNaN(sec)||!isFinite(sec))return '--:--'; var m=Math.floor(sec/60), s=Math.floor(sec%60); return fa(m+':'+(s<10?'0'+s:s)); }

    var box = player.closest('.voice-box');
    var btn = player.querySelector('.voice-play-btn');
    var icPlay = player.querySelector('.ic-play');
    var icPause = player.querySelector('.ic-pause');
    var bar = player.querySelector('.voice-progress-bar');
    var wrap = player.querySelector('.voice-progress-wrap');
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
            icPlay.style.display = ''; icPause.style.display = 'none';
            bar.style.width = '0%'; tCur.textContent = fmt(0);
            box.classList.remove('playing');
        });
        return audio;
    }

    btn.addEventListener('click', function () {
        var a = ensureAudio();
        if (a.paused) {
            a.play();
            icPlay.style.display = 'none'; icPause.style.display = '';
            box.classList.add('playing');
        } else {
            a.pause();
            icPlay.style.display = ''; icPause.style.display = 'none';
            box.classList.remove('playing');
        }
    });

    wrap.addEventListener('click', function (e) {
        var a = ensureAudio();
        var rect = wrap.getBoundingClientRect();
        // RTL: از سمت راست حساب می‌کنیم
        var ratio = (rect.right - e.clientX) / rect.width;
        if (a.duration) a.currentTime = ratio * a.duration;
    });
})();
</script>
@endpush
