@extends('panel.layouts.app')
@section('title', $event->title)

@section('content')
<div class="page-head" style="display:flex;align-items:center;gap:0.75rem;">
    <a href="{{ route('panel.events.index') }}" class="icon-btn" style="flex-shrink:0;">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M9 18l6-6-6-6"/></svg>
    </a>
    <div class="page-title" style="font-size:1.2rem;">جزئیات دورهمی</div>
</div>

@if (session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif
@if (session('error'))
    <div class="alert alert-danger">{{ session('error') }}</div>
@endif

<div class="card" style="padding:0;overflow:hidden;">
    @if($event->image)
        <div style="height:200px;position:relative;">
            <img src="{{ Storage::url($event->image) }}" style="width:100%;height:100%;object-fit:cover;">
            <div style="position:absolute;inset:0;background:linear-gradient(to top,var(--surface) 0%,transparent 55%);"></div>
        </div>
    @endif
    <div style="padding:{{ $event->image ? '0' : '1.5rem' }} 1.5rem 1.5rem;{{ $event->image ? 'margin-top:-2rem;position:relative;z-index:1;' : '' }}">
        <h2 style="font-size:1.3rem;font-weight:700;color:#fff;">{{ $event->title }}</h2>
        @if($event->subtitle)
            <p style="font-size:0.85rem;color:var(--text-dim);margin-top:4px;">{{ $event->subtitle }}</p>
        @endif

        {{-- اطلاعات --}}
        <div style="display:flex;flex-direction:column;gap:0.75rem;margin:1.25rem 0;padding:1rem;background:#0d0d0f;border-radius:14px;border:1px solid var(--border);">
            <div style="display:flex;justify-content:space-between;font-size:0.85rem;">
                <span style="color:var(--text-dim);display:flex;align-items:center;gap:6px;">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="var(--gold-2)" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2"/><path d="M16 2v4M8 2v4M3 10h18"/></svg>
                    تاریخ
                </span>
                <span>{{ \Morilog\Jalali\Jalalian::fromDateTime($event->starts_at)->format('Y/m/d') }}</span>
            </div>
            <div style="display:flex;justify-content:space-between;font-size:0.85rem;">
                <span style="color:var(--text-dim);display:flex;align-items:center;gap:6px;">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="var(--gold-2)" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/></svg>
                    ساعت
                </span>
                <span>{{ $event->starts_at->format('H:i') }}</span>
            </div>
            @if($event->venue)
            <div style="display:flex;justify-content:space-between;font-size:0.85rem;">
                <span style="color:var(--text-dim);display:flex;align-items:center;gap:6px;">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="var(--gold-2)" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><path d="M21 10c0 7-9 12-9 12s-9-5-9-12a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
                    مکان
                </span>
                <span>{{ $event->venue->name }}</span>
            </div>
            <div style="font-size:0.8rem;color:var(--text-dim);padding-top:0.5rem;border-top:1px solid var(--border);">
                {{ $event->venue->address }}
            </div>
            @if($event->venue->map_link)
                <a href="{{ $event->venue->map_link }}" target="_blank" style="color:var(--gold-2);text-decoration:none;font-size:0.82rem;display:flex;align-items:center;gap:4px;">
                    مشاهده روی نقشه
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 18l6-6-6-6"/></svg>
                </a>
            @endif
            @endif
        </div>

        @if($event->description)
        <div style="color:#ccc;line-height:1.9;font-size:0.9rem;margin-bottom:1.25rem;">{!! nl2br(e($event->description)) !!}</div>
        @endif

        {{-- شرکت‌کنندگان --}}
        @if($attendeeAvatars->isNotEmpty())
        <div style="margin-bottom:1.25rem;">
            <div style="color:var(--text-dim);font-size:0.78rem;margin-bottom:0.6rem;">شرکت‌کنندگان</div>
            <div style="display:flex;">
                @foreach($attendeeAvatars as $i => $avatar)
                    <div style="width:38px;height:38px;border-radius:50%;overflow:hidden;border:2px solid var(--surface);{{ $i > 0 ? 'margin-right:-10px;' : '' }}">
                        <img src="{{ Storage::url($avatar->avatar) }}" style="width:100%;height:100%;object-fit:cover;">
                    </div>
                @endforeach
            </div>
        </div>
        @endif

        {{-- قیمت --}}
        <div style="background:#0d0d0f;border:1px solid var(--border);border-radius:14px;padding:1.1rem;margin-bottom:1.25rem;">
            @if($discount > 0)
                <div style="display:flex;justify-content:space-between;color:var(--text-dim);font-size:0.82rem;">
                    <span>قیمت پایه</span>
                    <span style="text-decoration:line-through;">{{ number_format($event->base_price) }} تومان</span>
                </div>
                <div style="display:flex;justify-content:space-between;color:var(--success);font-size:0.82rem;margin-top:0.4rem;">
                    <span>تخفیف لایه شما</span><span>{{ $discount }}%</span>
                </div>
                <div style="height:1px;background:var(--border);margin:0.75rem 0;"></div>
            @endif
            <div style="display:flex;justify-content:space-between;align-items:center;">
                <span style="color:#fff;font-weight:700;">قیمت نهایی</span>
                <span style="color:var(--gold-1);font-size:1.35rem;font-weight:800;">{{ number_format($price) }} <small style="font-size:0.7rem;color:var(--text-dim);font-weight:400;">تومان</small></span>
            </div>
        </div>

        @php $remaining = $event->remainingCapacity(); @endphp
        @if($remaining <= 3 && $remaining > 0)
            <div style="text-align:center;color:var(--gold-1);font-size:0.82rem;margin-bottom:1rem;">فقط {{ $remaining }} جای خالی باقی مانده</div>
        @endif

        {{-- وضعیت / دکمه --}}
        @if($isRegistered)
            <div style="text-align:center;padding:0.95rem;background:rgba(93,202,143,0.1);border:1px solid rgba(93,202,143,0.3);border-radius:14px;color:var(--success);font-weight:600;">
                ✓ شما در این دورهمی ثبت‌نام کرده‌اید
            </div>
            @if($event->starts_at->isFuture())
            <form method="POST" action="{{ route('panel.events.cancel', $event) }}" style="margin-top:0.75rem;" onsubmit="return confirm('آیا از انصراف مطمئن هستید؟ وجه پرداختی بازگردانده نمی‌شود.');">
                @csrf
                <button type="submit" class="btn btn-ghost" style="color:var(--danger);">انصراف از دورهمی</button>
            </form>
            <p style="color:var(--text-faint);font-size:0.72rem;text-align:center;margin-top:0.5rem;">انصراف با اطلاع، امتیاز مثبت دارد</p>
            @endif
        @elseif($isWaiting)
            <div style="text-align:center;padding:0.95rem;background:var(--surface-2);border:1px solid var(--border);border-radius:14px;color:var(--text-dim);">
                شما در لیست انتظار این دورهمی هستید
            </div>
        @elseif($event->status === 'full' || $remaining <= 0)
            <form method="POST" action="{{ route('panel.events.waitlist', $event) }}">
                @csrf
                <button type="submit" class="btn btn-ghost">عضویت در لیست انتظار</button>
            </form>
            <p style="color:var(--text-faint);font-size:0.75rem;text-align:center;margin-top:0.6rem;">در صورت خالی شدن ظرفیت به شما اطلاع داده می‌شود</p>
        @elseif(!$event->isRegistrationOpen())
            <div style="text-align:center;padding:0.95rem;background:var(--surface-2);border:1px solid var(--border);border-radius:14px;color:var(--text-dim);">
                ثبت‌نام این دورهمی بسته شده است
            </div>
        @else
            <a href="{{ route('panel.payment.checkout', $event) }}" class="btn btn-gold">
                ثبت‌نام در این دورهمی
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 12H5M12 5l-7 7 7 7"/></svg>
            </a>
        @endif
    </div>
</div>
@endsection
