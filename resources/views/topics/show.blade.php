<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>{{ $topic->seo_title ?: $topic->title }}</title>
<meta name="description" content="{{ $topic->seo_description ?: $topic->hero_lead }}">
<meta name="robots" content="index, follow">
<link rel="canonical" href="{{ route('topics.show', $topic) }}">

<meta property="og:type" content="article">
<meta property="og:title" content="{{ $topic->seo_title ?: $topic->title }}">
<meta property="og:description" content="{{ $topic->seo_description ?: $topic->hero_lead }}">
<meta property="og:url" content="{{ route('topics.show', $topic) }}">
<meta property="og:site_name" content="پرده‌خوان">
<meta property="og:locale" content="fa_IR">

<link rel="alternate" type="application/rss+xml" title="پرده‌خوان" href="{{ route('feed') }}">
<link rel="stylesheet" href="/css/pardekhan.css?v=topic1">
</head>

<body>

<div class="topic-page">

  <nav class="topic-nav">
    <a class="topic-brand" href="{{ route('home') }}">
      <span class="topic-mark">پ</span>
      <span>پرده‌خوان</span>
    </a>

<div class="topic-nav-links">
  <a class="active" href="{{ route('topics.index') }}">پرونده‌ها</a>
  <a href="{{ route('episodes.index') }}">آرشیو</a>
  <a href="{{ route('home') }}#subscribe">تماس</a>
</div>
  </nav>


  <main class="topic-wrap">

    <aside class="topic-side">
      <div class="topic-side-title">در این پرونده</div>
      <a class="on" href="#intro">مقدمه <span>01</span></a>
      <a href="#concepts">مفاهیم کلیدی <span>02</span></a>
      <a href="#episodes">اپیزودهای مرتبط <span>03</span></a>
      <a href="#tags">تگ‌های مرتبط <span>04</span></a>
      <a href="#faq">پرسش‌های رایج <span>05</span></a>
    </aside>

    <section class="topic-content">

      <article class="topic-panel" id="intro">
        @forelse($topic->sections ?? [] as $section)
          <h2>{{ $section['title'] ?? 'مقدمه پرونده' }}</h2>
          <p>{{ $section['body'] ?? '' }}</p>
        @empty
          <h2>{{ $topic->title }}</h2>
          <p>{{ $topic->hero_lead }}</p>
        @endforelse
      </article>

      <article class="topic-panel" id="concepts">
        <div class="topic-section-head">
          <span>Concept Map</span>
          <h2>مفاهیم کلیدی این پرونده</h2>
        </div>

        <p>
          این مفاهیم ستون‌های اصلی پرونده‌اند. کاربر با آن‌ها مسیر خواندن اپیزودها را بهتر می‌فهمد و موتور جستجو هم رابطه موضوعی میان صفحه‌ها را دقیق‌تر تشخیص می‌دهد.
        </p>

        <div class="topic-concepts">
          @foreach($topic->key_concepts ?? [] as $concept)
            <div class="topic-chip">{{ $concept }}</div>
          @endforeach
        </div>
      </article>

      <article class="topic-panel" id="episodes">
        <div class="topic-section-head">
          <span>Related Episodes</span>
          <h2>اپیزودهای مرتبط با این پرونده</h2>
        </div>

        <p>
          این اپیزودها بر اساس اتصال دستی و تگ‌های مرتبط انتخاب شده‌اند. هرکدام بخشی از نقشه روان‌شناختی این موضوع را کامل می‌کنند.
        </p>

        <div class="topic-episodes">
          @forelse($episodes as $ep)
            <a class="topic-ep" href="{{ route('episode.show', $ep) }}">
              <div class="topic-poster" style="{{ $ep->cover_image ? 'background-image:url('.asset('storage/'.$ep->cover_image).')' : '' }}">
                @if(! $ep->cover_image)
                  <span>پ</span>
                @endif
              </div>

              <div class="topic-ep-body">
                <div class="topic-ep-meta">
                  اپیزود {{ sprintf('%02d', $ep->episode_number) }}
                  @if($ep->meta_duration)
                    <span>زمان پادکست: {{ $ep->meta_duration }}</span>
                  @endif
                </div>

                <h3>نقد {{ $ep->title_fa }}</h3>

                <p>{{ $ep->seo_description ?: $ep->hero_lead }}</p>

                <div class="topic-ep-tags">
                  @foreach(array_slice($ep->meta_tags ?? [], 0, 3) as $tag)
                    <span>{{ $tag }}</span>
                  @endforeach
                </div>
              </div>
            </a>
          @empty
            <div class="topic-empty">
              هنوز اپیزودی برای این پرونده وصل نشده است.
            </div>
          @endforelse
        </div>
      </article>

      <article class="topic-panel" id="tags">
<div class="topic-section-head">
  <span>مسیرهای پیشنهادی</span>
  <h2>موضوعات نزدیک به این پرونده</h2>
</div>

<p>
  این برچسب‌ها راه‌های دیگر ورود به همین مسئله‌اند. هرکدام شما را به مجموعه‌ای از نقدها و تحلیل‌هایی می‌برند که با {{ $topic->title }} پیوند دارند.
</p>

        <div class="topic-tags">
          @foreach($topic->related_tags ?? [] as $tag)
            <a class="topic-tag" href="{{ route('tag.show', str_replace(' ', '-', $tag)) }}">{{ $tag }}</a>
          @endforeach
        </div>
      </article>

      <article class="topic-panel" id="faq">
        <div class="topic-section-head">
          <span>FAQ</span>
          <h2>پرسش‌های رایج</h2>
        </div>

        <div class="topic-faq">
          @forelse($topic->faq ?? [] as $item)
            <div class="topic-q">
              <strong>{{ $item['question'] ?? '' }}</strong>
              <p>{{ $item['answer'] ?? '' }}</p>
            </div>
          @empty
            <div class="topic-empty">
              هنوز پرسشی برای این پرونده ثبت نشده است.
            </div>
          @endforelse
        </div>
      </article>

    </section>
  </main>

  <section class="topic-footer-cta">
    <div class="topic-cta-box">
      <div>
        <h2>این فقط یک صفحه نیست، یک پرونده زنده است</h2>
        <p>با اضافه شدن هر اپیزود جدید، این پرونده کامل‌تر می‌شود و مسیرهای تازه‌ای میان آثار، مفاهیم و تحلیل‌ها می‌سازد.</p>
      </div>

      <a class="topic-btn topic-btn-primary" href="{{ route('episodes.index') }}">بازگشت به آرشیو</a>
    </div>
  </section>

</div>

</body>
</html>