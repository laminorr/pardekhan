@extends('panel.layouts.app')
@section('title', 'داشبورد')

@section('content')
<div class="panel-card">
    <div style="display:flex;align-items:center;gap:1rem;margin-bottom:1.5rem;">
        <div style="width:50px;height:50px;background:#f59e0b;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:1.3rem;font-weight:bold;color:#000;">
            {{ mb_substr($member->first_name, 0, 1) }}
        </div>
        <div>
            <div style="font-weight:bold;color:#fff;">{{ $member->full_name }}</div>
            <div style="font-size:0.8rem;color:#888;">
                {{ $member->layer?->name ?? 'لایه ۱' }} · امتیاز: {{ $member->score }}
            </div>
        </div>
    </div>
    <hr style="border-color:#2a2a2a;margin-bottom:1.5rem;">
    <p style="color:#aaa;text-align:center;">به پرده‌خوان خوش آمدید 🎬</p>
    <p style="color:#666;text-align:center;font-size:0.85rem;margin-top:0.5rem;">امکانات بیشتر به زودی اضافه می‌شوند</p>
</div>

<form method="POST" action="{{ route('panel.logout') }}" style="margin-top:1rem;">
    @csrf
    <button type="submit" class="btn btn-secondary">خروج</button>
</form>
@endsection
