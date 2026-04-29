@extends('layouts.app')

@section('title', ($episode->seo_title ?? 'تحلیل فیلم «'.$episode->title_fa.'»') . ' | پیمان شیرپور — پرده‌خوان')
@section('description', $episode->seo_description ?? $episode->hero_lead)
@section('og_title', 'تحلیل فیلم «'.$episode->title_fa.'» | پرده‌خوان')
@section('og_image', $episode->og_image ? asset('storage/'.$episode->og_image) : ($episode->cover_image ? asset('storage/'.$episode->cover_image) : ''))
@section('canonical', url($episode->slug))
@section('robots', $episode->is_published ? 'index, follow' : 'noindex, nofollow')

@section('schema')
<script type="application/ld+json">
{
  "@@context": "https://schema.org",
  "@@type": "PodcastEpisode",
  "name": "اپیزود {{ $episode->episode_number }} — {{ $episode->title_fa }}",
  "description": "{{ $episode->seo_description ?? $episode->hero_lead }}",
  "url": "{{ url($episode->slug) }}",
  "datePublished": "{{ $episode->published_at?->toIso8601String() }}",
  "partOfSeries": {
    "@@type": "PodcastSeries",
    "name": "پرده‌خوان",
    "url": "{{ url('/') }}"
  },
  "author": {
    "@@type": "Person",
    "name": "پیمان شیرپور"
  }
}
</script>
@endsection

@section('nav-links')
<div class="n-links">
  <a href="#essay">مقدمه</a>
  <a href="#themes">تحلیل</a>
  <a href="#lessons">درس‌ها</a>
</div>
@endsection

@section('content')

<section class="hero">
  <div class="hero-orb a"></div>
  <div class="hero-orb b"></div>
  <div class="hero-orb c"></div>

  <div class="hero-inner">
    <div class="hero-grid">
      <div class="hero-text">
        <div class="h-badge">
          <span class="h-dot"></span>
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M12 1a3 3 0 0 0-3 3v8a3 3 0 0 0 6 0V4a3 3 0 0 0-3-3z"/>
            <path d="M19 10v2a7 7 0 0 1-14 0v-2"/>
          </svg>
          اپیزود {{ $episode->episode_number }} — تحلیل و اجرا:
          <strong style="color:var(--teal);margin-right:3px">پیمان شیرپور</strong>
        </div>

        <h1>{!! $episode->hero_title_html !!}</h1>

        <p class="h-en" style="font-family:'Playfair Display',serif;">
          {{ $episode->title_en }} — {{ $episode->director }}, {{ $episode->year }}
        </p>

        <p class="h-lead">{{ $episode->hero_lead }}</p>

        <div class="hero-actions">
          <div class="h-btns">
            <a href="#listen" class="btn btn-p">
              <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                <path d="M8 5v14l11-7z"/>
              </svg>
              پادکست را گوش بدهید
            </a>

            <a href="{{ $episode->imdb_url ?? '#' }}" target="_blank" rel="noopener" class="btn btn-imdb" style="{{ $episode->imdb_url ? '' : 'display:none' }}">
              <span class="imdb-mark">IMDb</span>
              صفحه‌ی فیلم
            </a>

            <a href="#essay" class="btn btn-s">
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <polyline points="6 9 12 15 18 9"/>
              </svg>
              شروع مطالعه
            </a>
          </div>

          <p class="h-seo-intro">
            این صفحه به تحلیل روان‌شناختی فیلم {{ $episode->title_en }} با تمرکز بر
            {{ collect($episode->meta_tags ?? [])->take(4)->implode('، ') }}
            در پادکست پرده‌خوان اختصاص دارد.
          </p>
        </div>
      </div>

      <div class="h-card rv">
        <div class="hc-title">
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <rect x="3" y="3" width="18" height="18" rx="2"/>
            <line x1="3" y1="9" x2="21" y2="9"/>
            <line x1="9" y1="21" x2="9" y2="9"/>
          </svg>
          اطلاعات اپیزود
        </div>

        <div class="hc-row">
          <span class="hc-label">
            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <circle cx="12" cy="12" r="10"/>
              <path d="M12 6v6l4 2"/>
            </svg>
            مدت
          </span>
          <span class="hc-val">{{ $episode->meta_duration }}</span>
        </div>

        <div class="hc-row">
          <span class="hc-label">
            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
              <circle cx="12" cy="12" r="3"/>
            </svg>
            رویکردها
          </span>
          <span class="hc-badge g">{{ $episode->meta_approaches_count }} رویکرد</span>
        </div>

        <div class="hc-row">
          <span class="hc-label">
            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"/>
              <path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"/>
            </svg>
            ارجاعات
          </span>
          <span class="hc-val">{{ $episode->meta_references_count }}</span>
        </div>

        <div class="hc-row">
          <span class="hc-label">
            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>
            </svg>
            نقل‌قول
          </span>
          <span class="hc-val">{{ $episode->meta_quotes_count }}</span>
        </div>

        <div class="hc-row">
          <span class="hc-label">
            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26"/>
            </svg>
            سطح
          </span>
          <span class="hc-badge r">{{ $episode->meta_level }}</span>
        </div>

        <div class="hc-tags">
          @foreach($episode->meta_tags ?? [] as $tag)
            <span class="hc-tag">{{ $tag }}</span>
          @endforeach
        </div>
      </div>
    </div>
  </div>
</section>

<section class="sec" id="essay">
  <div class="rv">
    <div class="sec-label"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 20h9"/><path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4 12.5-12.5z"/></svg> مقدمه</div>
    <h2 class="sec-title">{!! $episode->essay_title_html !!}</h2>
    <div class="divider"></div>
  </div>
  <div class="essay rv">
    @foreach($episode->essay_paragraphs ?? [] as $p)
    <p>{!! $p !!}</p>
    @endforeach
  </div>
  <div class="q-block teal rv">
    <span class="q-mark">"</span>
    <p>{{ $episode->opening_quote_text }}</p>
    <cite>— {{ $episode->opening_quote_cite }}</cite>
  </div>
  <div class="essay rv">
    @foreach($episode->essay_after_paragraphs ?? [] as $p)
    <p>{!! $p !!}</p>
    @endforeach
  </div>
</section>

<section class="sec-wide" id="themes" style="background:#f7fafa;padding-top:64px;padding-bottom:64px;">
  <div style="max-width:760px;" class="rv">
    <div class="sec-label"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M12 16v-4"/><path d="M12 8h.01"/></svg> تحلیل</div>
    <h2 class="sec-title">{{ $episode->themes->count() }} محور <span class="hi">تحلیل روان‌شناختی</span></h2>
    <p class="sec-sub">روی هر بخش بزنید تا تحلیل کامل با نقل‌قول و ارجاع نظری باز بشه.</p>
  </div>
  <div class="acc-list" style="max-width:760px;">
    @foreach($episode->themes as $i => $theme)
    <div class="acc rv {{ $i > 0 ? 'd'.$i : '' }}" data-acc>
      <div class="acc-head" onclick="toggleAcc(this)">
        <div class="acc-icon">{{ $theme->number_label }}</div>
        <div class="acc-head-text"><h3>{{ $theme->title }}</h3><span>{{ $theme->approach }}</span></div>
        <div class="acc-arrow"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="6 9 12 15 18 9"/></svg></div>
      </div>
      <div class="acc-body"><div class="acc-body-inner">
        <p>{!! $theme->paragraph !!}</p>
        <div class="acc-quote">{{ $theme->quote }}</div>
        <div class="acc-ref"><svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"/><path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"/></svg><div>{{ $theme->reference_fa }}<span class="ref-en">{{ $theme->reference_en }}</span></div></div>
        <div class="acc-simple"><div class="acc-simple-label"><svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"/><path d="M12 17h.01"/></svg> به زبان ساده</div><p>{{ $theme->simple_explanation }}</p></div>
      </div></div>
    </div>
    @endforeach
  </div>
</section>

<div class="bq rv">
  <div class="bq-bg"></div>
  <div class="bq-inner">
    <div class="bq-decor"><span></span><span></span><span></span></div>
    <span class="bq-m" style="font-family:'Playfair Display',serif;">"</span>
    <blockquote><span class="bq-highlight">{{ $episode->big_quote_highlight }}</span><br>{{ $episode->big_quote_rest }}</blockquote>
    <cite>— {{ $episode->big_quote_source }}</cite>
    <div class="bq-decor" style="margin-top:24px;margin-bottom:0"><span></span><span></span><span></span></div>
  </div>
</div>

@php $nums = ['۱','۲','۳','۴','۵','۶','۷','۸','۹','۱۰']; @endphp

<section class="sec-wide" id="lessons" style="padding-top:64px;padding-bottom:64px;">
  <div style="max-width:760px;" class="rv">
    <div class="sec-label"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg> کاربرد عملی</div>
    <h2 class="sec-title">{{ $episode->lessons->count() }} <span class="hi">کاربرد بالینی</span> از دل این تحلیل</h2>
    <p class="sec-sub">چیزهایی که از دل تحلیل این فیلم بیرون اومد — برای هرکسی که میخواد آدم‌ها رو بهتر بفهمه.</p>
  </div>
  <div class="lessons" style="max-width:760px;">
    @foreach($episode->lessons as $i => $lesson)
    <div class="lc rv {{ $i > 0 ? 'd'.$i : '' }}">
      <div class="lc-num">{{ $nums[$i] ?? ($i+1) }}</div>
      <h3>{{ $lesson->title }}</h3>
      <p>{{ $lesson->description }}</p>
      <div class="lc-ex">
        <strong><svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg> مثال مکالمه</strong>
        <p>{{ $lesson->example }}</p>
      </div>
    </div>
    @endforeach
  </div>
</section>

<section class="player-sec" id="listen">
  <div class="player-inner">
    <div style="text-align:center;margin-bottom:24px;" class="rv">
      <div class="sec-label" style="justify-content:center;"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 18V5l12-2v13"/><circle cx="6" cy="18" r="3"/><circle cx="18" cy="16" r="3"/></svg> گوش بدید</div>
      <h2 class="sec-title">تحلیل <span class="hi">کامل</span> رو بشنوید</h2>
    </div>

    <div class="player-card rv" style="{{ $episode->aparat_hash ? '' : 'display:none' }}">
      <div class="p-top">
        <div class="p-art"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 1a3 3 0 0 0-3 3v8a3 3 0 0 0 6 0V4a3 3 0 0 0-3-3z"/><path d="M19 10v2a7 7 0 0 1-14 0v-2"/><line x1="12" y1="19" x2="12" y2="23"/></svg></div>
        <div class="p-info"><h4>اپیزود {{ $episode->episode_number }} — {{ $episode->title_fa }}</h4><p>پرده‌خوان | پیمان شیرپور</p></div>
      </div>
      <div class="aparat-wrap">
        <span class="aparat-ratio"></span>
        <iframe src="https://www.aparat.com/video/video/embed/videohash/{{ $episode->aparat_hash }}/vt/frame" allowfullscreen="true" webkitallowfullscreen="true" mozallowfullscreen="true" loading="lazy"></iframe>
      </div>
    </div>

    <div class="next-ep rv" style="{{ $episode->next_episode_title ? '' : 'display:none' }}">
      <div class="next-art"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polygon points="5 3 19 12 5 21 5 3"/></svg></div>
      <div class="next-text">
        <div class="next-label">قسمت بعدی</div>
        <h5>اپیزود {{ $episode->next_episode_number }} — {{ $episode->next_episode_title }}</h5>
        <p>{{ $episode->next_episode_subtitle }}</p>
      </div>
      <div class="next-arrow"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="15 18 9 12 15 6"/></svg></div>
    </div>

    <div class="sub-card rv">
      <h4>هر هفته یه تحلیل جدید</h4>
      <p>شماره‌تون رو بذارید تا هر اپیزود جدید رو زودتر از بقیه بشنوید.</p>
      <input type="hidden" id="subToken" value="{{ csrf_token() }}">
      <div id="subSuccess" style="display:none;padding:12px;background:#f0fdf4;border-radius:10px;border:1px solid #bbf7d0;color:#166534;font-size:0.88rem;margin-bottom:12px;">شماره شما با موفقیت ثبت شد!</div>
      <div id="subError" style="display:none;padding:12px;background:#fef2f2;border-radius:10px;border:1px solid #fecaca;color:#991b1b;font-size:0.88rem;margin-bottom:12px;">خطا در ثبت. لطفاً دوباره امتحان کنید.</div>
      <div class="sub-form">
        <input type="tel" class="sub-input" id="subPhone" placeholder="09123456789" style="direction:ltr;text-align:left;">
        <button class="sub-btn" id="subBtn" onclick="submitPhone()">عضویت</button>
      </div>
      <div class="sub-platforms">
        <a href="#" class="pl-link"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polygon points="10 8 16 12 10 16"/></svg> اسپاتیفای</a>
        <a href="#" class="pl-link"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 1a3 3 0 0 0-3 3v8a3 3 0 0 0 6 0V4a3 3 0 0 0-3-3z"/><path d="M19 10v2a7 7 0 0 1-14 0v-2"/></svg> اپل پادکست</a>
        <a href="#" class="pl-link"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="3" width="20" height="14" rx="2"/><line x1="8" y1="21" x2="16" y2="21"/><line x1="12" y1="17" x2="12" y2="21"/></svg> کست‌باکس</a>
      </div>
    </div>
  </div>
</section>

@include('partials.comments')

@endsection

@section('scripts')
<script>
function toggleAcc(head){
  var acc=head.parentElement;
  var wasOpen=acc.classList.contains('open');
  document.querySelectorAll('.acc.open').forEach(function(a){a.classList.remove('open');a.querySelector('.acc-body').style.maxHeight='0'});
  if(!wasOpen){acc.classList.add('open');var body=acc.querySelector('.acc-body');body.style.maxHeight=body.scrollHeight+'px'}
}
function submitPhone(){
  var phone=document.getElementById('subPhone').value;
  var btn=document.getElementById('subBtn');
  if(!phone||phone.length<10){document.getElementById('subError').style.display='block';return}
  btn.disabled=true;btn.textContent='در حال ثبت...';
  fetch('/subscribe',{
    method:'POST',
    headers:{'Content-Type':'application/json','X-CSRF-TOKEN':document.getElementById('subToken').value,'Accept':'application/json'},
    body:JSON.stringify({phone:phone})
  }).then(function(r){return r.json()}).then(function(d){
    if(d.success){document.getElementById('subSuccess').style.display='block';document.getElementById('subError').style.display='none';document.getElementById('subPhone').value=''}
    else{document.getElementById('subError').style.display='block'}
    btn.disabled=false;btn.textContent='عضویت';
  }).catch(function(){document.getElementById('subError').style.display='block';btn.disabled=false;btn.textContent='عضویت'});
}
</script>
@endsection
