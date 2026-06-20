<x-filament-panels::page>
<div style="max-width:700px;margin:0 auto;">

    {{-- موضوع --}}
    <div style="background:rgba(255,255,255,0.03);border:1px solid rgba(255,255,255,0.08);border-radius:12px;padding:1rem 1.25rem;margin-bottom:1.5rem;display:flex;justify-content:space-between;align-items:center;">
        <div>
            <div style="font-weight:bold;">{{ $convo->subject }}</div>
            <div style="font-size:0.8rem;opacity:0.6;margin-top:3px;">{{ $convo->member->phone }}</div>
        </div>
        <button wire:click="toggleStatus" style="padding:0.5rem 1rem;border-radius:8px;border:1px solid #444;background:transparent;color:#aaa;cursor:pointer;font-size:0.8rem;font-family:inherit;">
            {{ $convo->status === 'open' ? 'بستن گفتگو' : 'بازکردن گفتگو' }}
        </button>
    </div>

    {{-- پیام‌ها --}}
    <div style="display:flex;flex-direction:column;gap:1rem;margin-bottom:1.5rem;">
        @foreach($convo->messages as $msg)
            @if($msg->sender_type === 'member')
                {{-- پیام مخاطب — راست --}}
                <div style="display:flex;justify-content:flex-start;">
                    <div style="max-width:75%;background:rgba(255,255,255,0.05);border:1px solid rgba(255,255,255,0.08);border-radius:14px;border-top-right-radius:4px;padding:0.85rem 1rem;">
                        <div style="font-size:0.7rem;color:#f59e0b;margin-bottom:4px;">{{ $convo->member->first_name }}</div>
                        <div style="line-height:1.7;">{{ $msg->body }}</div>
                        <div style="font-size:0.68rem;opacity:0.5;margin-top:5px;">{{ $msg->created_at->format('Y/m/d H:i') }}</div>
                    </div>
                </div>
            @else
                {{-- پیام ادمین — چپ --}}
                <div style="display:flex;justify-content:flex-end;">
                    <div style="max-width:75%;background:rgba(34,197,94,0.1);border:1px solid rgba(34,197,94,0.25);border-radius:14px;border-top-left-radius:4px;padding:0.85rem 1rem;">
                        <div style="font-size:0.7rem;color:#22c55e;margin-bottom:4px;">مدیریت{{ $msg->admin ? ' · ' . $msg->admin->name : '' }}</div>
                        <div style="line-height:1.7;">{{ $msg->body }}</div>
                        <div style="font-size:0.68rem;opacity:0.5;margin-top:5px;">{{ $msg->created_at->format('Y/m/d H:i') }}</div>
                    </div>
                </div>
            @endif
        @endforeach
    </div>

    {{-- فرم پاسخ --}}
    @if($convo->status === 'open')
    <div style="background:rgba(255,255,255,0.03);border:1px solid rgba(255,255,255,0.08);border-radius:12px;padding:1rem;">
        <textarea wire:model="replyBody" rows="3" placeholder="پاسخ خود را بنویسید..."
            style="width:100%;background:#0d0d0f;border:1px solid #333;border-radius:10px;padding:0.85rem 1rem;color:#fff;font-family:inherit;resize:vertical;"></textarea>
        <div style="margin-top:0.75rem;">
            <x-filament::button wire:click="sendReply" icon="heroicon-o-paper-airplane">
                ارسال پاسخ
            </x-filament::button>
        </div>
    </div>
    @else
    <div style="text-align:center;padding:1rem;opacity:0.5;font-size:0.85rem;">این گفتگو بسته شده است</div>
    @endif

</div>
</x-filament-panels::page>
