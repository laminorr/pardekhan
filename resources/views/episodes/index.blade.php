<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>آرشیو اپیزودها | پرده‌خوان</title>
<meta name="description" content="آرشیو کامل اپیزودهای پرده‌خوان؛ تحلیل و نقد آثار سینمایی و نمایشی از منظر روان‌شناسی، روایت، تروما، 
دلبستگی و روابط انسانی.">
<meta name="robots" content="index, follow">
<link rel="canonical" href="{{ route('episodes.index') }}">

<meta property="og:type" content="website">
<meta property="og:title" content="آرشیو اپیزودهای پرده‌خوان">
<meta property="og:description" content="همه اپیزودهای تحلیلی پرده‌خوان در یک صفحه؛ با امکان جستجو و فیلتر بر اساس موضوع، خالق اثر و 
سطح تحلیل.">
<meta property="og:url" content="{{ route('episodes.index') }}">
<meta property="og:site_name" content="پرده‌خوان">
<meta property="og:locale" content="fa_IR">

<link rel="alternate" type="application/rss+xml" title="پرده‌خوان" href="{{ route('feed') }}">
<link rel="stylesheet" href="/css/pardekhan.css">
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
      <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#94a3b8" stroke-width="2"><circle cx="11" cy="11" 
r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
      <input form="archiveFilterForm" type="text" name="q" value="{{ $q }}" placeholder="جستجو در اپیزودها..." 
class="home-search-input">
    </div>

<div class="home-nav-links">
  <a href="{{ route('topics.index') }}" class="home-nav-link">پرونده‌ها</a>
  <a href="{{ route('episodes.index') }}" class="home-nav-link home-nav-link-primary">آرشیو</a>
  <a href="{{ route('home') }}#subscribe" class="home-nav-link">تماس</a>
</div>
</nav>

<main class="archive-main">

<header class="archive-header">
  <h1>آرشیو اپیزودها</h1>
  <p>همه تحلیل‌ها و نقدهای پرده‌خوان، با امکان جستجو و فیلتر بر اساس موضوع، خالق اثر و سطح تحلیل.</p>
</header>

  <div class="archive-layout">

    <section class="archive-results">
      <div class="archive-results-top">
        <div>
          <h2>اپیزودها</h2>
          <p>{{ $episodes->total() }} اپیزود پیدا شد</p>
        </div>
        <a href="{{ route('feed') }}" class="archive-rss">RSS</a>
      </div>

      <div class="archive-list">
        @forelse($episodes as $ep)
          <article class="archive-card">
            <a href="{{ route('episode.show', $ep) }}" class="archive-cover" style="{{ $ep->cover_image ? '--cover-bg:url('.asset('storage/'.$ep->cover_image).')' : '' }}">
              <img src="{{ $ep->cover_image ? asset('storage/'.$ep->cover_image) : '' }}" alt="{{ $ep->title_fa }}" style="{{ 
$ep->cover_image ? '' : 'display:none' }}">
              <div class="archive-cover-fallback" style="{{ $ep->cover_image ? 'display:none' : '' }}">
                <span>پ</span>
              </div>
            </a>

            <div class="archive-card-body">
              <div class="archive-ep-number">اپیزود {{ sprintf('%02d', $ep->episode_number) }}</div>

              <h3>
                <a href="{{ route('episode.show', $ep) }}">تحلیل {{ $ep->title_fa }}</a>
              </h3>

              <div class="archive-meta-line">
                <span>{{ $ep->title_en }}</span>
                <span>{{ $ep->director }}</span>
                <span>{{ $ep->year }}</span>
              </div>

              <p class="archive-summary">
                {{ $ep->seo_description ?: $ep->hero_lead }}
              </p>

              <div class="archive-tags">
                @foreach(array_slice($ep->meta_tags ?? [], 0, 4) as $tag)
                  <a href="{{ route('episodes.index', ['tags' => [$tag]]) }}">{{ $tag }}</a>
                @endforeach
              </div>
            </div>

            <div class="archive-card-side">
              <div class="archive-duration">زمان پادکست: {{ $ep->meta_duration }}</div>
              <a href="{{ route('episode.show', $ep) }}" class="archive-btn">
                مشاهده اپیزود
                <span>▶</span>
              </a>
            </div>
          </article>
        @empty
          <div class="archive-empty">
            <h3>نتیجه‌ای پیدا نشد</h3>
            <p>فیلترها را تغییر بده یا جستجو را ساده‌تر کن.</p>
            <a href="{{ route('episodes.index') }}">پاک کردن فیلترها</a>
          </div>
        @endforelse
      </div>

      @if($episodes->hasPages())
        <div class="archive-pagination">
          @if($episodes->onFirstPage())
            <span class="disabled">قبلی</span>
          @else
            <a href="{{ $episodes->previousPageUrl() }}">قبلی</a>
          @endif

          @for($i = 1; $i <= $episodes->lastPage(); $i++)
            @if($i === $episodes->currentPage())
              <span class="active">{{ $i }}</span>
            @else
              <a href="{{ $episodes->url($i) }}">{{ $i }}</a>
            @endif
          @endfor

          @if($episodes->hasMorePages())
            <a href="{{ $episodes->nextPageUrl() }}">بعدی</a>
          @else
            <span class="disabled">بعدی</span>
          @endif
        </div>
      @endif
    </section>

    <aside class="archive-filters">
      <form id="archiveFilterForm" method="GET" action="{{ route('episodes.index') }}">
        <div class="archive-filter-title">
          <span>فیلترها</span>
          <a href="{{ route('episodes.index') }}">پاک کردن</a>
        </div>

        <label class="archive-field">
          <span>جستجو</span>
          <input type="text" name="q" value="{{ $q }}" placeholder="عنوان، خالق اثر یا موضوع...">
        </label>

        <label class="archive-field">
          <span>خالق اثر</span>
          <select name="director">
            <option value="">همه</option>
            @foreach($directors as $item)
              <option value="{{ $item }}" @selected($director === $item)>{{ $item }}</option>
            @endforeach
          </select>
        </label>

        <label class="archive-field">
          <span>سطح تحلیل</span>
          <select name="level">
            <option value="">همه</option>
            @foreach($levels as $item)
              <option value="{{ $item }}" @selected($level === $item)>{{ $item }}</option>
            @endforeach
          </select>
        </label>

        <div class="archive-checks">
          <span>موضوع‌ها و برچسب‌ها</span>

          <div class="archive-check-list">
            @foreach($tags as $tag)
              <label>
                <input type="checkbox" name="tags[]" value="{{ $tag }}" @checked(in_array($tag, $selectedTags))>
                <span>{{ $tag }}</span>
              </label>
            @endforeach
          </div>
        </div>

        <button type="submit" class="archive-submit">اعمال فیلترها</button>
      </form>
    </aside>

  </div>
</main>
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

<footer class="home-footer">
  <div class="home-footer-inner">
    <span class="home-footer-text">پرده‌خوان — پیمان شیرپور</span>
    <div class="home-footer-socials">
      <a href="#"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="2" width="20" height="20" rx="5"/><path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"/><line x1="17.5" y1="6.5" x2="17.51" y2="6.5"/></svg></a>
      <a href="#"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="22" y1="2" x2="11" y2="13"/><polygon points="22 2 15 22 11 13 2 9"/></svg></a>
      <a href="#"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22.54 6.42a2.78 2.78 0 0 0-1.94-2C18.88 4 12 4 12 4s-6.88 0-8.6.46a2.78 2.78 0 0 0-1.94 2A29 29 0 0 0 1 11.75a29 29 0 0 0 .46 5.33A2.78 2.78 0 0 0 3.4 19.1c1.72.46 8.6.46 8.6.46s6.88 0 8.6-.46a2.78 2.78 0 0 0 1.94-2 29 29 0 0 0 .46-5.25 29 29 0 0 0-.46-5.43z"/><polygon points="9.75 15.02 15.5 11.75 9.75 8.48"/></svg></a>
    </div>
  </div>
</footer>

<script>
function submitPhone(){
  var phone=document.getElementById('subPhone').value;
  var btn=document.getElementById('subBtn');

  if(!phone || phone.length < 10){
    document.getElementById('subError').style.display='block';
    return;
  }

  btn.disabled=true;
  btn.textContent='در حال ثبت...';

  fetch('/subscribe',{
    method:'POST',
    headers:{
      'Content-Type':'application/json',
      'X-CSRF-TOKEN':document.getElementById('subToken').value,
      'Accept':'application/json'
    },
    body:JSON.stringify({phone:phone})
  }).then(function(r){
    return r.json();
  }).then(function(d){
    if(d.success){
      document.getElementById('subSuccess').style.display='block';
      document.getElementById('subError').style.display='none';
      document.getElementById('subPhone').value='';
    }else{
      document.getElementById('subError').style.display='block';
    }

    btn.disabled=false;
    btn.textContent='عضویت';
  }).catch(function(){
    document.getElementById('subError').style.display='block';
    btn.disabled=false;
    btn.textContent='عضویت';
  });
}
</script>
</body>
</html>
