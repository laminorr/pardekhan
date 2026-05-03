{!! '<?xml version="1.0" encoding="UTF-8"?>' !!}
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">

  {{-- Home --}}
  <url>
    <loc>{{ url('/') }}</loc>
    <lastmod>{{ now()->toDateString() }}</lastmod>
    <changefreq>weekly</changefreq>
    <priority>1.0</priority>
  </url>

  {{-- Episodes Archive --}}
  <url>
    <loc>{{ route('episodes.index') }}</loc>
    <lastmod>{{ now()->toDateString() }}</lastmod>
    <changefreq>weekly</changefreq>
    <priority>0.9</priority>
  </url>

  {{-- Topics Archive --}}
  <url>
    <loc>{{ route('topics.index') }}</loc>
    <lastmod>{{ now()->toDateString() }}</lastmod>
    <changefreq>weekly</changefreq>
    <priority>0.9</priority>
  </url>

  {{-- Episode Pages --}}
  @foreach($episodes as $episode)
  <url>
    <loc>{{ route('episode.show', $episode) }}</loc>
    <lastmod>{{ optional($episode->updated_at)->toDateString() ?? now()->toDateString() }}</lastmod>
    <changefreq>monthly</changefreq>
    <priority>0.8</priority>
  </url>
  @endforeach

  {{-- Topic Pages --}}
  @isset($topics)
    @foreach($topics as $topic)
  <url>
    <loc>{{ route('topics.show', $topic) }}</loc>
    <lastmod>{{ optional($topic->updated_at)->toDateString() ?? now()->toDateString() }}</lastmod>
    <changefreq>monthly</changefreq>
    <priority>0.85</priority>
  </url>
    @endforeach
  @endisset

  {{-- Tag Pages --}}
  @isset($tags)
    @foreach($tags as $tag)
  <url>
    <loc>{{ route('tag.show', str_replace(' ', '-', $tag)) }}</loc>
    <lastmod>{{ now()->toDateString() }}</lastmod>
    <changefreq>weekly</changefreq>
    <priority>0.6</priority>
  </url>
    @endforeach
  @endisset

</urlset>