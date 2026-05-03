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
