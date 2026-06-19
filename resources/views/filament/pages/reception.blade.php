<x-filament-panels::page>
    <div style="max-width:500px;margin:0 auto;">

        {{-- نتیجه اسکن --}}
        @if($result)
            <div style="margin-bottom:1.5rem;padding:1.5rem;border-radius:16px;
                @if($result['status']==='success') background:rgba(34,197,94,0.1);border:2px solid #22c55e;
                @elseif($result['status']==='already_used') background:rgba(245,158,11,0.1);border:2px solid #f59e0b;
                @else background:rgba(239,68,68,0.1);border:2px solid #ef4444; @endif">

                {{-- آیکون وضعیت --}}
                <div style="text-align:center;font-size:3rem;margin-bottom:0.5rem;">
                    @if($result['status']==='success') ✅
                    @elseif($result['status']==='already_used') ⚠️
                    @else ❌ @endif
                </div>

                <div style="text-align:center;font-size:1.1rem;font-weight:bold;margin-bottom:1rem;
                    @if($result['status']==='success') color:#22c55e;
                    @elseif($result['status']==='already_used') color:#f59e0b;
                    @else color:#ef4444; @endif">
                    {{ $result['message'] }}
                </div>

                {{-- اطلاعات مهمان --}}
                @if(!empty($result['name']))
                <div style="display:flex;align-items:center;gap:1rem;background:rgba(0,0,0,0.2);border-radius:12px;padding:1rem;">
                    @if(!empty($result['avatar']))
                        <img src="{{ $result['avatar'] }}" style="width:60px;height:60px;border-radius:12px;object-fit:cover;">
                    @else
                        <div style="width:60px;height:60px;border-radius:12px;background:#f59e0b;display:flex;align-items:center;justify-content:center;font-size:1.5rem;font-weight:bold;color:#000;">
                            {{ mb_substr($result['name'], 0, 1) }}
                        </div>
                    @endif
                    <div>
                        <div style="font-size:1.1rem;font-weight:bold;">{{ $result['name'] }}</div>
                        @if(!empty($result['phone']))
                            <div style="font-size:0.85rem;opacity:0.7;">موبایل: ...{{ $result['phone'] }}</div>
                        @endif
                        @if(!empty($result['event']))
                            <div style="font-size:0.8rem;opacity:0.6;margin-top:2px;">{{ $result['event'] }}</div>
                        @endif
                    </div>
                </div>
                @endif

                <button wire:click="clearResult" style="width:100%;margin-top:1rem;padding:0.75rem;border-radius:10px;border:none;background:rgba(0,0,0,0.3);color:#fff;cursor:pointer;font-family:inherit;">
                    اسکن بعدی
                </button>
            </div>
        @endif

        {{-- اسکنر دوربین --}}
        @unless($result)
        <div style="background:#1a1a1a;border-radius:16px;padding:1.25rem;margin-bottom:1.5rem;">
            <div style="font-weight:bold;margin-bottom:1rem;text-align:center;">اسکن QR بلیت</div>
            <div id="reader" style="width:100%;border-radius:12px;overflow:hidden;"></div>
            <div id="scan-status" style="text-align:center;font-size:0.85rem;color:#888;margin-top:0.75rem;">در حال آماده‌سازی دوربین...</div>
        </div>

        {{-- ورود دستی --}}
        <div style="background:#1a1a1a;border-radius:16px;padding:1.25rem;">
            <div style="font-weight:bold;margin-bottom:1rem;">ورود دستی کد</div>
            <div style="display:flex;gap:0.5rem;">
                <input type="text" wire:model="manualCode" placeholder="PK-XXXXXXXXXX"
                    style="flex:1;background:#0d0d0f;border:1px solid #333;border-radius:10px;padding:0.75rem 1rem;color:#fff;font-family:monospace;direction:ltr;text-align:right;">
                <button wire:click="submitManual" style="padding:0.75rem 1.5rem;border-radius:10px;border:none;background:#f59e0b;color:#000;font-weight:bold;cursor:pointer;font-family:inherit;">
                    ثبت
                </button>
            </div>
        </div>
        @endunless

    </div>

    @unless($result)
    @push('scripts')
    <script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
    <script>
        document.addEventListener('livewire:navigated', initScanner);
        document.addEventListener('DOMContentLoaded', initScanner);

        function initScanner() {
            const readerEl = document.getElementById('reader');
            if (!readerEl || readerEl.dataset.started) return;
            readerEl.dataset.started = '1';

            const html5QrCode = new Html5Qrcode("reader");
            const statusEl = document.getElementById('scan-status');

            html5QrCode.start(
                { facingMode: "environment" },
                { fps: 10, qrbox: { width: 220, height: 220 } },
                (decodedText) => {
                    html5QrCode.stop().then(() => {
                        @this.checkIn(decodedText);
                    });
                },
                (err) => {}
            ).then(() => {
                if(statusEl) statusEl.textContent = 'دوربین را روی QR بلیت بگیرید';
            }).catch((err) => {
                if(statusEl) statusEl.textContent = 'دسترسی به دوربین ممکن نشد. از ورود دستی استفاده کنید.';
            });
        }
    </script>
    @endpush
    @endunless
</x-filament-panels::page>
