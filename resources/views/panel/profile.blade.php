@extends('panel.layouts.app')
@section('title', 'پروفایل')

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@majidh1/jalalidatepicker@1.0.0/dist/jalalidatepicker.min.css">
<style>
    .menu-row { display:flex; align-items:center; gap:0.85rem; padding:0.95rem 1rem; cursor:pointer; background:none; border:none; width:100%; font-family:inherit; text-align:right; text-decoration:none; color:inherit; }
    .menu-row:not(:last-child) { border-bottom:1px solid #f3f4f3; }
    .menu-ico { width:40px; height:40px; border-radius:12px; background:var(--bg-soft); display:flex; align-items:center; justify-content:center; flex-shrink:0; }
    .menu-row .label { flex:1; font-size:0.9rem; font-weight:700; }
    .collapse { max-height:0; opacity:0; overflow:hidden; transition:max-height 0.4s ease, opacity 0.3s; }
    .collapse.open { max-height:1200px; opacity:1; }
    /* هماهنگ‌سازی تقویم شمسی (JalaliDatePicker) با تم سبز */
    jdp-container { --jdp-primary: var(--pine); font-family:'Vazirmatn',sans-serif !important; direction:rtl; }
    jdp-container .jdp-day:not(.disabled-day):hover { background:var(--green-soft) !important; color:var(--pine) !important; }
</style>
@endpush

@section('content')
@if (session('success'))
    <div class="alert alert-success" style="margin-bottom:1rem;">{{ session('success') }}</div>
@endif
@if ($errors->any())
    <div class="alert alert-danger" style="margin-bottom:1rem;">@foreach ($errors->all() as $e)<div>{{ $e }}</div>@endforeach</div>
@endif

{{-- نمایه --}}
<div style="display:flex;flex-direction:column;align-items:center;padding-top:1rem;">
    <div style="width:96px;height:96px;border-radius:32px;background:linear-gradient(135deg,var(--pine),var(--pine-bright));display:flex;align-items:center;justify-content:center;font-size:2.4rem;font-weight:800;color:#fff;box-shadow:0 16px 32px -14px rgba(47,93,80,0.6);overflow:hidden;">
        @if($member->avatar && $member->avatar_approved)
            <img src="{{ Storage::url($member->avatar) }}" style="width:100%;height:100%;object-fit:cover;">
        @else
            {{ mb_substr($member->first_name, 0, 1) }}
        @endif
    </div>
    <div style="font-size:1.4rem;font-weight:800;margin-top:1rem;letter-spacing:-0.4px;">{{ $member->full_name }}</div>
    <div style="font-size:0.82rem;color:var(--ink-dim);margin-top:3px;">{{ $member->phone }}</div>
    <div style="margin-top:0.85rem;display:inline-flex;align-items:center;gap:8px;background:var(--green-soft);color:var(--pine);font-size:0.78rem;font-weight:800;padding:8px 16px;border-radius:20px;">
        <svg width="15" height="15" viewBox="0 0 24 24" fill="var(--pine)"><path d="M12 2l2.5 6.5L21 9l-5 4.5L17.5 21 12 17l-5.5 4L8 13.5 3 9l6.5-.5z"/></svg>
        لایهٔ {{ $member->layer?->name ?? 'عضو' }} · {{ fa(number_format($member->score)) }} امتیاز
    </div>
</div>

{{-- منو --}}
<div style="margin-top:1.5rem;border:1px solid var(--border);border-radius:20px;overflow:hidden;background:#fff;">
    {{-- ویرایش پروفایل --}}
    <button class="menu-row" onclick="toggleSection('edit')">
        <div class="menu-ico"><svg width="19" height="19" viewBox="0 0 24 24" fill="none" stroke="var(--pine)" stroke-width="1.6"><circle cx="12" cy="8" r="3.5"/><path d="M5.5 20a6.5 6.5 0 0 1 13 0"/></svg></div>
        <span class="label">ویرایش پروفایل</span>
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#c0c4c1" stroke-width="2" stroke-linecap="round"><path d="M9 6l6 6-6 6"/></svg>
    </button>

    {{-- دورهمی‌های من --}}
    <a href="{{ route('panel.events.my') }}" class="menu-row">
        <div class="menu-ico"><svg width="19" height="19" viewBox="0 0 24 24" fill="none" stroke="var(--pine)" stroke-width="1.6"><rect x="4" y="6" width="16" height="15" rx="2.5"/><path d="M4 10h16M8 3v4M16 3v4"/></svg></div>
        <span class="label">دورهمی‌های من</span>
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#c0c4c1" stroke-width="2" stroke-linecap="round"><path d="M9 6l6 6-6 6"/></svg>
    </a>

    {{-- تغییر رمز --}}
    <button class="menu-row" onclick="toggleSection('password')">
        <div class="menu-ico"><svg width="19" height="19" viewBox="0 0 24 24" fill="none" stroke="var(--pine)" stroke-width="1.6"><rect x="5" y="11" width="14" height="10" rx="2"/><path d="M8 11V7a4 4 0 0 1 8 0v4"/></svg></div>
        <span class="label">تغییر رمز عبور</span>
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#c0c4c1" stroke-width="2" stroke-linecap="round"><path d="M9 6l6 6-6 6"/></svg>
    </button>
</div>

{{-- فرم ویرایش (جمع‌شونده) --}}
<div class="collapse" id="section-edit">
    <form method="POST" action="{{ route('panel.profile') }}" enctype="multipart/form-data" style="margin-top:1rem;border:1px solid var(--border);border-radius:20px;padding:1.25rem;background:#fff;">
        @csrf
        <div class="field"><label>عکس پروفایل</label>
            <input type="file" name="avatar" accept="image/*">
            @if($member->avatar && !$member->avatar_approved)
                <div style="font-size:0.74rem;color:var(--burnt);margin-top:4px;">عکس در انتظار تایید مدیریت</div>
            @endif
        </div>
        <div class="field"><label>شهر</label><input type="text" name="city" value="{{ old('city', $member->city) }}" placeholder="مثلاً تهران"></div>
        <div class="field"><label>شغل</label><input type="text" name="job" value="{{ old('job', $member->job) }}" placeholder="شغل یا حوزه فعالیت"></div>
        <div class="field"><label>تحصیلات</label><input type="text" name="education" value="{{ old('education', $member->education) }}" placeholder="میزان تحصیلات"></div>
        <div class="field"><label>تاریخ تولد</label>
            <input type="text" id="birth_date_display" data-jdp data-jdp-max-date="today" readonly placeholder="انتخاب تاریخ تولد" autocomplete="off"
                value="{{ $member->birth_date ? \Morilog\Jalali\Jalalian::fromDateTime($member->birth_date)->format('Y/m/d') : '' }}"
                style="direction:rtl;text-align:right;cursor:pointer;background:var(--surface);">
            <input type="hidden" name="birth_date" id="birth_date_value" value="{{ old('birth_date', $member->birth_date?->format('Y-m-d')) }}">
        </div>
        <div class="field"><label>معرفی کوتاه</label><textarea name="bio" rows="3" style="width:100%;background:var(--surface);border:1px solid var(--border);border-radius:14px;padding:0.9rem 1rem;color:var(--ink);font-family:inherit;resize:vertical;" placeholder="چند جمله درباره خودتان...">{{ old('bio', $member->bio) }}</textarea></div>
        @if(!$member->profile_completed)
            <p style="color:var(--pine);font-size:0.8rem;margin-bottom:1rem;">با تکمیل پروفایل (شهر و شغل) امتیاز دریافت می‌کنید</p>
        @endif
        <button type="submit" class="btn btn-primary">ذخیره تغییرات</button>
    </form>
</div>

{{-- فرم تغییر رمز (جمع‌شونده) --}}
<div class="collapse" id="section-password">
    <form method="POST" action="{{ route('panel.profile.password') }}" style="margin-top:1rem;border:1px solid var(--border);border-radius:20px;padding:1.25rem;background:#fff;">
        @csrf
        <div class="field"><label>رمز عبور جدید</label><input type="password" name="new_password" required placeholder="حداقل ۶ کاراکتر"></div>
        <div class="field"><label>تکرار رمز جدید</label><input type="password" name="new_password_confirmation" required placeholder="رمز را دوباره وارد کنید"></div>
        <button type="submit" class="btn btn-primary">تغییر رمز عبور</button>
    </form>
</div>

{{-- کارت recap لایه --}}
<div style="margin-top:1.25rem;border-radius:20px;padding:1.3rem;background:linear-gradient(140deg,var(--pine),#1f4538);color:var(--green-tint);position:relative;overflow:hidden;">
    <div style="position:absolute;top:-30px;left:-20px;width:110px;height:110px;border-radius:50%;background:rgba(255,255,255,0.06);"></div>
    <div style="position:relative;font-size:0.78rem;color:#a7ccc0;">امتیاز شما</div>
    <div style="position:relative;font-size:1.8rem;font-weight:800;margin-top:4px;">{{ fa(number_format($member->score)) }}</div>
    @php
        $nextLayer = \App\Models\Layer::where('is_active',true)->where('min_score','>',$member->score)->orderBy('min_score')->first();
    @endphp
    @if($nextLayer)
        <div style="position:relative;font-size:0.78rem;color:#a7ccc0;margin-top:6px;">{{ fa(number_format($nextLayer->min_score - $member->score)) }} امتیاز تا لایهٔ {{ $nextLayer->name }}</div>
    @else
        <div style="position:relative;font-size:0.78rem;color:#a7ccc0;margin-top:6px;">به بالاترین لایه رسیده‌اید 🎉</div>
    @endif
</div>

{{-- خروج --}}
<form method="POST" action="{{ route('panel.logout') }}" style="margin-top:1rem;">
    @csrf
    <button type="submit" style="width:100%;padding:0.95rem;border-radius:15px;background:#fff;border:1px solid #f0d8d3;color:var(--burnt);font-family:inherit;font-size:0.92rem;font-weight:700;cursor:pointer;display:flex;align-items:center;justify-content:center;gap:8px;">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4M16 17l5-5-5-5M21 12H9"/></svg>
        خروج از حساب
    </button>
</form>

<div style="height:1rem;"></div>
@endsection

@section('nav')
    @include('panel.partials.bottom-nav', ['active' => 'profile'])
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/@majidh1/jalalidatepicker@1.0.0/dist/jalalidatepicker.min.js"></script>
<script>
    function toggleSection(id) {
        var el = document.getElementById('section-' + id);
        el.classList.toggle('open');
        if (el.classList.contains('open')) {
            setTimeout(function(){ el.scrollIntoView({behavior:'smooth', block:'nearest'}); }, 100);
        }
    }

    // راه‌اندازی تقویم شمسی برای تاریخ تولد
    (function () {
        // --- تبدیل تاریخ جلالی به میلادی (jalaali-js، مجوز MIT) ---
        function div(a, b) { return ~~(a / b); }
        function mod(a, b) { return a - ~~(a / b) * b; }
        var BREAKS = [-61, 9, 38, 199, 426, 686, 756, 818, 1111, 1181, 1210, 1635, 2060, 2097, 2192, 2262, 2324, 2394, 2456, 3178];
        function jalCal(jy) {
            var bl = BREAKS.length, gy = jy + 621, leapJ = -14, jp = BREAKS[0],
                jm, jump, leapG, march, n, i;
            if (jy < jp || jy >= BREAKS[bl - 1]) throw new Error('Invalid Jalaali year ' + jy);
            for (i = 1; i < bl; i += 1) {
                jm = BREAKS[i];
                jump = jm - jp;
                if (jy < jm) break;
                leapJ = leapJ + div(jump, 33) * 8 + div(mod(jump, 33), 4);
                jp = jm;
            }
            n = jy - jp;
            leapJ = leapJ + div(n, 33) * 8 + div(mod(n, 33) + 3, 4);
            if (mod(jump, 33) === 4 && jump - n === 4) leapJ += 1;
            leapG = div(gy, 4) - div((div(gy, 100) + 1) * 3, 4) - 150;
            march = 20 + leapJ - leapG;
            return { gy: gy, march: march };
        }
        function g2d(gy, gm, gd) {
            var d = div((gy + div(gm - 8, 6) + 100100) * 1461, 4)
                  + div(153 * mod(gm + 9, 12) + 2, 5)
                  + gd - 34840408;
            d = d - div(div(gy + 100100 + div(gm - 8, 6), 100) * 3, 4) + 752;
            return d;
        }
        function d2g(jdn) {
            var j = 4 * jdn + 139361631;
            j = j + div(div(4 * jdn + 183187720, 146097) * 3, 4) * 4 - 3908;
            var i = div(mod(j, 1461), 4) * 5 + 308;
            var gd = div(mod(i, 153), 5) + 1;
            var gm = mod(div(i, 153), 12) + 1;
            var gy = div(j, 1461) - 100100 + div(8 - gm, 6);
            return { gy: gy, gm: gm, gd: gd };
        }
        function j2d(jy, jm, jd) {
            var r = jalCal(jy);
            return g2d(r.gy, 3, r.march) + (jm - 1) * 31 - div(jm, 7) * (jm - 7) + jd - 1;
        }
        function jalaliToGregorian(jy, jm, jd) { return d2g(j2d(jy, jm, jd)); }

        function normalizeDigits(s) {
            // ارقام فارسی و عربی را به لاتین تبدیل کن
            return String(s)
                .replace(/[۰-۹]/g, function (c) { return c.charCodeAt(0) - 0x06F0; })
                .replace(/[٠-٩]/g, function (c) { return c.charCodeAt(0) - 0x0660; });
        }
        function pad2(n) { return (n < 10 ? '0' : '') + n; }

        var display = document.getElementById('birth_date_display');
        var hidden  = document.getElementById('birth_date_value');

        // راه‌اندازی تقویم؛ تاریخ آینده قابل انتخاب نیست
        if (window.jalaliDatepicker) {
            jalaliDatepicker.startWatch({
                time: false,
                persianDigits: false,
                autoShow: true,
                autoHide: true,
                hideAfterChange: true,
                maxDate: 'today',
                minDate: { year: 1300, month: 1, day: 1 },
                separatorChars: { date: '/' }
            });
        }

        // با هر انتخاب/پاک‌کردن، مقدار میلادی معتبر را در فیلد مخفی بنویس
        if (display && hidden) {
            display.addEventListener('change', function () {
                var raw = normalizeDigits((display.value || '').trim());
                var m = raw.match(/^(\d{4})\/(\d{1,2})\/(\d{1,2})$/);
                if (!m) { hidden.value = ''; return; }
                var jy = +m[1], jm = +m[2], jd = +m[3];
                try {
                    var g = jalaliToGregorian(jy, jm, jd);
                    hidden.value = g.gy + '-' + pad2(g.gm) + '-' + pad2(g.gd);
                } catch (e) {
                    hidden.value = '';
                }
            });
        }
    })();
</script>
@endpush
