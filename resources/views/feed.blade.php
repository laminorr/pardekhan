{!! '<?xml version="1.0" encoding="UTF-8"?>' !!}
<rss version="2.0" xmlns:atom="http://www.w3.org/2005/Atom" xmlns:itunes="http://www.itunes.com/dtds/podcast-1.0.dtd">
  <channel>
    <title>پرده‌خوان — تحلیل روان‌شناختی فیلم</title>
    <link>{{ url('/') }}</link>
    <description>تحلیل روان‌شناختی فیلم‌ها از دریچه بالینی. با اجرا و تحلیل پیمان شیرپور.</description>
    <language>fa</language>
    <atom:link href="{{ route('feed') }}" rel="self" type="application/rss+xml"/>
    <itunes:author>پیمان شیرپور</itunes:author>
    <itunes:category text="Health &amp; Fitness"><itunes:category text="Mental Health"/></itunes:category>
    @foreach($episodes as $episode)
    <item>
      <title>اپیزود {{ $episode->episode_number }} — {{ $episode->title_fa }}</title>
      <link>{{ url($episode->slug) }}</link>
      <guid isPermaLink="true">{{ url($episode->slug) }}</guid>
      <pubDate>{{ $episode->published_at?->toRssString() }}</pubDate>
      <description>{{ $episode->hero_lead }}</description>
      <itunes:author>پیمان شیرپور</itunes:author>
      <itunes:duration>{{ $episode->meta_duration }}</itunes:duration>
    </item>
    @endforeach
  </channel>
</rss>
