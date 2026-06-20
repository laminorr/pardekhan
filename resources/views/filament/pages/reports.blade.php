<x-filament-panels::page>
<div style="display:flex;flex-direction:column;gap:1.5rem;max-width:700px;">

    {{-- اعضا --}}
    @if(auth()->user()->isSuperAdmin())
    <div style="background:rgba(255,255,255,0.03);border:1px solid rgba(255,255,255,0.08);border-radius:14px;padding:1.5rem;">
        <div style="display:flex;align-items:center;gap:0.75rem;margin-bottom:0.75rem;">
            <div style="width:42px;height:42px;border-radius:11px;background:rgba(212,175,106,0.1);display:flex;align-items:center;justify-content:center;">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#d4af6a" stroke-width="1.7"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/></svg>
            </div>
            <div>
                <div style="font-weight:bold;">گزارش اعضا</div>
                <div style="font-size:0.8rem;opacity:0.6;">همه اعضا با لایه، امتیاز و کیف پول</div>
            </div>
        </div>
        <a href="{{ route('admin.reports.members') }}" style="display:inline-flex;align-items:center;gap:6px;padding:0.6rem 1.2rem;background:#d4af6a;color:#1a1408;border-radius:10px;text-decoration:none;font-weight:bold;font-size:0.85rem;">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4M7 10l5 5 5-5M12 15V3"/></svg>
            دانلود CSV
        </a>
    </div>

    {{-- مالی --}}
    <div style="background:rgba(255,255,255,0.03);border:1px solid rgba(255,255,255,0.08);border-radius:14px;padding:1.5rem;">
        <div style="display:flex;align-items:center;gap:0.75rem;margin-bottom:0.75rem;">
            <div style="width:42px;height:42px;border-radius:11px;background:rgba(212,175,106,0.1);display:flex;align-items:center;justify-content:center;">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#d4af6a" stroke-width="1.7"><rect x="2" y="5" width="20" height="14" rx="2.5"/><path d="M2 10h20"/></svg>
            </div>
            <div>
                <div style="font-weight:bold;">گزارش مالی</div>
                <div style="font-size:0.8rem;opacity:0.6;">همه تراکنش‌های کیف پول</div>
            </div>
        </div>
        <a href="{{ route('admin.reports.financial') }}" style="display:inline-flex;align-items:center;gap:6px;padding:0.6rem 1.2rem;background:#d4af6a;color:#1a1408;border-radius:10px;text-decoration:none;font-weight:bold;font-size:0.85rem;">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4M7 10l5 5 5-5M12 15V3"/></svg>
            دانلود CSV
        </a>
    </div>
    @endif

    {{-- ثبت‌نام‌های دورهمی --}}
    <div style="background:rgba(255,255,255,0.03);border:1px solid rgba(255,255,255,0.08);border-radius:14px;padding:1.5rem;">
        <div style="display:flex;align-items:center;gap:0.75rem;margin-bottom:1rem;">
            <div style="width:42px;height:42px;border-radius:11px;background:rgba(212,175,106,0.1);display:flex;align-items:center;justify-content:center;">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#d4af6a" stroke-width="1.7"><rect x="3" y="4" width="18" height="18" rx="2"/><path d="M16 2v4M8 2v4M3 10h18"/></svg>
            </div>
            <div>
                <div style="font-weight:bold;">گزارش ثبت‌نام دورهمی</div>
                <div style="font-size:0.8rem;opacity:0.6;">لیست شرکت‌کنندگان + وضعیت پرداخت و حضور</div>
            </div>
        </div>
        @if($this->events->isEmpty())
            <div style="opacity:0.5;font-size:0.85rem;">دورهمی‌ای وجود ندارد</div>
        @else
        <div style="display:flex;flex-direction:column;gap:0.5rem;">
            @foreach($this->events as $event)
            <div style="display:flex;justify-content:space-between;align-items:center;padding:0.6rem 0.85rem;background:rgba(0,0,0,0.2);border-radius:10px;">
                <div style="font-size:0.88rem;">
                    {{ $event->title }}
                    <span style="opacity:0.5;font-size:0.78rem;">· {{ \Morilog\Jalali\Jalalian::fromDateTime($event->starts_at)->format('Y/m/d') }}</span>
                </div>
                <a href="{{ route('admin.reports.event', $event) }}" style="display:inline-flex;align-items:center;gap:5px;padding:0.4rem 0.9rem;background:rgba(212,175,106,0.15);color:#d4af6a;border-radius:8px;text-decoration:none;font-size:0.78rem;font-weight:bold;white-space:nowrap;">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4M7 10l5 5 5-5M12 15V3"/></svg>
                    دانلود
                </a>
            </div>
            @endforeach
        </div>
        @endif
    </div>

</div>
</x-filament-panels::page>
