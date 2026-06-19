<x-filament-panels::page>
    @php
        $regs = $this->registrations;
        $total = $regs->count();
        $attended = $regs->where('attendance_status', 'attended')->count();
        $absent = $regs->where('attendance_status', 'absent')->count();
        $pending = $regs->where('attendance_status', 'registered')->count();
    @endphp

    {{-- آمار --}}
    <div style="display:grid;grid-template-columns:repeat(4,1fr);gap:1rem;margin-bottom:1.5rem;">
        <div style="background:rgba(255,255,255,0.03);border:1px solid rgba(255,255,255,0.08);border-radius:14px;padding:1rem;text-align:center;">
            <div style="font-size:1.6rem;font-weight:bold;">{{ $total }}</div>
            <div style="font-size:0.8rem;opacity:0.6;">کل ثبت‌نام</div>
        </div>
        <div style="background:rgba(34,197,94,0.08);border:1px solid rgba(34,197,94,0.25);border-radius:14px;padding:1rem;text-align:center;">
            <div style="font-size:1.6rem;font-weight:bold;color:#22c55e;">{{ $attended }}</div>
            <div style="font-size:0.8rem;opacity:0.6;">حاضر</div>
        </div>
        <div style="background:rgba(239,68,68,0.08);border:1px solid rgba(239,68,68,0.25);border-radius:14px;padding:1rem;text-align:center;">
            <div style="font-size:1.6rem;font-weight:bold;color:#ef4444;">{{ $absent }}</div>
            <div style="font-size:0.8rem;opacity:0.6;">غایب</div>
        </div>
        <div style="background:rgba(245,158,11,0.08);border:1px solid rgba(245,158,11,0.25);border-radius:14px;padding:1rem;text-align:center;">
            <div style="font-size:1.6rem;font-weight:bold;color:#f59e0b;">{{ $pending }}</div>
            <div style="font-size:0.8rem;opacity:0.6;">نیامده</div>
        </div>
    </div>

    {{-- لیست --}}
    <div style="background:rgba(255,255,255,0.02);border:1px solid rgba(255,255,255,0.08);border-radius:14px;overflow:hidden;">
        @forelse($regs as $reg)
        <div style="display:flex;align-items:center;gap:1rem;padding:1rem 1.25rem;border-bottom:1px solid rgba(255,255,255,0.05);">
            {{-- آواتار --}}
            @if($reg->member->avatar && $reg->member->avatar_approved)
                <img src="{{ \Illuminate\Support\Facades\Storage::url($reg->member->avatar) }}" style="width:44px;height:44px;border-radius:12px;object-fit:cover;flex-shrink:0;">
            @else
                <div style="width:44px;height:44px;border-radius:12px;background:#f59e0b;display:flex;align-items:center;justify-content:center;font-weight:bold;color:#000;flex-shrink:0;">{{ mb_substr($reg->member->first_name,0,1) }}</div>
            @endif

            {{-- اطلاعات --}}
            <div style="flex:1;min-width:0;">
                <div style="font-weight:bold;">{{ $reg->member->first_name }} {{ $reg->member->last_name }}</div>
                <div style="font-size:0.78rem;opacity:0.6;direction:ltr;text-align:right;">{{ $reg->member->phone }}</div>
            </div>

            {{-- وضعیت + دکمه --}}
            <div style="display:flex;align-items:center;gap:0.5rem;flex-shrink:0;">
                @if($reg->attendance_status === 'attended')
                    <span style="font-size:0.8rem;color:#22c55e;font-weight:bold;">✓ حاضر</span>
                    <button wire:click="markRegistered({{ $reg->id }})" style="padding:0.4rem 0.7rem;border-radius:8px;border:1px solid #444;background:transparent;color:#aaa;cursor:pointer;font-size:0.75rem;font-family:inherit;">لغو</button>
                @elseif($reg->attendance_status === 'absent')
                    <span style="font-size:0.8rem;color:#ef4444;font-weight:bold;">غایب</span>
                    <button wire:click="markRegistered({{ $reg->id }})" style="padding:0.4rem 0.7rem;border-radius:8px;border:1px solid #444;background:transparent;color:#aaa;cursor:pointer;font-size:0.75rem;font-family:inherit;">لغو</button>
                @else
                    <button wire:click="markAttended({{ $reg->id }})" style="padding:0.45rem 0.8rem;border-radius:8px;border:none;background:#22c55e;color:#fff;cursor:pointer;font-size:0.78rem;font-weight:bold;font-family:inherit;">حاضر</button>
                    <button wire:click="markAbsent({{ $reg->id }})" style="padding:0.45rem 0.8rem;border-radius:8px;border:none;background:#ef4444;color:#fff;cursor:pointer;font-size:0.78rem;font-weight:bold;font-family:inherit;">غایب</button>
                @endif
            </div>
        </div>
        @empty
        <div style="padding:3rem;text-align:center;opacity:0.5;">هنوز ثبت‌نامی برای این دورهمی نیست</div>
        @endforelse
    </div>
</x-filament-panels::page>
