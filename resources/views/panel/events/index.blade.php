@extends('panel.layouts.app')
@section('title', 'دورهمی‌ها')

@section('content')
<div class="panel-card">
    <h2>دورهمی‌ها</h2>

    @if($events->isEmpty())
        <p style="color:#888;text-align:center;padding:2rem 0;">در حال حاضر دورهمی فعالی برای شما وجود ندارد.</p>
    @else
        <div style="display:flex;flex-direction:column;gap:1rem;">
            @foreach($events as $event)
                <a href="{{ route('panel.events.show', $event) }}" style="display:block;text-decoration:none;color:inherit;">
                    <div style="background:#111;border:1px solid #2a2a2a;border-radius:12px;overflow:hidden;transition:border-color 0.2s;">
                        @if($event->image)
                            <img src="{{ Storage::url($event->image) }}" style="width:100%;height:140px;object-fit:cover;">
                        @endif
                        <div style="padding:1rem;">
                            <div style="font-weight:bold;color:#fff;font-size:1.05rem;">{{ $event->title }}</div>
                            @if($event->subtitle)
                                <div style="color:#888;font-size:0.85rem;margin-top:0.2rem;">{{ $event->subtitle }}</div>
                            @endif
                            <div style="display:flex;gap:1rem;margin-top:0.75rem;font-size:0.8rem;color:#aaa;">
                                <span>📅 {{ \Morilog\Jalali\Jalalian::fromDateTime($event->starts_at)->format('Y/m/d') }}</span>
                                <span>🕐 {{ $event->starts_at->format('H:i') }}</span>
                            </div>
                            @php $remaining = $event->remainingCapacity(); @endphp
                            @if($event->status === 'full' || $remaining <= 0)
                                <div style="margin-top:0.5rem;color:#ef4444;font-size:0.8rem;">ظرفیت تکمیل شده</div>
                            @elseif($remaining <= 3)
                                <div style="margin-top:0.5rem;color:#f59e0b;font-size:0.8rem;">فقط {{ $remaining }} جای خالی باقی مانده</div>
                            @endif
                        </div>
                    </div>
                </a>
            @endforeach
        </div>
    @endif
</div>

<div style="margin-top:1rem;">
    <a href="{{ route('panel.dashboard') }}" style="display:block;text-align:center;padding:0.85rem;background:#2a2a2a;color:#ddd;border-radius:10px;text-decoration:none;">بازگشت به داشبورد</a>
</div>
@endsection
