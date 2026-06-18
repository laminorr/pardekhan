@extends('panel.layouts.app')
@section('title', 'پروفایل')

@section('content')
<div class="panel-card">
    <h2>پروفایل من</h2>

    @if (session('success'))
        <div class="success-msg">{{ session('success') }}</div>
    @endif

    @if ($errors->any())
        <div style="color:#ef4444;background:#1f0000;border:1px solid #7f1d1d;border-radius:8px;padding:0.75rem 1rem;margin-bottom:1rem;font-size:0.85rem;">
            @foreach ($errors->all() as $error)
                <div>{{ $error }}</div>
            @endforeach
        </div>
    @endif

    {{-- وضعیت --}}
    <div style="display:flex;align-items:center;gap:1rem;margin-bottom:1.5rem;padding-bottom:1.5rem;border-bottom:1px solid #2a2a2a;">
        <div style="width:60px;height:60px;border-radius:50%;overflow:hidden;background:#f59e0b;display:flex;align-items:center;justify-content:center;font-size:1.5rem;font-weight:bold;color:#000;">
            @if($member->avatar && $member->avatar_approved)
                <img src="{{ Storage::url($member->avatar) }}" style="width:100%;height:100%;object-fit:cover;">
            @else
                {{ mb_substr($member->first_name, 0, 1) }}
            @endif
        </div>
        <div>
            <div style="font-weight:bold;color:#fff;">{{ $member->full_name }}</div>
            <div style="font-size:0.8rem;color:#888;">{{ $member->layer?->name ?? 'لایه ۱' }} · امتیاز: {{ $member->score }}</div>
        </div>
    </div>

    <form method="POST" action="{{ route('panel.profile') }}" enctype="multipart/form-data">
        @csrf

        <div class="form-group">
            <label>عکس پروفایل</label>
            @if($member->avatar && !$member->avatar_approved)
                <p style="color:#f59e0b;font-size:0.8rem;margin-bottom:0.5rem;">⏳ عکس شما در انتظار تایید است</p>
            @endif
            <input type="file" name="avatar" accept="image/jpeg,image/png" style="padding:0.5rem;">
            <p style="color:#666;font-size:0.75rem;margin-top:0.3rem;">حداکثر ۲ مگابایت، فرمت jpg یا png</p>
        </div>

        <div class="form-group">
            <label>شهر</label>
            <input type="text" name="city" value="{{ old('city', $member->city) }}" placeholder="مثلاً تهران">
        </div>

        <div class="form-group">
            <label>شغل</label>
            <input type="text" name="job" value="{{ old('job', $member->job) }}" placeholder="شغل یا حوزه فعالیت">
        </div>

        <div class="form-group">
            <label>تحصیلات</label>
            <input type="text" name="education" value="{{ old('education', $member->education) }}" placeholder="میزان تحصیلات">
        </div>

        <div class="form-group">
            <label>تاریخ تولد</label>
            <input type="date" name="birth_date" value="{{ old('birth_date', $member->birth_date?->format('Y-m-d')) }}" style="direction:ltr;">
        </div>

        <div class="form-group">
            <label>معرفی کوتاه</label>
            <textarea name="bio" rows="3" style="width:100%;background:#111;border:1px solid #333;border-radius:10px;padding:0.75rem 1rem;color:#fff;font-family:inherit;resize:vertical;" placeholder="چند جمله درباره خودتان...">{{ old('bio', $member->bio) }}</textarea>
        </div>

        @if(!$member->profile_completed)
            <p style="color:#f59e0b;font-size:0.8rem;margin-bottom:1rem;">💡 با تکمیل پروفایل (شهر و شغل) امتیاز دریافت می‌کنید</p>
        @endif

        <button type="submit" class="btn btn-primary">ذخیره تغییرات</button>
    </form>
</div>

<div style="margin-top:1rem;display:flex;gap:0.75rem;">
    <a href="{{ route('panel.dashboard') }}" style="flex:1;display:block;text-align:center;padding:0.85rem;background:#2a2a2a;color:#ddd;border-radius:10px;text-decoration:none;">بازگشت</a>
</div>
@endsection
