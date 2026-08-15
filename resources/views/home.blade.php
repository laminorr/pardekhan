<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>پرده‌خوان — تحلیل روان‌شناختی فیلم | پیمان شیرپور</title>
<meta name="description" content="تحلیل روان‌شناختی فیلم‌ها از دریچه بالینی. هر فیلم، یک پرونده بالینی. با اجرا و تحلیل پیمان شیرپور.">
<meta name="theme-color" content="#ffffff">
<link rel="canonical" href="{{ url('/') }}">
<meta property="og:type" content="website">
<meta property="og:title" content="پرده‌خوان — تحلیل روان‌شناختی فیلم">
<meta property="og:description" content="تحلیل روان‌شناختی فیلم‌ها از دریچه بالینی. با اجرا و تحلیل پیمان شیرپور.">
<meta property="og:url" content="{{ url('/') }}">
<meta property="og:site_name" content="پرده‌خوان">
<meta property="og:locale" content="fa_IR">
<link rel="alternate" type="application/rss+xml" title="پرده‌خوان" href="{{ route('feed') }}">
<link rel="stylesheet" href="/css/pardekhan.css">
<style>
  /* لینک ظریف «دیدن ادامه» و بخش تئاتر — بدون بازنویسی استایل سراسری */
  .home-more{text-align:left;padding:0 2px 8px;margin-top:-14px}
  .home-more-link{display:inline-flex;align-items:center;gap:4px;font-size:0.82rem;font-weight:700;color:#0d9488;text-decoration:none;transition:opacity .2s}
  .home-more-link:hover{opacity:.65}
  .home-theater{margin-top:12px;border-top:0.5px solid rgba(0,0,0,0.06);padding-top:28px}
  /* دسکتاپ: تیتر راست، زیرعنوان ته چپ همان خط — کم‌ارتفاع. موبایل دست‌نخورده */
  @media(min-width:641px){
    .home-header{text-align:right;display:flex;flex-wrap:wrap;align-items:baseline;justify-content:space-between}
    .home-header-icon{flex:0 0 100%;justify-content:flex-start;font-size:0.72rem;margin-bottom:4px}
    .home-title{font-size:1.15rem;font-weight:800;text-align:right;margin:0}
    .home-subtitle{font-size:0.82rem;text-align:left;margin:0;color:#94a3b8}
    .home-grid{padding-top:12px}
  }

  /* ── ویترین کاورها: نوار تزئینی تمام‌عرض با دو ردیف بی‌نهایت ── */
  /* استایل کاملاً محدود به .showcase-marquee است و روی سایر صفحات اثری ندارد */
  .showcase-marquee{
    width:100%;
    background:#e8e9e7;
    padding:30px 0;
    overflow:hidden;                 /* پنهان‌کردن سرریز افقی نوار بلند */
    border-top:1px solid rgba(255,255,255,0.05);
    border-bottom:1px solid rgba(255,255,255,0.05);
    /* محو نرم لبه‌های چپ و راست برای حس «پرمیوم» */
  }
  .showcase-row{overflow:visible}   /* اجازه‌ی بالا‌آمدن کاور هنگام هاور داخل paddingِ نوار */
  .showcase-row + .showcase-row{margin-top:18px}
  .showcase-track{
    display:inline-flex;
    width:max-content;               /* عرض بر اساس محتوا → با هر تعداد کاور کار می‌کند */
    gap:0;
    will-change:transform;
  }
  /* هر ریل دقیقاً دو «گروه» یکسان دارد؛ چون هر دو گروه هم‌عرض‌اند، -۵۰٪ همیشه دقیقاً روی درز می‌نشیند */
  .showcase-group{
    display:flex;
    flex:0 0 auto;
    gap:0;                           /* فاصله‌ی بین کاورها فقط با margin-left خودشان است → عرض دو گروه دقیقاً برابر */
  }
  /* دو ردیف در جهت مخالف؛ آهسته و آرام (یک دور کامل ~۷۲/۸۴ ثانیه) */
  .showcase-track-a{animation:showcase-scroll 72s linear infinite}
  .showcase-track-b{animation:showcase-scroll 84s linear infinite reverse}
  /* هاور روی هرجای نوار → توقف هر دو ردیف */
  .showcase-marquee:hover .showcase-track{animation-play-state:paused}

  @keyframes showcase-scroll{
    from{transform:translateX(0)}
    to{transform:translateX(-50%)}   /* دو گروهِ هم‌عرض → -۵۰٪ دقیقاً به اندازه‌ی یک گروه جابه‌جا می‌شود و بی‌درز حلقه می‌زند */
  }

  .showcase-item{
    position:relative;
    display:block;
    flex:0 0 auto;
    margin-left:16px;                /* فاصله فقط از یک سمت + gap:0 → عرض هر گروه = N×(عرض کاور+۱۶) و درز = فاصله‌ی داخلی */
    border-radius:6px;
    text-decoration:none;
    /* حالت پیش‌فرض: سیاه‌وسفید و کمی محو برای ظاهری یکدست و ظریف */
    filter:grayscale(100%) contrast(1.05) brightness(0.95);
    opacity:.8;
    transition:transform .4s ease, filter .4s ease, opacity .4s ease, box-shadow .4s ease;
  }
  .showcase-item img{
    display:block;
    height:150px;                    /* ردیف فیلم (بالا) — بزرگ‌تر */
    width:auto;
    aspect-ratio:2/3;                /* نسبت پوستر؛ ابعاد ثابت → بدون layout shift */
    object-fit:cover;
    border-radius:6px;
    background:#161618;
  }
  .showcase-row-book .showcase-item img{height:110px}   /* ردیف کتاب (پایین) — کوچک‌تر */
  /* هاور روی یک کاور → تمام‌رنگ، بزرگ‌نمایی و سایه‌ی نرم */
  .showcase-item:hover{
    filter:none;
    opacity:1;
    transform:scale(1.04) translateY(-2px);
    box-shadow:0 10px 26px rgba(0,0,0,0.55);
    z-index:2;
  }

  @media(max-width:640px){
    .showcase-marquee{padding:18px 0}
    .showcase-track{gap:0}
    .showcase-row + .showcase-row{margin-top:12px}
    .showcase-item img{height:110px}                    /* فیلم روی موبایل */
    .showcase-row-book .showcase-item img{height:80px}  /* کتاب روی موبایل */
  }

  /* احترام به prefers-reduced-motion → ردیف‌ها ثابت می‌مانند */
  @media(prefers-reduced-motion:reduce){
    .showcase-track{animation:none}
  }
</style>
</head>
<body>

<nav class="home-nav">
  <div class="home-nav-inner">
    <a href="{{ route('home') }}" class="home-nav-brand">
      <div class="home-nav-mark">پ</div>
      <span class="home-nav-name">پرده‌خوان</span>
      <div class="home-nav-sep"></div>
      <span class="home-nav-author">پیمان شیرپور</span>
    </a>
    <div class="home-nav-search">
      <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#94a3b8" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
      <input type="text" placeholder="جستجو در اپیزودها..." class="home-search-input">
    </div>
<div class="home-nav-links">
  <a href="{{ route('topics.index') }}" class="home-nav-link">پرونده‌ها</a>
  <a href="{{ route('episodes.index') }}" class="home-nav-link home-nav-link-primary">آرشیو</a>
  <a href="{{ route('home') }}#subscribe" class="home-nav-link">تماس</a>
</div>
</nav>

{{-- بنر معرفی سراسری --}}
<div style="background:linear-gradient(135deg,#0f766e,#115e59);color:#fff;">
  <div style="max-width:1200px;margin:0 auto;padding:18px 32px;font-size:0.95rem;line-height:1.9;text-align:center;font-weight:500;">
    نقد و خوانش فیلم و تئاتر با رویکرد روان‌شناختی؛ از ساختار شخصیت و پویایی روابط تا تعارض‌های درون‌روانی، الگوهای دلبستگی و جهان ذهنی انسان.
  </div>
</div>
{{-- ── ویترین کاورها: نوار متحرک تمام‌عرض (دو ردیف در جهت مخالف). چیزی رندر نمی‌شود اگر هر دو خالی باشند ── --}}
@php
  $showcaseFilms = $showcaseFilms ?? collect();
  $showcaseBooks = $showcaseBooks ?? collect();

  // هر «گروه» به‌قدر کافی تکرار می‌شود تا حتی با چند کاورِ اندک هم از عرض هر نمایشگر پهن بزرگ‌تر شود.
  // سپس همین گروهِ یکسان دقیقاً دوبار داخل ریل رندر می‌شود؛ چون دو نیمه کاملاً هم‌عرض‌اند،
  // انیمیشن -۵۰٪ همیشه دقیقاً به‌اندازه‌ی یک گروه جابه‌جا می‌شود و حلقه بدون پرش/ریست می‌بندد.
  $filmGroup = collect();
  if ($showcaseFilms->isNotEmpty()) {
    $rep = max(2, (int) ceil(26 / $showcaseFilms->count()));   // ≥۲۶ کاور در هر گروه (~۳۰۰۰px) → پُرکردن نمایشگرهای پهن
    for ($i = 0; $i < $rep; $i++) { $filmGroup = $filmGroup->concat($showcaseFilms); }
  }
  $bookGroup = collect();
  if ($showcaseBooks->isNotEmpty()) {
    $rep = max(2, (int) ceil(32 / $showcaseBooks->count()));   // کاورِ کتاب کوچک‌تر است → تکرار بیشتر برای همان عرض
    for ($i = 0; $i < $rep; $i++) { $bookGroup = $bookGroup->concat($showcaseBooks); }
  }
@endphp
@if($showcaseFilms->isNotEmpty() || $showcaseBooks->isNotEmpty())
<section class="showcase-marquee">
  @if($showcaseFilms->isNotEmpty())
  <div class="showcase-row showcase-row-film">
    <div class="showcase-track showcase-track-a">
      {{-- گروهِ کاملاً یکسان دقیقاً دوبار رندر می‌شود → دو نیمه‌ی هم‌عرض → -۵۰٪ دقیقاً روی درز --}}
      @for($g = 0; $g < 2; $g++)
      <div class="showcase-group" @if($g === 1) aria-hidden="true" @endif>
        @foreach($filmGroup as $c)
          @if($c->imdb_url)
            <a href="{{ $c->imdb_url }}" target="_blank" rel="noopener noreferrer" class="showcase-item" aria-label="مشاهده در IMDB">
              <img src="{{ asset('storage/'.$c->image) }}" alt="" width="100" height="150" loading="lazy" decoding="async">
            </a>
          @else
            <div class="showcase-item">
              <img src="{{ asset('storage/'.$c->image) }}" alt="" width="100" height="150" loading="lazy" decoding="async">
            </div>
          @endif
        @endforeach
      </div>
      @endfor
    </div>
  </div>
  @endif
  @if($showcaseBooks->isNotEmpty())
  <div class="showcase-row showcase-row-book">
    <div class="showcase-track showcase-track-b">
      @for($g = 0; $g < 2; $g++)
      <div class="showcase-group" @if($g === 1) aria-hidden="true" @endif>
        @foreach($bookGroup as $c)
          @if($c->imdb_url)
            <a href="{{ $c->imdb_url }}" target="_blank" rel="noopener noreferrer" class="showcase-item" aria-label="مشاهده لینک">
              <img src="{{ asset('storage/'.$c->image) }}" alt="" width="74" height="110" loading="lazy" decoding="async">
            </a>
          @else
            <div class="showcase-item">
              <img src="{{ asset('storage/'.$c->image) }}" alt="" width="74" height="110" loading="lazy" decoding="async">
            </div>
          @endif
        @endforeach
      </div>
      @endfor
    </div>
  </div>
  @endif
</section>
@endif

<main class="home-main">
  <div class="home-header">
    <div class="home-header-icon">
      <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#0d9488" stroke-width="2"><path d="M12 1a3 3 0 0 0-3 3v8a3 3 0 0 0 6 0V4a3 3 0 0 0-3-3z"/><path d="M19 10v2a7 7 0 0 1-14 0v-2"/></svg>
      <span>تحلیل روان‌شناختی فیلم</span>
    </div>
    <h1 class="home-title">اپیزودهای تحلیل شده</h1>
    <p class="home-subtitle">هر فیلم، یک پرونده بالینی</p>
  </div>

  <div class="home-grid">
    @forelse($episodes as $ep)
    <a href="{{ route('episode.show', $ep) }}" class="ep-card">
      <div class="ep-cover">
        <img src="{{ $ep->cover_image ? asset('storage/'.$ep->cover_image) : '' }}" alt="{{ $ep->title_fa }}" style="{{ $ep->cover_image ? '' : 'display:none' }}">
        <div class="ep-cover-fallback" style="{{ $ep->cover_image ? 'display:none' : '' }}">
          <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="rgba(255,255,255,0.3)" stroke-width="1.5"><path d="M12 1a3 3 0 0 0-3 3v8a3 3 0 0 0 6 0V4a3 3 0 0 0-3-3z"/><path d="M19 10v2a7 7 0 0 1-14 0v-2"/></svg>
        </div>
        <div class="ep-badge">{{ sprintf('%02d', $ep->episode_number) }}</div>
        <div class="ep-duration">{{ $ep->meta_duration }}</div>
      </div>
      <div class="ep-info">
        <h2 class="ep-title">{{ $ep->title_fa }}</h2>
        <p class="ep-director">{{ $ep->director }} · {{ $ep->year }}</p>
        <div class="ep-tags">
          @foreach(array_slice($ep->meta_tags ?? [], 0, 3) as $tag)
          <span class="ep-tag">{{ $tag }}</span>
          @endforeach
        </div>
      </div>
    </a>
    @empty
    <div class="home-empty">
      <p>هنوز اپیزودی منتشر نشده</p>
    </div>
    @endforelse
  </div>

  <div class="home-more">
    <a href="{{ route('episodes.index') }}" class="home-more-link">دیدن ادامهٔ نقدها →</a>
  </div>

  @if(isset($theaterReviews) && $theaterReviews->isNotEmpty())
  <div class="home-header home-theater">
    <div class="home-header-icon">
      <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#0d9488" stroke-width="2"><path d="M2 12s2.5-7 10-7 10 7 10 7"/><path d="M6 12a2 2 0 0 0 4 0"/><path d="M14 12a2 2 0 0 0 4 0"/></svg>
      <span>نقد و تحلیل تئاتر</span>
    </div>
    <h2 class="home-title">نقد تئاتر</h2>
    <p class="home-subtitle">تحلیل و نقد اجراهای صحنه</p>
  </div>

  <div class="home-grid">
    @foreach($theaterReviews as $review)
    <a href="{{ route('theater.show', $review) }}" class="ep-card">
      <div class="ep-cover">
        <img src="{{ $review->cover ? asset('storage/'.$review->cover) : '' }}" alt="{{ $review->title }}" style="{{ $review->cover ? '' : 'display:none' }}">
        <div class="ep-cover-fallback" style="{{ $review->cover ? 'display:none' : '' }}">
          <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="rgba(255,255,255,0.3)" stroke-width="1.5"><path d="M2 12s2.5-7 10-7 10 7 10 7"/><path d="M6 12a2 2 0 0 0 4 0"/><path d="M14 12a2 2 0 0 0 4 0"/></svg>
        </div>
      </div>
      <div class="ep-info">
        <h2 class="ep-title">{{ $review->title }}</h2>
      </div>
    </a>
    @endforeach
  </div>
  @endif

  <div class="home-subscribe" id="subscribe">
    <div class="home-subscribe-inner">
      <p class="home-subscribe-text">شماره‌تون رو بذارید تا هر اپیزود جدید رو زودتر از بقیه بشنوید</p>
      <input type="hidden" id="subToken" value="{{ csrf_token() }}">
      <div id="subSuccess" class="home-msg home-msg-ok" style="display:none">شماره شما با موفقیت ثبت شد!</div>
      <div id="subError" class="home-msg home-msg-err" style="display:none">خطا در ثبت. لطفاً دوباره امتحان کنید.</div>
      <div class="home-subscribe-form">
        <input type="tel" id="subPhone" class="home-subscribe-input" placeholder="09123456789">
        <button id="subBtn" class="home-subscribe-btn" onclick="submitPhone()">عضویت</button>
      </div>
    </div>
  </div>
</main>

<footer class="home-footer">
  <div class="home-footer-inner">
    <div class="home-footer-brand">
      <span class="home-footer-text">پرده‌خوان — پیمان شیرپور</span>
      <div class="home-footer-contact">
        <p>نشانی: تهران، محله کشاورز، خیابان رستاک، خیابان ورنوس، پلاک ۹، طبقه ۱، واحد ۲ &nbsp;|&nbsp; تلفن: ۸۸۹۹۸۲۵۰</p>
      </div>
    </div>

    <div class="home-footer-tools">
      <div class="home-footer-socials">
        <a href="#" aria-label="YouTube">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M22.54 6.42a2.78 2.78 0 0 0-1.94-2C18.88 4 12 4 12 4s-6.88 0-8.6.46a2.78 2.78 0 0 0-1.94 2A29 29 0 0 0 1 11.75a29 29 0 0 0 .46 5.33A2.78 2.78 0 0 0 3.4 19.1c1.72.46 8.6.46 8.6.46s6.88 0 8.6-.46a2.78 2.78 0 0 0 1.94-2 29 29 0 0 0 .46-5.25 29 29 0 0 0-.46-5.43z"/>
            <polygon points="9.75 15.02 15.5 11.75 9.75 8.48"/>
          </svg>
        </a>

        <a href="#" aria-label="Telegram">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <line x1="22" y1="2" x2="11" y2="13"/>
            <polygon points="22 2 15 22 11 13 2 9"/>
          </svg>
        </a>

        <a href="#" aria-label="Instagram">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <rect x="2" y="2" width="20" height="20" rx="5"/>
            <path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"/>
            <line x1="17.5" y1="6.5" x2="17.51" y2="6.5"/>
          </svg>
        </a>
      </div>

      <div class="home-enamad">
        <a referrerpolicy="origin" target="_blank" href="https://trustseal.enamad.ir/?id=727918&Code=zSYYrQjm5BS7fX0QrdURANvznKm6tCCP">
          <img referrerpolicy="origin" src="https://trustseal.enamad.ir/logo.aspx?id=727918&Code=zSYYrQjm5BS7fX0QrdURANvznKm6tCCP" alt="نماد اعتماد الکترونیکی" style="cursor:pointer" code="zSYYrQjm5BS7fX0QrdURANvznKm6tCCP">
        </a>
      </div>
    </div>
  </div>
</footer>

<script>
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
</body>
</html>
