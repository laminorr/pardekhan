<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>پرونده‌های موضوعی پرده‌خوان</title>
<meta name="description" content="آرشیو پرونده‌های موضوعی پرده‌خوان؛ مسیرهایی برای دنبال کردن تحلیل‌های روان‌شناختی آثار نمایشی بر اساس 
تروما، دلبستگی، روان‌پویشی، خانواده و حقیقت.">
<meta name="robots" content="index, follow">
<link rel="canonical" href="{{ route('topics.index') }}">

<meta property="og:type" content="website">
<meta property="og:title" content="پرونده‌های موضوعی پرده‌خوان">
<meta property="og:description" content="مسیرهای موضوعی برای دنبال کردن نقدها و تحلیل‌های روان‌شناختی پرده‌خوان.">
<meta property="og:url" content="{{ route('topics.index') }}">
<meta property="og:site_name" content="پرده‌خوان">
<meta property="og:locale" content="fa_IR">

<link rel="alternate" type="application/rss+xml" title="پرده‌خوان" href="{{ route('feed') }}">
<link rel="stylesheet" href="/css/pardekhan.css?v=topics-index1">
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

<div class="home-nav-links">
  <a href="{{ route('topics.index') }}" class="home-nav-link home-nav-link-primary">پرونده‌ها</a>
  <a href="{{ route('episodes.index') }}" class="home-nav-link">آرشیو</a>
  <a href="{{ route('home') }}#subscribe" class="home-nav-link">تماس</a>
</div>
  </div>
</nav>

<main class="topics-index-main">

  <header class="topics-index-header">
    <span>پرونده‌های موضوعی پرده‌خوان</span>
    <h1>مسیرهای عمیق‌تر برای خواندن آثار نمایشی</h1>
    <p>
      هر پرونده، چند نقد و تحلیل را زیر یک مسئله روان‌شناختی کنار هم می‌گذارد؛ از تروما و دلبستگی تا روان‌پویشی، خانواده، حقیقت و 
فروپاشی رابطه.
    </p>
  </header>

  <section class="topics-index-grid">
    @forelse($topics as $topic)
      <a href="{{ route('topics.show', $topic) }}" class="topic-index-card">
        <div class="topic-index-card-top">
          <span class="topic-index-kicker">{{ $topic->hero_kicker ?: 'پرونده موضوعی' }}</span>
          <span class="topic-index-count">{{ $topic->related_episodes_count ?? 0 }} اپیزود</span>
        </div>

        <h2>{{ $topic->title }}</h2>

        <p>
          {{ $topic->seo_description ?: $topic->hero_lead }}
        </p>

        <div class="topic-index-concepts">
          @foreach(array_slice($topic->key_concepts ?? [], 0, 5) as $concept)
            <span>{{ $concept }}</span>
          @endforeach
        </div>

        <div class="topic-index-more">
          ورود به پرونده
          <span>←</span>
        </div>
      </a>
    @empty
      <div class="topics-index-empty">
        هنوز پرونده‌ای منتشر نشده است.
      </div>
    @endforelse
  </section>

</main>

<footer class="home-footer">
  <div class="home-footer-inner">
    <span class="home-footer-text">پرده‌خوان — پیمان شیرپور</span>
  </div>
</footer>

</body>
</html>
