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

    {{-- ─────────── رأی‌گیریِ اعضا (فاز ۳) ─────────── --}}
    <div id="wm-vote" style="margin-top:1.8rem;padding-top:1.4rem;border-top:1px solid var(--line,#e7e2d8);">
        <div style="font-size:0.95rem;font-weight:800;color:var(--ink);margin-bottom:0.9rem;">این هفته می‌بینیش؟</div>

        {{-- فرمِ واقعی برای fallbackِ بدون JS (mirror الگوی MoodController) --}}
        <form method="POST" action="{{ route('panel.film.vote') }}" id="wm-vote-form">
            @csrf
            <input type="hidden" name="decision" id="wm-decision-input" value="{{ $myDecision }}">
            <div style="display:flex;gap:0.6rem;">
                <button type="submit" name="decision" value="will_watch"
                        class="wm-vote-btn @if($myDecision === 'will_watch') is-active @endif"
                        data-decision="will_watch" aria-pressed="{{ $myDecision === 'will_watch' ? 'true' : 'false' }}">
                    می‌بینمش
                </button>
                <button type="submit" name="decision" value="will_not_watch"
                        class="wm-vote-btn @if($myDecision === 'will_not_watch') is-active @endif"
                        data-decision="will_not_watch" aria-pressed="{{ $myDecision === 'will_not_watch' ? 'true' : 'false' }}">
                    این هفته نه
                </button>
            </div>
        </form>

        {{-- نتیجهٔ جمعی — گیتِ اثبات اجتماعی --}}
        <div id="wm-result" style="margin-top:1.1rem;">
            @include('panel.film.partials.vote-result', [
                'reveal'       => $reveal,
                'myDecision'   => $myDecision,
                'total'        => $total,
                'threshold'    => $threshold,
                'willWatch'    => $willWatch,
                'willNot'      => $willNot,
                'willWatchPct' => $willWatchPct,
            ])
        </div>
    </div>

    <style>
        .wm-vote-btn{
            flex:1;padding:0.7rem 0.5rem;border-radius:14px;cursor:pointer;
            font-family:inherit;font-size:0.9rem;font-weight:800;
            border:1.5px solid var(--line,#e7e2d8);background:var(--card,#fff);
            color:var(--ink-mid,#4a4a44);transition:all .15s ease;
        }
        .wm-vote-btn:hover{border-color:var(--pine);}
        .wm-vote-btn.is-active{
            background:var(--pine);border-color:var(--pine);color:#fff;
            box-shadow:0 8px 20px -10px rgba(47,93,80,0.6);
        }
    </style>

    <script>
    (function () {
        var form  = document.getElementById('wm-vote-form');
        var input = document.getElementById('wm-decision-input');
        var result = document.getElementById('wm-result');
        if (!form) return;

        var btns = form.querySelectorAll('.wm-vote-btn');
        var token = form.querySelector('input[name="_token"]');
        var faDigits = ['۰','۱','۲','۳','۴','۵','۶','۷','۸','۹'];
        function fa(n){ return String(n).replace(/\d/g, function (d){ return faDigits[d]; }); }

        function setActive(decision) {
            btns.forEach(function (b) {
                var on = b.getAttribute('data-decision') === decision;
                b.classList.toggle('is-active', on);
                b.setAttribute('aria-pressed', on ? 'true' : 'false');
            });
            if (input) input.value = decision;
        }

        function renderResult(d) {
            if (!result) return;
            if (d.reveal) {
                var pct = fa(d.pct);
                var notPct = fa(100 - d.pct);
                result.innerHTML =
                    '<div style="display:flex;align-items:center;justify-content:space-between;font-size:0.8rem;font-weight:800;margin-bottom:0.4rem;">' +
                        '<span style="color:var(--pine);">٪' + pct + ' می‌بینند</span>' +
                        '<span style="color:var(--ink-dim,#8a8a80);">٪' + notPct + ' نمی‌بینند</span>' +
                    '</div>' +
                    '<div style="display:flex;height:12px;border-radius:99px;overflow:hidden;background:var(--line,#e7e2d8);">' +
                        '<div style="width:' + d.pct + '%;background:var(--pine);"></div>' +
                    '</div>' +
                    '<div style="font-size:0.72rem;color:var(--ink-dim,#8a8a80);margin-top:0.45rem;">' +
                        'مجموع ' + fa(d.total) + ' رأی' +
                    '</div>';
            } else {
                // رأی داده ولی هنوز به آستانه نرسیده — بدون هیچ عددی.
                result.innerHTML =
                    '<div style="font-size:0.78rem;color:var(--ink-dim,#8a8a80);line-height:1.9;">' +
                        'نتیجهٔ جمعی به‌زودی (بعد از رأی چند نفر دیگر)…' +
                    '</div>';
            }
        }

        btns.forEach(function (btn) {
            btn.addEventListener('click', function (e) {
                e.preventDefault();
                var decision = btn.getAttribute('data-decision');
                setActive(decision);

                var body = new URLSearchParams();
                body.append('decision', decision);
                body.append('_token', token ? token.value : '');

                fetch(form.action, {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                        'Content-Type': 'application/x-www-form-urlencoded'
                    },
                    body: body.toString(),
                    credentials: 'same-origin'
                }).then(function (r) {
                    if (!r.ok) throw new Error('bad status');
                    return r.json();
                }).then(function (data) {
                    if (!data || !data.ok) throw new Error('not ok');
                    setActive(data.my_decision);
                    renderResult(data);
                }).catch(function () {
                    // خطای شبکه/AJAX → ارسالِ عادیِ فرم (POST + redirect)
                    form.submit();
                });
            });
        });
    })();
    </script>
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
