<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>بلیت — {{ $ticket->event->title }}</title>
<link href="https://fonts.googleapis.com/css2?family=Vazirmatn:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/qrcodejs@1.0.0/qrcode.min.js"></script>
<style>
    :root {
        --gold-1:#f0dca8; --gold-2:#d4af6a; --gold-3:#b8923f;
        --bg:#08080a; --surface:#121214; --border:rgba(255,255,255,0.08);
        --gold-border:rgba(212,175,106,0.25); --text:#ece9e4; --text-dim:#93908a;
    }
    html { background: var(--bg); }
    * { box-sizing:border-box; margin:0; padding:0; }
    body { font-family:'Vazirmatn',sans-serif; background:var(--bg); color:var(--text); padding:1.5rem; direction:rtl; min-height:100vh; }
    .ticket-wrap { max-width:400px; margin:0 auto; }

    .ticket {
        background:linear-gradient(160deg, rgba(212,175,106,0.1) 0%, var(--surface) 45%);
        border:1px solid var(--gold-border); border-radius:24px; overflow:hidden;
        position:relative;
    }
    .t-header { padding:1.5rem 1.5rem 1.25rem; text-align:center; border-bottom:1px dashed var(--gold-border); position:relative; }
    .t-brand { font-size:1.3rem; font-weight:900;
        background:linear-gradient(135deg,var(--gold-1),var(--gold-3));
        -webkit-background-clip:text; -webkit-text-fill-color:transparent; background-clip:text; }
    .t-brand-sub { font-size:0.62rem; color:var(--gold-2); letter-spacing:3px; margin-top:2px; }
    /* بریدگی‌های بلیت */
    .t-notch { position:absolute; bottom:-11px; width:22px; height:22px; background:var(--bg); border-radius:50%; }
    .t-notch.r { right:-11px; } .t-notch.l { left:-11px; }

    .t-body { padding:1.5rem; }
    .t-title { font-size:1.25rem; font-weight:700; color:#fff; text-align:center; }
    .t-sub { font-size:0.82rem; color:var(--text-dim); text-align:center; margin-top:4px; }

    .t-info { display:flex; flex-direction:column; gap:0.7rem; margin:1.5rem 0; padding:1rem; background:rgba(0,0,0,0.25); border-radius:14px; }
    .t-row { display:flex; justify-content:space-between; font-size:0.85rem; }
    .t-row .lbl { color:var(--text-dim); }
    .t-row .val { color:var(--text); font-weight:500; }

    /* QR */
    .t-qr-wrap { text-align:center; margin:1.5rem 0; }
    .t-qr { display:inline-block; padding:14px; background:#fff; border-radius:16px; }
    .t-qr img, .t-qr canvas { display:block; }
    .t-code { font-family:monospace; font-size:1rem; color:var(--gold-1); margin-top:0.85rem; letter-spacing:2px; direction:ltr; }
    .t-hint { font-size:0.72rem; color:var(--text-dim); margin-top:0.4rem; }

    .t-status {
        text-align:center; padding:0.6rem; border-radius:12px; font-size:0.85rem; font-weight:600; margin-top:0.5rem;
    }
    .t-status.active { background:rgba(93,202,143,0.12); color:#5dca8f; }
    .t-status.used { background:rgba(255,255,255,0.05); color:var(--text-dim); }
    .t-status.cancelled { background:rgba(226,101,90,0.12); color:#e2655a; }

    .actions { margin-top:1.5rem; display:flex; gap:0.75rem; }
    .btn { flex:1; display:flex; align-items:center; justify-content:center; gap:6px;
        padding:0.9rem; border-radius:14px; border:none; font-family:inherit; font-size:0.9rem;
        font-weight:700; cursor:pointer; text-decoration:none; }
    .btn-gold { background:linear-gradient(135deg,var(--gold-1),var(--gold-3)); color:#1a1408; }
    .btn-ghost { background:var(--surface); color:var(--text); border:1px solid var(--border); }

    @media print {
        body { background:#fff; color:#000; padding:0; }
        html { background:#fff; }
        .actions, .no-print { display:none !important; }
        .ticket { border:2px solid #d4af6a; background:#fff; }
        .t-title, .t-row .val { color:#000; }
        .t-info { background:#f5f5f5; }
        .t-notch { background:#fff; }
    }
</style>
</head>
<body>
<div class="ticket-wrap">
    <div class="ticket">
        <div class="t-header">
            <div class="t-brand">پرده‌خوان</div>
            <div class="t-brand-sub">بلیت ورود</div>
            <div class="t-notch r"></div>
            <div class="t-notch l"></div>
        </div>
        <div class="t-body">
            <div class="t-title">{{ $ticket->event->title }}</div>
            @if($ticket->event->subtitle)
                <div class="t-sub">{{ $ticket->event->subtitle }}</div>
            @endif

            <div class="t-info">
                <div class="t-row">
                    <span class="lbl">صاحب بلیت</span>
                    <span class="val">{{ $ticket->member->full_name }}</span>
                </div>
                <div class="t-row">
                    <span class="lbl">تاریخ</span>
                    <span class="val">{{ \Morilog\Jalali\Jalalian::fromDateTime($ticket->event->starts_at)->format('Y/m/d') }}</span>
                </div>
                <div class="t-row">
                    <span class="lbl">ساعت</span>
                    <span class="val">{{ $ticket->event->starts_at->format('H:i') }}</span>
                </div>
                @if($ticket->event->venue)
                <div class="t-row">
                    <span class="lbl">مکان</span>
                    <span class="val">{{ $ticket->event->venue->name }}</span>
                </div>
                @endif
            </div>

            <div class="t-qr-wrap">
                <div class="t-qr" id="qrcode"></div>
                <div class="t-code">{{ $ticket->code }}</div>
                <div class="t-hint">این کد را هنگام ورود نشان دهید</div>
            </div>

            <div class="t-status {{ $ticket->status }}">
                @if($ticket->status === 'active') ✓ بلیت معتبر
                @elseif($ticket->status === 'used') استفاده شده در {{ $ticket->used_at?->format('H:i') }}
                @else باطل شده @endif
            </div>
        </div>
    </div>

    <div class="actions">
        <button onclick="window.print()" class="btn btn-gold">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M6 9V2h12v7M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/><path d="M6 14h12v8H6z"/></svg>
            پرینت / ذخیره PDF
        </button>
        <a href="{{ route('panel.tickets.index') }}" class="btn btn-ghost no-print">بازگشت</a>
    </div>
</div>

<script>
    new QRCode(document.getElementById("qrcode"), {
        text: "{{ $ticket->code }}",
        width: 180,
        height: 180,
        colorDark: "#000000",
        colorLight: "#ffffff",
        correctLevel: QRCode.CorrectLevel.H
    });
</script>
</body>
</html>
