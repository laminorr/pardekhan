@extends('panel.layouts.app')
@section('title', 'باشگاه کتاب‌خوانی')

@push('styles')
<style>
    /* ── باشگاه کتاب‌خوانی (اسکوپ‌شده) ── */
    .bookclub-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 0.7rem;
    }
    .bookclub-item {
        display: block;
        border: none;
        padding: 0;
        margin: 0;
        background: transparent;
        cursor: pointer;
        border-radius: 12px;
        overflow: hidden;
        aspect-ratio: 2 / 3;
        box-shadow: 0 4px 14px rgba(40,60,50,0.10);
        transition: transform 0.18s, box-shadow 0.18s;
        -webkit-tap-highlight-color: transparent;
    }
    .bookclub-item:active { transform: scale(0.97); }
    .bookclub-item img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        background: var(--green-soft);
    }
    /* ── حلقهٔ نازک دورِ کتاب‌های دارای دورهمی ── */
    /* دورهمی پیش‌ِرو: سبز فسفری ظریف */
    .bookclub-item--upcoming {
        box-shadow: 0 0 0 2px #4ade80, 0 4px 14px rgba(40,60,50,0.10);
    }
    /* دورهمی گذشته: سبز کاجِ مرده (برند) */
    .bookclub-item--past {
        box-shadow: 0 0 0 2px var(--pine), 0 4px 14px rgba(40,60,50,0.10);
    }

    /* ── دکمهٔ دورهمیِ مرتبط در مودال ── */
    .bookclub-modal__gathering {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        font-size: 0.82rem;
        font-weight: 700;
        color: #fff;
        text-decoration: none;
        background: var(--pine);
        border: 1px solid rgba(255,255,255,0.22);
        padding: 0.55rem 1.15rem;
        border-radius: 99px;
        transition: filter 0.18s;
    }
    .bookclub-modal__gathering:active { filter: brightness(1.15); }
    .bookclub-modal__actions {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        justify-content: center;
        gap: 0.6rem;
    }

    /* ── مودال (پاپ‌آپ) ── */
    .bookclub-modal {
        position: fixed;
        inset: 0;
        z-index: 100;
        display: none;
        align-items: center;
        justify-content: center;
        padding: 1.4rem;
        background: rgba(16,20,18,0.72);
        backdrop-filter: blur(4px);
    }
    .bookclub-modal.open { display: flex; animation: bcFade 0.2s ease; }
    @keyframes bcFade { from { opacity: 0; } to { opacity: 1; } }
    .bookclub-modal__inner {
        position: relative;
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 0.9rem;
        max-width: 320px;
        width: 100%;
        animation: bcPop 0.24s cubic-bezier(.5,1.4,.5,1);
    }
    @keyframes bcPop { from { transform: scale(0.9); opacity: 0; } to { transform: scale(1); opacity: 1; } }
    .bookclub-modal__img {
        width: 100%;
        max-height: 68vh;
        border-radius: 16px;
        object-fit: contain;
        box-shadow: 0 24px 60px -20px rgba(0,0,0,0.7);
    }
    .bookclub-modal__buy {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        font-size: 0.82rem;
        font-weight: 700;
        color: #fff;
        text-decoration: none;
        background: rgba(255,255,255,0.14);
        border: 1px solid rgba(255,255,255,0.22);
        padding: 0.55rem 1.15rem;
        border-radius: 99px;
        backdrop-filter: blur(6px);
        transition: background 0.18s;
    }
    .bookclub-modal__buy:active { background: rgba(255,255,255,0.24); }
    .bookclub-modal__close {
        position: absolute;
        top: -14px;
        left: -6px;
        width: 36px;
        height: 36px;
        border-radius: 50%;
        border: none;
        background: rgba(255,255,255,0.16);
        color: #fff;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        backdrop-filter: blur(6px);
    }

    /* ── صفحه‌بندی زیبا و ظریف ── */
    .bookclub-pager nav { display: flex; align-items: center; justify-content: center; }
    .bookclub-pager svg[role="img"] { display: none; } /* حذف آیکون پیش‌فرض */
    .bookclub-pager ul {
        display: flex;
        list-style: none;
        gap: 0.4rem;
        padding: 0;
        margin: 0;
        align-items: center;
        flex-wrap: wrap;
        justify-content: center;
    }
    .bookclub-pager li { margin: 0; }
    .bookclub-pager a, .bookclub-pager span {
        display: flex;
        align-items: center;
        justify-content: center;
        min-width: 38px;
        height: 38px;
        padding: 0 0.5rem;
        border-radius: 12px;
        font-size: 0.85rem;
        font-weight: 700;
        text-decoration: none;
        border: 1px solid var(--border);
        background: var(--surface);
        color: var(--ink-mid);
    }
    .bookclub-pager a:hover { border-color: var(--pine); color: var(--pine); }
    .bookclub-pager [aria-current="page"] span {
        background: var(--pine);
        border-color: var(--pine);
        color: #fff;
    }
    .bookclub-pager [aria-disabled="true"] span {
        opacity: 0.4;
        background: var(--bg-mute);
    }
</style>
@endpush

@section('content')
{{-- هدر --}}
<div style="display:flex;align-items:flex-start;gap:0.75rem;margin-bottom:1.3rem;">
    <a href="{{ route('panel.dashboard') }}" class="icon-btn" style="flex-shrink:0;margin-top:2px;">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 6l6 6-6 6"/></svg>
    </a>
    <div style="flex:1;">
        <div style="font-size:1.4rem;font-weight:800;letter-spacing:-0.5px;">باشگاه کتاب‌خوانی</div>
        <div style="font-size:0.78rem;color:var(--ink-faint);">کتاب‌های خوب را باهم بخوانیم</div>
    </div>
</div>

@if($books->isEmpty())
    <div style="text-align:center;padding:3rem 1rem;color:var(--ink-dim);">
        <div style="width:64px;height:64px;border-radius:20px;background:var(--green-soft);display:flex;align-items:center;justify-content:center;margin:0 auto 1rem;">
            <svg width="30" height="30" viewBox="0 0 24 24" fill="none" stroke="var(--pine)" stroke-width="1.5"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/></svg>
        </div>
        <div style="font-size:0.95rem;">هنوز کتابی معرفی نشده است</div>
    </div>
@else
    <div class="bookclub-grid">
        @foreach($books as $book)
            @php
                $ev = $book->event;
                // حلقه: پیش‌ِرو (>= اکنون) سبز فسفری، گذشته سبز کاج، بدون دورهمی بدون حلقه
                $ringClass = '';
                if ($ev) {
                    $ringClass = ($ev->starts_at && $ev->starts_at->gte(now()))
                        ? 'bookclub-item--upcoming'
                        : 'bookclub-item--past';
                }
            @endphp
        <button type="button" class="bookclub-item {{ $ringClass }}"
            data-cover="{{ $book->cover_src }}"
            data-buy="{{ $book->buy_url }}"
            data-event="{{ $ev?->id }}">
            <img src="{{ $book->cover_src }}" alt="" loading="lazy">
        </button>
        @endforeach
    </div>

    @if($books->hasPages())
        <div class="bookclub-pager" style="margin-top:1.6rem;">{{ $books->onEachSide(1)->links() }}</div>
    @endif
@endif

{{-- مودال مشترک برای همهٔ کتاب‌ها --}}
<div class="bookclub-modal" id="bookModal">
    <div class="bookclub-modal__inner">
        <button type="button" class="bookclub-modal__close" id="bookModalClose" aria-label="بستن">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6L6 18M6 6l12 12"/></svg>
        </button>
        <img src="" alt="" class="bookclub-modal__img" id="bookModalImg">
        <div class="bookclub-modal__actions">
            <a href="#" target="_blank" rel="noopener" class="bookclub-modal__buy" id="bookModalBuy" style="display:none;">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"><path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"/><path d="M3 6h18M16 10a4 4 0 0 1-8 0"/></svg>
                خرید از دیجی‌کالا
            </a>
            <a href="#" class="bookclub-modal__gathering" id="bookModalGathering" style="display:none;">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                دورهمی مربوط به این کتاب
            </a>
        </div>
    </div>
</div>

<div style="height:1rem;"></div>
@endsection

@section('nav')
    @include('panel.partials.bottom-nav', ['active' => 'home'])
@endsection

@push('scripts')
<script>
(function () {
    var modal = document.getElementById('bookModal');
    var modalImg = document.getElementById('bookModalImg');
    var modalBuy = document.getElementById('bookModalBuy');
    var modalGathering = document.getElementById('bookModalGathering');
    var closeBtn = document.getElementById('bookModalClose');
    if (!modal) return;

    // آدرس صفحهٔ دورهمی — جای‌گزینِ شناسه با آدرس واقعی پُر می‌شود
    var eventUrlBase = "{{ route('panel.events.show', ['event' => '__EVENT_ID__']) }}";

    function openModal(cover, buy, eventId) {
        modalImg.src = cover || '';
        if (buy) {
            modalBuy.href = buy;
            modalBuy.style.display = 'inline-flex';
        } else {
            modalBuy.style.display = 'none';
            modalBuy.removeAttribute('href');
        }
        if (eventId) {
            modalGathering.href = eventUrlBase.replace('__EVENT_ID__', eventId);
            modalGathering.style.display = 'inline-flex';
        } else {
            modalGathering.style.display = 'none';
            modalGathering.removeAttribute('href');
        }
        modal.classList.add('open');
    }

    function closeModal() {
        modal.classList.remove('open');
        modalImg.src = '';
    }

    document.querySelectorAll('.bookclub-item').forEach(function (item) {
        item.addEventListener('click', function () {
            openModal(
                item.getAttribute('data-cover'),
                item.getAttribute('data-buy'),
                item.getAttribute('data-event')
            );
        });
    });

    closeBtn.addEventListener('click', closeModal);
    modal.addEventListener('click', function (e) {
        // بستن با کلیک روی پس‌زمینه (نه روی محتوای مودال)
        if (e.target === modal) closeModal();
    });
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape' && modal.classList.contains('open')) closeModal();
    });
})();
</script>
@endpush
