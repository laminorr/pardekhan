{!! '<?xml version="1.0" encoding="UTF-8"?>' !!}
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
  <url>
    <loc>{{ url('/') }}</loc>
    <changefreq>weekly</changefreq>
    <priority>1.0</priority>
  </url>
  @foreach($episodes as $episode)
  <url>
    <loc>{{ url($episode->slug) }}</loc>
    <lastmod>{{ $episode->updated_at->toIso8601String() }}</lastmod>
    <changefreq>monthly</changefreq>
    <priority>0.8</priority>
  </url>
  @endforeach
</urlset>
