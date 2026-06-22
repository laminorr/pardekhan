@extends('panel.layouts.app')
@section('title', 'بازخورد')

@section('content')
<div class="page-head" style="display:flex;align-items:center;gap:0.75rem;">
    <a href="{{ route('panel.events.my') }}" class="icon-btn" style="flex-shrink:0;">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M9 18l6-6-6-6"/></svg>
    </a>
    <div class="page-title" style="font-size:1.2rem;">بازخورد دورهمی</div>
</div>

@if ($errors->any())
    <div class="alert alert-danger">@foreach ($errors->all() as $e)<div>{{ $e }}</div>@endforeach</div>
@endif

<div class="card">
    <div style="font-weight:700;color:var(--ink);">{{ $event->title }}</div>
    <div style="font-size:0.8rem;color:var(--ink-dim);margin-top:4px;">{{ \Morilog\Jalali\Jalalian::fromDateTime($event->starts_at)->format('Y/m/d') }}</div>
</div>

<form method="POST" action="{{ route('panel.feedback.store', $event) }}">
    @csrf
    <div class="card">
        <div style="text-align:center;margin-bottom:1.5rem;">
            <div style="font-size:0.9rem;color:var(--ink-dim);margin-bottom:1rem;">تجربه شما از این دورهمی چطور بود؟</div>

            {{-- ستاره‌ها --}}
            <div id="stars" style="display:flex;justify-content:center;gap:0.5rem;direction:ltr;">
                @for($i = 1; $i <= 5; $i++)
                <button type="button" class="star-btn" data-value="{{ $i }}"
                    style="background:none;border:none;cursor:pointer;padding:0;">
                    <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="var(--pine-bright)" stroke-width="1.5" class="star-icon">
                        <path d="M12 2l2.6 6.6L21 9.2l-5 4.5 1.5 7L12 17l-5.5 3.7L8 13.7l-5-4.5 6.4-.6z"/>
                    </svg>
                </button>
                @endfor
            </div>
            <input type="hidden" name="rating" id="rating-input" value="">
            <div id="rating-label" style="font-size:0.85rem;color:var(--pine);margin-top:0.75rem;min-height:1.2rem;"></div>
        </div>

        <div class="field">
            <label>نظر شما (اختیاری)</label>
            <textarea name="comment" rows="4" placeholder="تجربه‌تان را بنویسید..."
                style="width:100%;background:var(--surface);border:1px solid var(--border);border-radius:13px;padding:0.85rem 1rem;color:var(--ink);font-family:inherit;resize:vertical;">{{ old('comment') }}</textarea>
        </div>

        <button type="submit" class="btn btn-primary">ثبت بازخورد</button>
    </div>
</form>

@push('scripts')
<script>
    const labels = {1:'خیلی بد',2:'بد',3:'متوسط',4:'خوب',5:'عالی'};
    const stars = document.querySelectorAll('.star-btn');
    const input = document.getElementById('rating-input');
    const label = document.getElementById('rating-label');
    let selected = 0;

    function paint(val) {
        stars.forEach(s => {
            const v = parseInt(s.dataset.value);
            const icon = s.querySelector('.star-icon');
            if (v <= val) { icon.setAttribute('fill', 'var(--pine)'); icon.setAttribute('stroke', 'var(--pine)'); }
            else { icon.setAttribute('fill', 'none'); icon.setAttribute('stroke', 'var(--pine-bright)'); }
        });
    }
    stars.forEach(s => {
        s.addEventListener('click', () => {
            selected = parseInt(s.dataset.value);
            input.value = selected;
            label.textContent = labels[selected];
            paint(selected);
        });
        s.addEventListener('mouseenter', () => paint(parseInt(s.dataset.value)));
        s.addEventListener('mouseleave', () => paint(selected));
    });
</script>
@endpush
@endsection
