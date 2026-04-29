<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
@php
    $defaultTitle = 'پرده‌خوان — تحلیل روان‌شناختی فیلم';
    $defaultDescription = 'تحلیل روان‌شناختی فیلم‌ها با اجرا و تحلیل پیمان شیرپور';
    $defaultImage = asset('images/og-default.jpg');

    $pageTitle = trim($__env->yieldContent('title', $defaultTitle));
    $pageDescription = trim($__env->yieldContent('description', $defaultDescription));
    $pageCanonical = trim($__env->yieldContent('canonical', url()->current()));
    $pageRobots = trim($__env->yieldContent('robots', 'index, follow'));
    $pageOgTitle = trim($__env->yieldContent('og_title', $pageTitle));
    $pageOgImage = trim($__env->yieldContent('og_image', $defaultImage)) ?: $defaultImage;
@endphp

<title>{{ $pageTitle }}</title>
<meta name="description" content="{{ $pageDescription }}">
<meta name="robots" content="{{ $pageRobots }}">
<meta name="theme-color" content="#ffffff">
<link rel="canonical" href="{{ $pageCanonical }}">

<meta property="og:type" content="@yield('og_type', 'article')">
<meta property="og:title" content="{{ $pageOgTitle }}">
<meta property="og:description" content="{{ $pageDescription }}">
<meta property="og:url" content="{{ $pageCanonical }}">
<meta property="og:site_name" content="پرده‌خوان">
<meta property="og:locale" content="fa_IR">
<meta property="og:image" content="{{ $pageOgImage }}">

<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="{{ $pageOgTitle }}">
<meta name="twitter:description" content="{{ $pageDescription }}">
<meta name="twitter:image" content="{{ $pageOgImage }}">

<link rel="alternate" type="application/rss+xml" title="پرده‌خوان" href="{{ route('feed') }}">


@yield('schema')

<link rel="stylesheet" href="/css/pardekhan.css">
</head>
<body>

<div class="prog" id="prog"></div>

<nav id="nav">
  <a href="{{ route('home') }}" class="n-logo">
    <div class="n-mark">پ</div>
    <span class="n-name">پرده‌خوان</span>
  </a>
  @yield('nav-links')
  <a href="#listen" class="n-cta">
    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polygon points="5 3 19 12 5 21 5 3"/></svg>
    گوش بدید
  </a>
</nav>

@yield('content')

<footer>
  <div class="f-brand"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 1a3 3 0 0 0-3 3v8a3 3 0 0 0 6 0V4a3 3 0 0 0-3-3z"/><path d="M19 10v2a7 7 0 0 1-14 0v-2"/></svg> پرده‌خوان</div>
  <p class="f-by">با اجرا و تحلیل <strong>پیمان شیرپور</strong></p>
  <p class="f-desc">تحلیل روان‌شناختی فیلم‌ها. ویژه علاقه‌مندان و متخصصان.</p>
  <div class="f-socials">
    <a href="#"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="2" width="20" height="20" rx="5"/><path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"/><line x1="17.5" y1="6.5" x2="17.51" y2="6.5"/></svg></a>
    <a href="#"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="22" y1="2" x2="11" y2="13"/><polygon points="22 2 15 22 11 13 2 9"/></svg></a>
    <a href="#"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22.54 6.42a2.78 2.78 0 0 0-1.94-2C18.88 4 12 4 12 4s-6.88 0-8.6.46a2.78 2.78 0 0 0-1.94 2A29 29 0 0 0 1 11.75a29 29 0 0 0 .46 5.33A2.78 2.78 0 0 0 3.4 19.1c1.72.46 8.6.46 8.6.46s6.88 0 8.6-.46a2.78 2.78 0 0 0 1.94-2 29 29 0 0 0 .46-5.25 29 29 0 0 0-.46-5.43z"/><polygon points="9.75 15.02 15.5 11.75 9.75 8.48"/></svg></a>
  </div>
  <p class="f-copy">&copy; ۱۴۰۵ — تمامی حقوق محفوظ است</p>
</footer>

<script>
window.addEventListener('scroll',function(){
  var h=document.documentElement;
  document.getElementById('prog').style.width=(h.scrollTop/(h.scrollHeight-h.clientHeight))*100+'%';
  document.getElementById('nav').classList.toggle('scrolled',window.scrollY>60);
});
var obs=new IntersectionObserver(function(es){es.forEach(function(e){if(e.isIntersecting){e.target.classList.add('v');obs.unobserve(e.target)}})},{threshold:0.06,rootMargin:'0px 0px -30px 0px'});
document.querySelectorAll('.rv').forEach(function(el){obs.observe(el)});
</script>
@yield('scripts')
</body>
</html>
