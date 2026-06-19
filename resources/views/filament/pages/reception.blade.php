<x-filament-panels::page>
<div style="max-width:480px;margin:0 auto;" wire:key="reception-{{ $preview ? 'preview' : ($result ? 'result' : 'scan') }}">

    {{-- ═══ نتیجه نهایی ═══ --}}
    @if($result)
        @php
            $rc = match($result['status']) {
                'success' => ['#22c55e', 'rgba(34,197,94,0.08)'],
                'already_used' => ['#f59e0b', 'rgba(245,158,11,0.08)'],
                default => ['#ef4444', 'rgba(239,68,68,0.08)'],
            };
        @endphp
        <div style="padding:1.75rem 1.5rem;border-radius:18px;border:2px solid {{ $rc[0] }};background:{{ $rc[1] }};text-align:center;">
            <div style="width:64px;height:64px;margin:0 auto 1rem;border-radius:50%;background:{{ $rc[0] }};display:flex;align-items:center;justify-content:center;">
                @if($result['status']==='success')
                    <svg width="34" height="34" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6L9 17l-5-5"/></svg>
                @elseif($result['status']==='already_used')
                    <svg width="34" height="34" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M12 9v4M12 17h.01"/><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/></svg>
                @else
                    <svg width="34" height="34" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6L6 18M6 6l12 12"/></svg>
                @endif
            </div>
            <div style="font-size:1.15rem;font-weight:bold;color:{{ $rc[0] }};margin-bottom:0.5rem;">{{ $result['message'] }}</div>
            @if(!empty($result['used_at']))
                <div style="font-size:0.85rem;color:{{ $rc[0] }};opacity:0.8;margin-bottom:1rem;">ساعت ثبت: {{ $result['used_at'] }}</div>
            @endif

            @if(!empty($result['name']))
            <div style="display:flex;align-items:center;gap:1rem;background:rgba(0,0,0,0.25);border-radius:14px;padding:1rem;margin-top:1rem;text-align:right;">
                @if(!empty($result['avatar']))
                    <img src="{{ $result['avatar'] }}" style="width:56px;height:56px;border-radius:14px;object-fit:cover;flex-shrink:0;">
                @else
                    <div style="width:56px;height:56px;border-radius:14px;background:#f59e0b;display:flex;align-items:center;justify-content:center;font-size:1.4rem;font-weight:bold;color:#000;flex-shrink:0;">{{ mb_substr($result['name'],0,1) }}</div>
                @endif
                <div style="flex:1;">
                    <div style="font-size:1.05rem;font-weight:bold;color:#fff;">{{ $result['name'] }}</div>
                    @if(!empty($result['phone']))<div style="font-size:0.82rem;color:#aaa;margin-top:3px;">موبایل: ...{{ $result['phone'] }}</div>@endif
                    @if(!empty($result['event']))<div style="font-size:0.78rem;color:#888;margin-top:2px;">{{ $result['event'] }}</div>@endif
                </div>
            </div>
            @endif

            <button wire:click="reset_scan" style="width:100%;margin-top:1.25rem;padding:0.9rem;border-radius:12px;border:none;background:#f59e0b;color:#000;font-weight:bold;cursor:pointer;font-family:inherit;font-size:0.95rem;">
                اسکن بعدی
            </button>
        </div>

    {{-- ═══ پیش‌نمایش — تایید قبل از ثبت ═══ --}}
    @elseif($preview)
        <div style="padding:1.5rem;border-radius:18px;border:2px solid #d4af6a;background:rgba(212,175,106,0.06);">
            <div style="text-align:center;font-size:0.85rem;color:#d4af6a;margin-bottom:1.25rem;">بلیت معتبر — لطفاً هویت را تایید کنید</div>

            <div style="display:flex;align-items:center;gap:1rem;background:rgba(0,0,0,0.25);border-radius:14px;padding:1.25rem;">
                @if(!empty($preview['avatar']))
                    <img src="{{ $preview['avatar'] }}" style="width:72px;height:72px;border-radius:16px;object-fit:cover;flex-shrink:0;">
                @else
                    <div style="width:72px;height:72px;border-radius:16px;background:#f59e0b;display:flex;align-items:center;justify-content:center;font-size:1.8rem;font-weight:bold;color:#000;flex-shrink:0;">{{ mb_substr($preview['name'],0,1) }}</div>
                @endif
                <div style="flex:1;">
                    <div style="font-size:1.2rem;font-weight:bold;color:#fff;">{{ $preview['name'] }}</div>
                    <div style="font-size:0.85rem;color:#aaa;margin-top:4px;">موبایل: ...{{ $preview['phone'] }}</div>
                    <div style="font-size:0.8rem;color:#888;margin-top:2px;">{{ $preview['event'] }}</div>
                </div>
            </div>

            <div style="display:flex;gap:0.75rem;margin-top:1.25rem;">
                <button wire:click="reset_scan" style="flex:1;padding:0.9rem;border-radius:12px;border:1px solid #444;background:transparent;color:#aaa;font-weight:bold;cursor:pointer;font-family:inherit;">لغو</button>
                <button wire:click="confirm" style="flex:2;padding:0.9rem;border-radius:12px;border:none;background:#22c55e;color:#fff;font-weight:bold;cursor:pointer;font-family:inherit;font-size:0.95rem;">✓ تایید و ثبت حضور</button>
            </div>
        </div>

    {{-- ═══ اسکنر ═══ --}}
    @else
        <div style="background:#1a1a1a;border-radius:16px;padding:1.25rem;margin-bottom:1.25rem;">
            <div style="font-weight:bold;margin-bottom:1rem;text-align:center;">اسکن QR بلیت</div>
            <div id="reader" wire:ignore style="width:100%;border-radius:12px;overflow:hidden;"></div>
            <div id="scan-status" style="text-align:center;font-size:0.85rem;color:#888;margin-top:0.75rem;">در حال آماده‌سازی دوربین...</div>
        </div>

        <div style="background:#1a1a1a;border-radius:16px;padding:1.25rem;">
            <div style="font-weight:bold;margin-bottom:1rem;">ورود دستی کد</div>
            <div style="display:flex;gap:0.5rem;">
                <input type="text" wire:model="manualCode" wire:keydown.enter="submitManual" placeholder="PK-XXXXXXXXXX"
                    style="flex:1;background:#0d0d0f;border:1px solid #333;border-radius:10px;padding:0.75rem 1rem;color:#fff;font-family:monospace;direction:ltr;text-align:right;">
                <button wire:click="submitManual" style="padding:0.75rem 1.5rem;border-radius:10px;border:none;background:#f59e0b;color:#000;font-weight:bold;cursor:pointer;font-family:inherit;">ثبت</button>
            </div>
        </div>
    @endif

</div>

@if(!$preview && !$result)
@push('scripts')
<script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
<script>
    (function() {
        let scanner = null;

        function startScanner() {
            const el = document.getElementById('reader');
            const statusEl = document.getElementById('scan-status');
            if (!el) return;

            scanner = new Html5Qrcode("reader");
            scanner.start(
                { facingMode: "environment" },
                { fps: 10, qrbox: { width: 220, height: 220 } },
                (decodedText) => {
                    if (scanner) {
                        scanner.stop().then(() => {
                            @this.lookup(decodedText);
                        }).catch(() => {
                            @this.lookup(decodedText);
                        });
                    }
                },
                () => {}
            ).then(() => {
                if (statusEl) statusEl.textContent = 'دوربین را روی QR بگیرید';
            }).catch(() => {
                if (statusEl) statusEl.textContent = 'دوربین در دسترس نیست. از ورود دستی استفاده کنید.';
            });
        }

        // شروع با کمی تاخیر تا DOM آماده شه
        setTimeout(startScanner, 300);
    })();
</script>
@endpush
@endif
</x-filament-panels::page>
