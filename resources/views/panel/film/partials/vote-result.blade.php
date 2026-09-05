{{--
    نتیجهٔ جمعیِ رأی‌گیری — گیتِ اثبات اجتماعی.
    ▪ اگر عضو رأی نداده باشد: هیچ چیزی نشان داده نمی‌شود (فقط دکمه‌ها).
    ▪ اگر رأی داده ولی total < threshold: خطِ «به‌زودی» بدون هیچ عددی.
    ▪ اگر رأی داده و total ≥ threshold: نوارِ افقی با درصد و شمارشِ فارسی.
--}}
@if($reveal)
    <div style="display:flex;align-items:center;justify-content:space-between;font-size:0.8rem;font-weight:800;margin-bottom:0.4rem;">
        <span style="color:var(--pine);">٪{{ fa($willWatchPct) }} می‌بینند</span>
        <span style="color:var(--ink-dim,#8a8a80);">٪{{ fa(100 - $willWatchPct) }} نمی‌بینند</span>
    </div>
    <div style="display:flex;height:12px;border-radius:99px;overflow:hidden;background:var(--line,#e7e2d8);">
        <div style="width:{{ $willWatchPct }}%;background:var(--pine);"></div>
    </div>
    <div style="font-size:0.72rem;color:var(--ink-dim,#8a8a80);margin-top:0.45rem;">
        مجموع {{ fa($total) }} رأی
    </div>
@elseif($myDecision !== null)
    <div style="font-size:0.78rem;color:var(--ink-dim,#8a8a80);line-height:1.9;">
        نتیجهٔ جمعی به‌زودی (بعد از رأی چند نفر دیگر)…
    </div>
@endif
