<x-filament-panels::page>
<div style="width:100%;">

    {{-- موضوع و وضعیت --}}
    <div style="background:rgba(255,255,255,0.03);border:1px solid rgba(255,255,255,0.08);border-radius:14px;padding:1.25rem;margin-bottom:1.5rem;">
        <div style="display:flex;justify-content:space-between;align-items:center;gap:1rem;">
            <div style="min-width:0;">
                <div style="font-weight:bold;font-size:1.05rem;">{{ $this->convo->subject }}</div>
                <div style="font-size:0.82rem;opacity:0.6;margin-top:4px;direction:ltr;text-align:right;">{{ $this->convo->member->phone }}</div>
            </div>
            <button wire:click="toggleStatus" style="flex-shrink:0;padding:0.5rem 1rem;border-radius:9px;border:1px solid rgba(255,255,255,0.15);background:transparent;color:#aaa;cursor:pointer;font-size:0.8rem;font-family:inherit;white-space:nowrap;">
                {{ $this->convo->status === 'open' ? 'بستن گفتگو' : 'بازکردن' }}
            </button>
        </div>
    </div>

    {{-- پیام‌ها --}}
    <div style="display:flex;flex-direction:column;gap:0.85rem;margin-bottom:1.5rem;">
        @foreach($this->convo->messages as $msg)
            @if($msg->sender_type === 'member')
                {{-- مخاطب: راست، خاکستری --}}
                <div style="display:flex;justify-content:flex-start;">
                    <div style="max-width:65%;background:rgba(255,255,255,0.05);border:1px solid rgba(255,255,255,0.1);border-radius:16px;border-top-right-radius:4px;padding:0.85rem 1.1rem;">
                        <div style="font-size:0.72rem;color:#f59e0b;margin-bottom:5px;font-weight:600;">{{ $this->convo->member->first_name }}</div>
                        <div style="line-height:1.8;color:#fff;">{{ $msg->body }}</div>
                        <div style="font-size:0.68rem;opacity:0.45;margin-top:6px;">{{ $msg->created_at->format('H:i') }}</div>
                    </div>
                </div>
            @else
                {{-- ادمین: چپ، سبز --}}
                <div style="display:flex;justify-content:flex-end;">
                    <div style="max-width:65%;background:rgba(34,197,94,0.1);border:1px solid rgba(34,197,94,0.25);border-radius:16px;border-top-left-radius:4px;padding:0.85rem 1.1rem;">
                        <div style="font-size:0.72rem;color:#22c55e;margin-bottom:5px;font-weight:600;">مدیریت{{ $msg->admin ? ' · ' . $msg->admin->name : '' }}</div>
                        <div style="line-height:1.8;color:#fff;">{{ $msg->body }}</div>
                        <div style="font-size:0.68rem;opacity:0.45;margin-top:6px;">{{ $msg->created_at->format('H:i') }}</div>
                    </div>
                </div>
            @endif
        @endforeach
    </div>

    {{-- فرم پاسخ --}}
    @if($this->convo->status === 'open')
    <div style="background:rgba(255,255,255,0.03);border:1px solid rgba(255,255,255,0.08);border-radius:14px;padding:1.25rem;">
        <textarea wire:model="replyBody" rows="3" placeholder="پاسخ خود را بنویسید..."
            style="width:100%;background:#0d0d0f;border:1px solid rgba(255,255,255,0.15);border-radius:10px;padding:0.85rem 1rem;color:#fff;font-family:inherit;resize:vertical;box-sizing:border-box;"></textarea>
        <div style="margin-top:0.85rem;display:flex;justify-content:flex-end;">
            <x-filament::button wire:click="sendReply" icon="heroicon-o-paper-airplane">
                ارسال پاسخ
            </x-filament::button>
        </div>
    </div>
    @else
    <div style="text-align:center;padding:1.25rem;background:rgba(255,255,255,0.02);border-radius:12px;opacity:0.6;font-size:0.85rem;">
        این گفتگو بسته شده است
    </div>
    @endif

</div>
</x-filament-panels::page>
