<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>بلیت — {{ $ticket->event->title }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Vazirmatn:wght@100..900&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/qrcodejs@1.0.0/qrcode.min.js"></script>
    <style>
        :root { --pine:#2f5d50; --pine-bright:#3f7a68; --paper:#fcfcfb; --ink:#16181a; --dim:#8b8f93; --dim2:#9aa09c; }
        * { box-sizing:border-box; margin:0; padding:0; -webkit-tap-highlight-color:transparent; }
        html { background:#0e211c; }
        body { font-family:'Vazirmatn',sans-serif; background:#0e211c; color:#eaf3ef; min-height:100vh; direction:rtl; -webkit-font-smoothing:antialiased; }
        .screen { max-width:430px; margin:0 auto; min-height:100vh; display:flex; flex-direction:column; padding-bottom:1.5rem; }
        svg { display:block; }

        .t-head { display:flex; align-items:center; gap:14px; padding:1.4rem 1.5rem 0; }
        .t-back { width:42px; height:42px; border-radius:13px; border:1px solid rgba(255,255,255,0.12); background:rgba(255,255,255,0.06); display:flex; align-items:center; justify-content:center; text-decoration:none; }
        .t-head-title { font-size:1.15rem; font-weight:800; }
        .t-label { text-align:center; padding:0.9rem 1.5rem 0; }
        .t-label span { font-size:0.7rem; letter-spacing:3px; color:#6ea38f; font-weight:700; }

        /* کارت بلیت */
        .ticket { margin:1.3rem 1.3rem 0; background:var(--paper); border-radius:26px; color:var(--ink); position:relative; overflow:hidden; box-shadow:0 30px 60px -30px rgba(0,0,0,0.5); }
        .ticket-bar { height:8px; background:linear-gradient(90deg,var(--pine),var(--pine-bright)); }
        .ticket-pad { padding:1.4rem 1.5rem 0.5rem; }
        .t-title { font-size:1.35rem; font-weight:800; line-height:1.25; letter-spacing:-0.4px; }
        .t-sub { font-size:0.78rem; color:var(--dim); margin-top:4px; }
        .t-row { padding:0 1.5rem; display:flex; justify-content:space-between; margin-top:0.9rem; }
        .t-row .k { font-size:0.7rem; color:var(--dim2); }
        .t-row .v { font-size:0.88rem; font-weight:800; margin-top:3px; }

        /* بریدگی و خط‌چین */
        .t-perf { position:relative; height:28px; margin-top:1.1rem; }
        .t-perf .notch { position:absolute; top:50%; width:28px; height:28px; border-radius:50%; background:#0e211c; transform:translateY(-50%); }
        .t-perf .notch.r { right:-14px; }
        .t-perf .notch.l { left:-14px; }
        .t-perf .dash { position:absolute; top:50%; left:22px; right:22px; border-top:2px dashed #d8dcda; }

        /* QR */
        .t-qr-area { padding:1rem 1.5rem 1.6rem; display:flex; flex-direction:column; align-items:center; }
        .t-qr { padding:14px; background:#fff; border-radius:18px; box-shadow:inset 0 0 0 1px #f0f1f0; }
        .t-qr img, .t-qr canvas { display:block; }
        .t-code-label { margin-top:14px; font-size:0.7rem; color:var(--dim2); }
        .t-code { font-size:1.15rem; font-weight:800; letter-spacing:3px; margin-top:3px; direction:ltr; }
        .t-status { margin-top:14px; display:inline-flex; align-items:center; gap:7px; font-size:0.75rem; font-weight:800; padding:8px 16px; border-radius:20px; }
        .t-status.active { background:#e8efec; color:var(--pine); }
        .t-status.pending_payment { background:#fbeee4; color:#c2552f; }
        .t-status.used { background:#f0f1f0; color:var(--dim); }
        .t-status.cancelled { background:#fbeae4; color:#c2552f; }
        .t-status .dot { width:8px; height:8px; border-radius:50%; background:currentColor; animation:pkdot 2s infinite; }
        @keyframes pkdot { 0%,100%{opacity:1;} 50%{opacity:0.3;} }

        .t-note { text-align:center; padding:1.2rem 2.2rem 0; font-size:0.76rem; color:#6ea38f; line-height:1.9; }
    </style>
</head>
<body>
<div class="screen">
    <div class="t-head">
        <a href="{{ route('panel.tickets.index') }}" class="t-back">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#eaf3ef" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M15 6l-6 6 6 6"/></svg>
        </a>
        <div class="t-head-title">بلیت من</div>
    </div>
    <div class="t-label"><span>بلیت دورهمی پرده‌خوان</span></div>

    <div class="ticket">
        <div class="ticket-bar"></div>
        <div class="ticket-pad">
            <div class="t-title">{{ $ticket->event->title }}</div>
            @if($ticket->event->subtitle)
                <div class="t-sub">{{ $ticket->event->subtitle }}</div>
            @endif
        </div>

        <div class="t-row">
            <div><div class="k">صاحب بلیت</div><div class="v">{{ $ticket->member->full_name }}</div></div>
            <div style="text-align:left;"><div class="k">لایه</div><div class="v" style="color:var(--pine);">{{ $ticket->member->layer?->name ?? '—' }}</div></div>
        </div>
        <div class="t-row">
            <div><div class="k">تاریخ و ساعت</div><div class="v" style="font-size:0.82rem;">{{ fa(\Morilog\Jalali\Jalalian::fromDateTime($ticket->event->starts_at)->format('l j F')) }} · {{ fa($ticket->event->starts_at->format('H:i')) }}</div></div>
            @if($ticket->event->venue)
            <div style="text-align:left;"><div class="k">مکان</div><div class="v" style="font-size:0.82rem;">{{ $ticket->event->venue->name }}</div></div>
            @endif
        </div>

        <div class="t-perf">
            <div class="notch r"></div>
            <div class="notch l"></div>
            <div class="dash"></div>
        </div>

        <div class="t-qr-area">
            <div class="t-qr" id="qrcode"></div>
            <div class="t-code-label">کد یکتای بلیت</div>
            <div class="t-code">{{ fa($ticket->code) }}</div>

            <div class="t-status {{ $ticket->status }}">
                @if($ticket->status === 'active')
                    <span class="dot"></span>معتبر · آمادهٔ ورود
                @elseif($ticket->status === 'pending_payment')
                    در انتظار تایید پرداخت
                @elseif($ticket->status === 'used')
                    استفاده شده@if($ticket->used_at) · {{ fa($ticket->used_at->format('H:i')) }}@endif
                @else
                    لغو شده
                @endif
            </div>
        </div>
    </div>

    <div class="t-note">این کد را هنگام ورود نشان دهید. بلیت قابل انتقال نیست.</div>
</div>

<script>
    new QRCode(document.getElementById("qrcode"), {
        text: "{{ $ticket->code }}",
        width: 150, height: 150,
        colorDark: "#11302a", colorLight: "#ffffff",
        correctLevel: QRCode.CorrectLevel.H
    });
</script>
</body>
</html>
