@extends('panel.layouts.app')
@section('title', 'پروفایل')

@section('content')
<div class="page-head" style="display:flex;align-items:center;gap:0.75rem;">
    <a href="{{ route('panel.dashboard') }}" class="icon-btn" style="flex-shrink:0;">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M9 18l6-6-6-6"/></svg>
    </a>
    <div class="page-title" style="font-size:1.2rem;">پروفایل من</div>
</div>

@if (session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif
@if ($errors->any())
    <div class="alert alert-danger">
        @foreach ($errors->all() as $error)<div>{{ $error }}</div>@endforeach
    </div>
@endif

{{-- کارت نمایه --}}
<div class="card card-gold" style="display:flex;align-items:center;gap:1rem;">
    <div style="width:60px;height:60px;border-radius:18px;padding:2px;background:linear-gradient(140deg,var(--gold-1),var(--gold-3));">
        <div style="width:100%;height:100%;border-radius:16px;background:var(--surface);display:flex;align-items:center;justify-content:center;font-size:1.4rem;font-weight:800;color:var(--gold-1);overflow:hidden;">
            @if($member->avatar && $member->avatar_approved)
                <img src="{{ Storage::url($member->avatar) }}" style="width:100%;height:100%;object-fit:cover;">
            @else
                {{ mb_substr($member->first_name, 0, 1) }}
            @endif
        </div>
    </div>
    <div>
        <div style="font-weight:700;color:#fff;font-size:1.05rem;">{{ $member->full_name }}</div>
        <div style="font-size:0.8rem;color:var(--gold-1);margin-top:3px;">{{ $member->layer?->name ?? 'بدون لایه' }} · {{ number_format($member->score) }} امتیاز</div>
    </div>
</div>

<form method="POST" action="{{ route('panel.profile') }}" enctype="multipart/form-data">
    @csrf
    <div class="card">
        <div class="field">
            <label>عکس پروفایل</label>
            @if($member->avatar && !$member->avatar_approved)
                <p style="color:var(--gold-1);font-size:0.8rem;margin-bottom:0.5rem;">⏳ عکس شما در انتظار تایید است</p>
            @endif
            <input type="file" name="avatar" accept="image/jpeg,image/png" style="padding:0.6rem;">
            <p style="color:var(--text-faint);font-size:0.72rem;margin-top:0.3rem;">حداکثر ۲ مگابایت، فرمت jpg یا png</p>
        </div>
        <div class="field"><label>شهر</label><input type="text" name="city" value="{{ old('city', $member->city) }}" placeholder="مثلاً تهران"></div>
        <div class="field"><label>شغل</label><input type="text" name="job" value="{{ old('job', $member->job) }}" placeholder="شغل یا حوزه فعالیت"></div>
        <div class="field"><label>تحصیلات</label><input type="text" name="education" value="{{ old('education', $member->education) }}" placeholder="میزان تحصیلات"></div>
        <div class="field"><label>تاریخ تولد</label><input type="date" name="birth_date" value="{{ old('birth_date', $member->birth_date?->format('Y-m-d')) }}" style="direction:ltr;"></div>
        <div class="field"><label>معرفی کوتاه</label><textarea name="bio" rows="3" style="width:100%;background:#0d0d0f;border:1px solid var(--border);border-radius:13px;padding:0.85rem 1rem;color:var(--text);font-family:inherit;resize:vertical;" placeholder="چند جمله درباره خودتان...">{{ old('bio', $member->bio) }}</textarea></div>

        @if(!$member->profile_completed)
            <p style="color:var(--gold-1);font-size:0.8rem;margin-bottom:1rem;">با تکمیل پروفایل (شهر و شغل) امتیاز دریافت می‌کنید</p>
        @endif

        <button type="submit" class="btn btn-gold">ذخیره تغییرات</button>
    </div>
</form>
@endsection
