@extends('layouts.app')

@section('title', 'نقد تئاتر «'.$review->title.'» | پرده‌خوان')
@section('description', \Illuminate\Support\Str::limit(trim(strip_tags($review->body)), 150) ?: 'نقد و تحلیل تئاتر در پرده‌خوان')
@section('og_image', $review->cover ? asset('storage/'.$review->cover) : '')
@section('canonical', route('theater.show', $review))
@section('robots', $review->is_published ? 'index, follow' : 'noindex, nofollow')

@section('nav-links')
<div class="n-links">
  <a href="{{ route('home') }}">خانه</a>
  <a href="{{ route('topics.index') }}">پرونده‌ها</a>
  <a href="{{ route('episodes.index') }}">آرشیو</a>
</div>
@endsection

@section('content')

{{-- استایل اختصاصی صفحه نقد تئاتر — فقط داخل .tr-* و .pk-prose (بدون نشت به بقیه اپ) --}}
<style>
  .tr-wrap{max-width:760px;margin:0 auto;padding:96px 20px 40px}
  .tr-badge{display:inline-flex;align-items:center;gap:6px;font-size:0.8rem;font-weight:700;color:#0d9488;background:#f0fdfa;border:0.5px solid rgba(13,148,136,0.2);border-radius:999px;padding:5px 12px;margin-bottom:16px}
  .tr-title{font-size:1.9rem;font-weight:900;line-height:1.5;color:#0f172a;margin-bottom:20px}
  .tr-cover{border-radius:16px;overflow:hidden;margin:0 0 28px;border:0.5px solid rgba(0,0,0,0.06);background:linear-gradient(160deg,#115e59,#0d9488,#5eead4)}
  .tr-cover img{width:100%;height:auto;display:block}
  .tr-divider{height:2px;width:56px;background:linear-gradient(90deg,#0d9488,#5eead4);border-radius:2px;margin:0 0 24px}

  /* متن نقد (HTML ذخیره‌شده از ویرایشگر، پاک‌سازی‌شده) */
  .pk-prose{font-size:1rem;color:#1a1e28;line-height:1.85;text-align:justify;word-break:break-word;overflow-wrap:anywhere}
  .pk-prose > :first-child{margin-top:0}
  .pk-prose p{margin:0 0 1.1rem}
  .pk-prose h2,.pk-prose h3,.pk-prose h4{color:#0f172a;font-weight:800;line-height:1.5;margin:2rem 0 0.75rem;text-align:right}
  .pk-prose h2{font-size:1.32rem}
  .pk-prose h3{font-size:1.14rem}
  .pk-prose h4{font-size:1rem}
  .pk-prose ul,.pk-prose ol{margin:0 0 1.1rem;padding-inline-start:1.6rem}
  .pk-prose ul{list-style:disc}
  .pk-prose ol{list-style:decimal}
  .pk-prose li{margin:0.35rem 0;padding-inline-start:0.2rem}
  .pk-prose li::marker{color:#0d9488}
  .pk-prose a{color:#0d9488;text-decoration:underline;text-underline-offset:3px;font-weight:600;word-break:break-word}
  .pk-prose blockquote{margin:1.3rem 0;padding:0.9rem 1.2rem;border-inline-start:3px solid #0d9488;background:#f0fdfa;border-radius:0 12px 12px 0;color:#334155}
  .pk-prose blockquote p:last-child{margin-bottom:0}
  .pk-prose img{max-width:100%;height:auto;border-radius:14px;margin:1.2rem 0;display:block}
  .pk-prose strong,.pk-prose b{font-weight:800;color:#0f172a}
  .pk-prose hr{border:none;border-top:1px solid rgba(0,0,0,0.08);margin:1.8rem 0}
  @media(max-width:640px){.tr-wrap{padding:88px 16px 32px}.tr-title{font-size:1.5rem}}
</style>

<article class="tr-wrap">
  <div class="tr-badge">
    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
      <path d="M2 12s2.5-7 10-7 10 7 10 7"/><path d="M6 12a2 2 0 0 0 4 0"/><path d="M14 12a2 2 0 0 0 4 0"/>
    </svg>
    نقد تئاتر
  </div>

  <h1 class="tr-title rv">{{ $review->title }}</h1>
  <div class="tr-divider"></div>

  @if($review->cover)
  <div class="tr-cover rv">
    <img src="{{ asset('storage/'.$review->cover) }}" alt="{{ $review->title }}">
  </div>
  @endif

  {{-- متن نقد — همیشه با clean() پاک‌سازی می‌شود تا از XSS جلوگیری شود --}}
  <div class="pk-prose rv">
    {!! clean($review->body) !!}
  </div>
</article>

@endsection
