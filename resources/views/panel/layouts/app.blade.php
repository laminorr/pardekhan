<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="format-detection" content="telephone=no">
    <title>@yield('title', 'پرده‌خوان')</title>

    {{-- ── تشخیصِ زودهنگامِ ناوبری بین‌صفحه‌ای (پیش از رنگ‌گیریِ body/لودر) ──
         اگر پرچمِ ناوبری از صفحهٔ قبل ست شده باشد، همین‌جا در <head> کلاسِ pk-navmode روی <html> گذاشته می‌شود
         تا لودر از همان فریمِ نخست در حالتِ سبک رنگ بگیرد (بدون فلاشِ لحظه‌ایِ حالتِ کامل در پایان ناوبری).
         پرچم دقیقاً همین‌جا یک‌بار مصرف می‌شود. --}}
    <script>try{if(sessionStorage.getItem('pk-nav-loading')==='1'){document.documentElement.classList.add('pk-navmode');sessionStorage.removeItem('pk-nav-loading');}}catch(e){}</script>

    {{-- ── PWA (فقط پنل مخاطب /panel) ── --}}
    <link rel="manifest" href="/pwa/manifest.webmanifest">
    <meta name="theme-color" content="#2e5d50">
    {{-- آیکون‌های iOS / Safari --}}
    <link rel="apple-touch-icon" href="/pwa/apple-touch-icon.png">
    <link rel="apple-touch-icon" sizes="152x152" href="/pwa/icon-152.png">
    <link rel="apple-touch-icon" sizes="167x167" href="/pwa/icon-167.png">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="پرده‌خوان">
    {{-- ── iOS Splash Screens (apple-touch-startup-image) — نمایش اسپلش سبز به‌جای صفحهٔ سفید هنگام اجرا ── --}}
    {{-- iPhone 14/15 Pro Max — 430×932 @3 --}}
    <link rel="apple-touch-startup-image" href="/pwa/splash-iphone-14-15-pro-max.png"
          media="(device-width: 430px) and (device-height: 932px) and (-webkit-device-pixel-ratio: 3) and (orientation: portrait)">
    {{-- iPhone 14/15 Pro — 393×852 @3 --}}
    <link rel="apple-touch-startup-image" href="/pwa/splash-iphone-14-15-pro.png"
          media="(device-width: 393px) and (device-height: 852px) and (-webkit-device-pixel-ratio: 3) and (orientation: portrait)">
    {{-- iPhone 12/13/14 Plus & Pro Max (older) — 428×926 @3 --}}
    <link rel="apple-touch-startup-image" href="/pwa/splash-iphone-12-13-14-plus-max.png"
          media="(device-width: 428px) and (device-height: 926px) and (-webkit-device-pixel-ratio: 3) and (orientation: portrait)">
    {{-- iPhone 12/13/14 — 390×844 @3 --}}
    <link rel="apple-touch-startup-image" href="/pwa/splash-iphone-12-13-14.png"
          media="(device-width: 390px) and (device-height: 844px) and (-webkit-device-pixel-ratio: 3) and (orientation: portrait)">
    {{-- iPhone X/XS/11 Pro — 375×812 @3 --}}
    <link rel="apple-touch-startup-image" href="/pwa/splash-iphone-x-xs-11pro.png"
          media="(device-width: 375px) and (device-height: 812px) and (-webkit-device-pixel-ratio: 3) and (orientation: portrait)">
    {{-- iPhone XS Max/11 Pro Max — 414×896 @3 --}}
    <link rel="apple-touch-startup-image" href="/pwa/splash-iphone-xs-max-11promax.png"
          media="(device-width: 414px) and (device-height: 896px) and (-webkit-device-pixel-ratio: 3) and (orientation: portrait)">
    {{-- iPhone XR/11 — 414×896 @2 --}}
    <link rel="apple-touch-startup-image" href="/pwa/splash-iphone-xr-11.png"
          media="(device-width: 414px) and (device-height: 896px) and (-webkit-device-pixel-ratio: 2) and (orientation: portrait)">
    {{-- iPhone 6+/7+/8+ Plus — 414×736 @3 --}}
    <link rel="apple-touch-startup-image" href="/pwa/splash-iphone-plus.png"
          media="(device-width: 414px) and (device-height: 736px) and (-webkit-device-pixel-ratio: 3) and (orientation: portrait)">
    {{-- iPhone 6/7/8/SE2/SE3 — 375×667 @2 --}}
    <link rel="apple-touch-startup-image" href="/pwa/splash-iphone-8-se2.png"
          media="(device-width: 375px) and (device-height: 667px) and (-webkit-device-pixel-ratio: 2) and (orientation: portrait)">
    {{-- iPhone 5/SE1 — 320×568 @2 --}}
    <link rel="apple-touch-startup-image" href="/pwa/splash-iphone-se1.png"
          media="(device-width: 320px) and (device-height: 568px) and (-webkit-device-pixel-ratio: 2) and (orientation: portrait)">
    {{-- iPad 10.2 — 810×1080 @2 --}}
    <link rel="apple-touch-startup-image" href="/pwa/splash-ipad-10.2.png"
          media="(device-width: 810px) and (device-height: 1080px) and (-webkit-device-pixel-ratio: 2) and (orientation: portrait)">
    {{-- iPad 9.7 — 768×1024 @2 --}}
    <link rel="apple-touch-startup-image" href="/pwa/splash-ipad-9.7.png"
          media="(device-width: 768px) and (device-height: 1024px) and (-webkit-device-pixel-ratio: 2) and (orientation: portrait)">
    {{-- iPad Pro 11 — 834×1194 @2 --}}
    <link rel="apple-touch-startup-image" href="/pwa/splash-ipad-pro-11.png"
          media="(device-width: 834px) and (device-height: 1194px) and (-webkit-device-pixel-ratio: 2) and (orientation: portrait)">
    {{-- iPad Pro 12.9 — 1024×1366 @2 --}}
    <link rel="apple-touch-startup-image" href="/pwa/splash-ipad-pro-12.9.png"
          media="(device-width: 1024px) and (device-height: 1366px) and (-webkit-device-pixel-ratio: 2) and (orientation: portrait)">
    {{-- Favicon --}}
    <link rel="icon" type="image/png" sizes="32x32" href="/pwa/favicon-32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="/pwa/favicon-16.png">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Vazirmatn:wght@100..900&display=swap" rel="stylesheet">
    <style>
        :root {
            /* پالت سبز کاج روشن — گالری پرده‌خوان */
            --pine: #2f5d50;
            --pine-deep: #1f4d40;
            --pine-bright: #3f7a68;
            --burnt: #c2552f;

            --bg: #fcfcfb;
            --bg-soft: #f1f4f3;
            --bg-mute: #f0f1f0;
            --surface: #ffffff;
            --green-soft: #e8efec;
            --green-tint: #eaf3ef;
            --green-line: #eef1ef;

            --ink: #16181a;
            --ink-2: #0e1110;
            --ink-dim: #8b8f93;
            --ink-faint: #9aa09c;
            --ink-mid: #6a7470;
            --border: #ededeb;
            --border-2: #e6e8e6;
        }
        * { box-sizing: border-box; margin: 0; padding: 0; -webkit-tap-highlight-color: transparent; }
        html { background: var(--bg); }
        body {
            font-family: 'Vazirmatn', sans-serif;
            background: #fcfcfb;
            color: var(--ink);
            min-height: 100vh; direction: rtl; line-height: 1.6;
            -webkit-font-smoothing: antialiased;
        }
        html { scrollbar-gutter: stable; }
        .phone {
            max-width: 430px; margin: 0 auto; min-height: 100vh;
            position: relative; overflow-x: hidden; padding-bottom: 92px;
            background: #fcfcfb;
        }
        .wrap { padding: 1.4rem 1.2rem; position: relative; z-index: 1; }
        svg { display: block; }

        /* هدر */
        .topbar { display: flex; align-items: center; justify-content: space-between; margin-bottom: 1.4rem; }
        .greeting .hi { font-size: 0.8rem; color: var(--ink-dim); font-weight: 500; }
        .greeting .name { font-size: 1.45rem; font-weight: 800; color: var(--ink); line-height: 1.1;
            letter-spacing: -0.02em; margin-top: 1px; }
        .icon-btn {
            width: 44px; height: 44px; border-radius: 15px; background: var(--surface);
            border: 1px solid var(--border); display: flex; align-items: center; justify-content: center;
            color: var(--ink); position: relative; text-decoration: none;
            box-shadow: 0 2px 8px rgba(40,60,50,0.06);
        }
        .icon-btn .ndot { position: absolute; top: 10px; right: 11px; width: 8px; height: 8px;
            border-radius: 50%; background: var(--burnt); border: 2px solid var(--surface); }

        /* عنوان صفحه */
        .page-head { display: flex; align-items: center; gap: 0.7rem; margin-bottom: 1.4rem; }
        .page-title { font-size: 1.5rem; font-weight: 800; color: var(--ink); letter-spacing: -0.02em; }
        .page-sub { font-size: 0.82rem; color: var(--ink-dim); margin-top: 0.15rem; }

        /* کارت */
        .card { background: var(--surface); border: 1px solid #fff;
            border-radius: 22px; padding: 1.3rem; margin-bottom: 1rem;
            box-shadow: 0 4px 20px rgba(40,60,50,0.07); }

        /* بخش */
        .section-head { display: flex; align-items: center; justify-content: space-between; margin: 1.6rem 0 1rem; }
        .section-title { font-size: 1.15rem; font-weight: 800; color: var(--ink); letter-spacing: -0.02em; }
        .see-all { font-size: 0.78rem; color: var(--pine); text-decoration: none; font-weight: 700; }

        /* دکمه‌ها */
        .btn { display: flex; align-items: center; justify-content: center; gap: 6px;
            width: 100%; padding: 0.9rem; border-radius: 15px; border: none;
            font-family: inherit; font-size: 0.95rem; font-weight: 700; cursor: pointer;
            text-decoration: none; transition: transform 0.15s; }
        .btn:active { transform: scale(0.98); }
        .btn-primary { background: var(--pine); color: #fff; box-shadow: 0 8px 20px rgba(47,93,80,0.25); }
        .btn-burnt { background: var(--burnt); color: #fff; box-shadow: 0 8px 20px rgba(194,85,47,0.25); }
        .btn-ghost { background: var(--surface); color: var(--ink); border: 1px solid var(--border); }

        /* فرم */
        .field { margin-bottom: 1.1rem; }
        .field label { display: block; font-size: 0.82rem; color: var(--ink-mid); margin-bottom: 0.5rem; font-weight: 500; }
        .field input, .field textarea, .field select {
            width: 100%; background: var(--surface); border: 1px solid var(--border);
            border-radius: 14px; padding: 0.85rem 1rem; color: var(--ink);
            font-size: 1rem; font-family: inherit; transition: border-color 0.2s; }
        .field input:focus, .field textarea:focus, .field select:focus { outline: none; border-color: var(--pine); }

        /* پیام‌ها */
        .alert { padding: 0.85rem 1rem; border-radius: 13px; font-size: 0.85rem; margin-bottom: 1rem; }
        .alert-success { background: var(--green-tint); border: 1px solid #c5ddd2; color: var(--pine-deep); }
        .alert-danger { background: #fbeae4; border: 1px solid #f0cdbe; color: var(--burnt); }

        /* نویگیشن پایین */
        .bottom-nav { position: fixed; bottom: 0; left: 50%; transform: translateX(-50%);
            width: 100%; max-width: 430px; background: rgba(252,252,251,0.92);
            backdrop-filter: blur(18px); border-top: 1px solid var(--border);
            display: flex; justify-content: space-around; padding: 0.6rem 0 0.9rem; z-index: 50; }
        .nav-i { display: flex; flex-direction: column; align-items: center; gap: 3px;
            color: var(--ink-faint); text-decoration: none; font-size: 0.64rem; font-weight: 600; flex: 1; }
        .nav-i.on { color: var(--pine); }
        .nav-ico { width: 48px; height: 32px; border-radius: 12px; display: flex;
            align-items: center; justify-content: center; transition: background 0.2s; }
        .nav-i.on .nav-ico { background: var(--green-soft); }

        .back-link { display: flex; align-items: center; justify-content: center; gap: 6px;
            padding: 0.9rem; background: var(--surface); border: 1px solid var(--border);
            border-radius: 14px; color: var(--ink-dim); text-decoration: none; font-size: 0.9rem; margin-top: 1rem; }

        @keyframes pkring { from { stroke-dashoffset: 477.5; } }

        /* ── بنر نصب PWA ── */
        .pwa-install {
            display: none;
            align-items: center;
            gap: 0.7rem;
            margin: 0 1.2rem 0.6rem;
            padding: 0.8rem 0.9rem;
            background: var(--pine);
            color: #fff;
            border-radius: 16px;
            box-shadow: 0 8px 22px rgba(47, 93, 80, 0.28);
        }
        .pwa-install.show { display: flex; }
        .pwa-install .pwa-ico {
            width: 40px; height: 40px; border-radius: 12px; flex-shrink: 0;
            background: rgba(255, 255, 255, 0.14);
        }
        .pwa-install .pwa-txt { flex: 1; min-width: 0; }
        .pwa-install .pwa-title { font-size: 0.86rem; font-weight: 800; line-height: 1.3; }
        .pwa-install .pwa-desc {
            font-size: 0.72rem; font-weight: 500;
            color: rgba(255, 255, 255, 0.82); margin-top: 2px; line-height: 1.5;
        }
        .pwa-install .pwa-btn {
            flex-shrink: 0; padding: 0.5rem 0.95rem; border: none; border-radius: 11px;
            background: #fff; color: var(--pine-deep);
            font-family: inherit; font-size: 0.8rem; font-weight: 800; cursor: pointer;
        }
        .pwa-install .pwa-btn:active { transform: scale(0.97); }
        .pwa-install .pwa-close {
            flex-shrink: 0; width: 28px; height: 28px; border: none; border-radius: 9px;
            background: rgba(255, 255, 255, 0.14); color: #fff;
            display: flex; align-items: center; justify-content: center; cursor: pointer;
        }
        /* حالت راهنمای iOS: بدون دکمهٔ نصب، فقط متن */
        .pwa-install.ios .pwa-btn { display: none; }

        /* ── لودر برند پرده‌خوان (پوشش تمام‌صفحه، جای فلش سفید — منطبق بر اسپلش iOS) ── */
        #pk-loader {
            position: fixed; inset: 0; z-index: 9999;
            background: #2e5d50;
            display: flex; align-items: center; justify-content: center;
            opacity: 1; transition: opacity .35s ease;
        }
        #pk-loader.pk-hide { opacity: 0; pointer-events: none; }
        /* لوگو: دقیقاً وسط صفحه و هم‌اندازهٔ لوگوی اسپلش تا هنگام گذارِ اسپلش→لودر جابه‌جا نشود */
        #pk-loader .pk-logo {
            position: absolute; top: 50%; left: 50%;
            transform: translate(-50%, -50%);
            direction: ltr;                       /* چینش میله‌ها مستقل از RTL، مطابق اسپلش */
            display: flex; flex-direction: column; align-items: stretch;
            width: min(15vmin, 96px);
            gap: min(2.4vmin, 14px);
        }
        #pk-loader .pk-bar { height: min(2.4vmin, 15px); border-radius: 8px; }
        #pk-loader .pk-bar.b1 { width: 100%; background: #ffffff; }                        /* میلهٔ کامل */
        #pk-loader .pk-bar.b2 { width: 78%;  background: #c2552f; align-self: flex-end;   } /* نارنجی، چسبیده به راست */
        #pk-loader .pk-bar.b3 { width: 88%;  background: #ffffff; align-self: flex-start; } /* چسبیده به چپ */

        /* متن انتظار + ساعت شنی: زیر لوگو، بی‌آنکه لوگو را از مرکز جابه‌جا کنند */
        #pk-loader .pk-extras {
            position: absolute; left: 50%; top: 50%;
            transform: translateX(-50%);
            margin-top: min(15vmin, 104px);
            display: flex; flex-direction: column; align-items: center;
            gap: 14px; width: 86%; max-width: 320px;
            opacity: 0; animation: pk-extras-in .5s ease .25s forwards;
        }
        @keyframes pk-extras-in {
            from { opacity: 0; transform: translateX(-50%) translateY(8px); }
            to   { opacity: 1; transform: translateX(-50%) translateY(0); }
        }
        /* ساعت شنی (SVG درون‌خطی، انیمیشن خالص CSS) */
        #pk-loader .pk-hourglass { width: 34px; height: 42px; overflow: visible; }
        #pk-loader .pk-hg-rotor {
            transform-box: fill-box; transform-origin: center;
            animation: pk-hg-flip 2.6s ease-in-out infinite;
        }
        #pk-loader .pk-hg-fall {
            transform-box: fill-box; transform-origin: center;
            animation: pk-hg-fall 2.6s ease-in-out infinite;
        }
        @keyframes pk-hg-flip {
            0%, 34%  { transform: rotate(0deg); }
            50%, 84% { transform: rotate(180deg); }
            100%     { transform: rotate(360deg); }
        }
        @keyframes pk-hg-fall {
            0%   { opacity: 0; transform: translateY(-2px); }
            10%  { opacity: 1; }
            30%  { opacity: 1; transform: translateY(3px); }
            34%  { opacity: 0; }
            50%  { opacity: 0; transform: translateY(-2px); }
            60%  { opacity: 1; }
            80%  { opacity: 1; transform: translateY(3px); }
            84%  { opacity: 0; }
            100% { opacity: 0; transform: translateY(-2px); }
        }
        /* خط راهنمای فارسی */
        #pk-loader .pk-wait {
            margin: 0; color: rgba(255, 255, 255, 0.9);
            font-family: inherit; font-size: 0.9rem; font-weight: 600;
            line-height: 1.9; text-align: center; letter-spacing: .2px;
        }

        /* ── حالت ناوبری بین‌صفحه‌ای: سبک و ظریف — فقط لوگوی کوچکِ متحرک، بدون متن و ساعت شنی ──
           دو راهِ ورود به این حالت، با ظاهرِ کاملاً یکسان:
             • html.pk-navmode #pk-loader → از <head> پیش از رنگ‌گیری (صفحهٔ ورودی، بدون فلاش)
             • #pk-loader.pk-nav          → با JS هنگام ترکِ صفحه (صفحهٔ خروجی) */
        html.pk-navmode #pk-loader .pk-extras,
        #pk-loader.pk-nav .pk-extras { display: none; }            /* بی‌متن، بی‌ساعت‌شنی */
        html.pk-navmode #pk-loader .pk-logo,
        #pk-loader.pk-nav .pk-logo {                               /* کوچک‌تر از اسپلش (حدوداً نصف) */
            width: min(13vmin, 84px);
            gap: min(1.6vmin, 10px);
        }
        html.pk-navmode #pk-loader .pk-bar,
        #pk-loader.pk-nav .pk-bar {
            height: min(2.2vmin, 14px);
            transform-origin: center;
            opacity: .28;                                          /* حالت پایه؛ انیمیشن روشنش می‌کند */
            animation: pk-nav-bar 1.4s ease-in-out infinite;
        }
        html.pk-navmode #pk-loader .pk-bar.b1,
        #pk-loader.pk-nav .pk-bar.b1 { animation-delay: 0s; }     /* یکی‌یکی، پلکانی و نرم */
        html.pk-navmode #pk-loader .pk-bar.b2,
        #pk-loader.pk-nav .pk-bar.b2 { animation-delay: .16s; }
        html.pk-navmode #pk-loader .pk-bar.b3,
        #pk-loader.pk-nav .pk-bar.b3 { animation-delay: .32s; }
        @keyframes pk-nav-bar {
            0%, 65%, 100% { opacity: .28; transform: scaleX(.94); }
            30%           { opacity: 1;   transform: scaleX(1); }
        }

        @media (prefers-reduced-motion: reduce) {
            #pk-loader .pk-extras { opacity: 1; animation: none; transform: translateX(-50%); }
            #pk-loader .pk-hg-rotor { animation: none; }
            #pk-loader .pk-hg-fall { animation: none; opacity: 1; }
            html.pk-navmode #pk-loader .pk-bar,
            #pk-loader.pk-nav .pk-bar { animation: none; opacity: 1; transform: none; }
        }
    </style>
    @stack('styles')
</head>
<body>
    {{-- ── لودر برند (اولین چیز داخل body تا پیش از بقیه رنگ بگیرد) ── --}}
    <div id="pk-loader" role="status" aria-label="در حال بارگذاری صفحه، لطفاً منتظر بمانید">
        <div class="pk-logo" aria-hidden="true">
            <div class="pk-bar b1"></div>
            <div class="pk-bar b2"></div>
            <div class="pk-bar b3"></div>
        </div>
        <div class="pk-extras">
            <svg class="pk-hourglass" viewBox="0 0 24 30" fill="none" aria-hidden="true" focusable="false">
                <g class="pk-hg-rotor">
                    <rect x="4" y="1.6" width="16" height="2.6" rx="1.3" fill="#fff8ef"></rect>
                    <rect x="4" y="25.8" width="16" height="2.6" rx="1.3" fill="#fff8ef"></rect>
                    <path d="M6 4.6 C6 9.8 9.6 12.1 12 15 C14.4 12.1 18 9.8 18 4.6 Z"
                          fill="rgba(255,255,255,.14)" stroke="#fff8ef" stroke-width="1.3" stroke-linejoin="round"></path>
                    <path d="M6 25.4 C6 20.2 9.6 17.9 12 15 C14.4 17.9 18 20.2 18 25.4 Z"
                          fill="rgba(255,255,255,.14)" stroke="#fff8ef" stroke-width="1.3" stroke-linejoin="round"></path>
                    <path d="M8.4 6.4 C8.4 9.4 10.4 11 12 12.7 C13.6 11 15.6 9.4 15.6 6.4 Z" fill="#c2552f"></path>
                    <path d="M9.4 23.6 C9.4 21 10.9 19.6 12 18.3 C13.1 19.6 14.6 21 14.6 23.6 Z" fill="#c2552f"></path>
                </g>
                <line class="pk-hg-fall" x1="12" y1="14" x2="12" y2="17.4" stroke="#c2552f" stroke-width="1.2" stroke-linecap="round"></line>
            </svg>
            <p class="pk-wait">لطفاً تا بارگذاری کامل صفحه منتظر بمانید</p>
        </div>
    </div>
    {{-- نکته: تشخیصِ ناوبریِ ورودی به <head> منتقل شده (کلاسِ html.pk-navmode) تا پیش از رنگ‌گیریِ لودر اعمال شود؛
         این‌جا دیگر اسکریپتی لازم نیست و پرچم فقط یک‌بار در <head> مصرف می‌شود. --}}
    <div class="phone">
        {{-- بنر نصب PWA (فقط مرورگر پشتیبان؛ در حالت standalone پنهان) --}}
        <div class="pwa-install" id="pwaInstall" role="dialog" aria-label="نصب پرده‌خوان">
            <img class="pwa-ico" src="/pwa/icon-192.png" alt="" width="40" height="40">
            <div class="pwa-txt">
                <div class="pwa-title">نصب پرده‌خوان</div>
                <div class="pwa-desc" id="pwaDesc">افزودن به صفحهٔ اصلی برای دسترسی سریع‌تر</div>
            </div>
            <button class="pwa-btn" type="button" id="pwaInstallBtn">نصب</button>
            <button class="pwa-close" type="button" id="pwaCloseBtn" aria-label="بستن">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" aria-hidden="true">
                    <path d="M18 6 6 18M6 6l12 12"></path>
                </svg>
            </button>
        </div>
        <div class="wrap">
            @yield('content')
        </div>
        @yield('nav')
    </div>
    @stack('scripts')

    {{-- ── PWA: ثبت Service Worker (scope=/panel) + مدیریت دکمهٔ نصب ── --}}
    <script>
    (function () {
        // ثبت service worker با scope محدود به /panel
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', function () {
                navigator.serviceWorker.register('/sw.js', { scope: '/panel' }).catch(function () {});
            });
        }

        var STORAGE_KEY = 'pwa-install-dismissed';
        var banner = document.getElementById('pwaInstall');
        if (!banner) return;

        var installBtn = document.getElementById('pwaInstallBtn');
        var closeBtn = document.getElementById('pwaCloseBtn');
        var descEl = document.getElementById('pwaDesc');

        var isStandalone = window.matchMedia('(display-mode: standalone)').matches
            || window.navigator.standalone === true;

        function dismissed() {
            try { return localStorage.getItem(STORAGE_KEY) === '1'; } catch (e) { return false; }
        }
        function remember() {
            try { localStorage.setItem(STORAGE_KEY, '1'); } catch (e) {}
        }
        function hide() { banner.classList.remove('show'); }

        // اگر از قبل نصب/standalone است یا کاربر قبلاً رد کرده، چیزی نشان نده
        if (isStandalone || dismissed()) return;

        closeBtn.addEventListener('click', function () { hide(); remember(); });

        // ── Android / Chrome: beforeinstallprompt ──
        var deferredPrompt = null;
        window.addEventListener('beforeinstallprompt', function (e) {
            e.preventDefault();
            deferredPrompt = e;
            banner.classList.remove('ios');
            banner.classList.add('show');
        });

        installBtn.addEventListener('click', function () {
            if (!deferredPrompt) return;
            deferredPrompt.prompt();
            deferredPrompt.userChoice.then(function () {
                deferredPrompt = null;
                hide();
            });
        });

        // پس از نصب موفق، بنر را پنهان و رد‌شدن را ثبت کن
        window.addEventListener('appinstalled', function () {
            deferredPrompt = null;
            hide();
            remember();
        });

        // ── iOS Safari: بدون beforeinstallprompt → راهنمای «افزودن به صفحهٔ اصلی» ──
        var ua = window.navigator.userAgent;
        var isIOS = /iPad|iPhone|iPod/.test(ua) && !window.MSStream;
        // فقط Safari واقعی (نه کروم/فایرفاکس روی iOS که نصب PWA ندارند)
        var isIOSSafari = isIOS && /Safari/.test(ua) && !/CriOS|FxiOS|EdgiOS|OPiOS/.test(ua);

        if (isIOSSafari && !isStandalone) {
            banner.classList.add('ios');
            descEl.textContent = 'در Safari دکمهٔ اشتراک‌گذاری را بزنید و «افزودن به صفحهٔ اصلی» را انتخاب کنید.';
            banner.classList.add('show');
        }
    })();
    </script>

    {{-- ── کنترل لودر برند: پنهان‌سازی هنگام آماده‌شدن، نمایش دوباره بین صفحات، ایمنی ── --}}
    <script>
    (function () {
        var loader = document.getElementById('pk-loader');
        if (!loader) return;

        var MIN_MS = 300;      // حداقل زمان نمایش تا سوسو نزند
        var SAFETY_MS = 5000;  // ایمنی: هرگز صفحه را قفل نکن
        var shownAt = Date.now();

        function clearNavFlag() {
            try { sessionStorage.removeItem('pk-nav-loading'); } catch (e) {}
        }

        function hide() {
            var wait = Math.max(0, MIN_MS - (Date.now() - shownAt));
            setTimeout(function () {
                loader.classList.add('pk-hide');
                clearNavFlag();                      // پرچم پاک شود تا اجرای تازهٔ بعدی گیرِ حالت سبک نکند
            }, wait);
        }
        function forceHide() {
            loader.classList.add('pk-hide');
            clearNavFlag();
        }
        function show() {
            shownAt = Date.now();
            // پرچمِ انتقال به صفحهٔ بعد: تا لودرِ صفحهٔ تازه هم از همان ابتدا سبک شروع شود (بدون فلاشِ حالت کامل)
            try { sessionStorage.setItem('pk-nav-loading', '1'); } catch (e) {}
            loader.classList.add('pk-nav');          // ناوبری بین‌صفحه‌ای → حالت سبک (فقط لوگوی کوچکِ متحرک)
            loader.classList.remove('pk-hide');
            // اگر ناوبری به هر دلیل انجام نشد، کاربر را گیر نینداز
            setTimeout(forceHide, SAFETY_MS);
        }

        // پنهان‌سازی وقتی صفحه آماده است
        if (document.readyState === 'complete') { hide(); }
        else { window.addEventListener('load', hide); }

        // ایمنی اولیه: در هر صورت پس از چند ثانیه پنهان شود
        setTimeout(forceHide, SAFETY_MS);

        // back/forward و بازیابی از bfcache → لودر نباید بماند
        window.addEventListener('pageshow', function (e) {
            if (e.persisted) forceHide();
        });
        window.addEventListener('pagehide', function () { /* ناوبری واقعی */ });

        function isModified(e) {
            return e.button !== 0 || e.metaKey || e.ctrlKey || e.shiftKey || e.altKey;
        }

        // نمایش دوباره هنگام ناوبری داخل پنل (کلیک روی لینک)
        document.addEventListener('click', function (e) {
            if (e.defaultPrevented || isModified(e)) return;
            var a = e.target && e.target.closest ? e.target.closest('a[href]') : null;
            if (!a) return;
            if (a.target && a.target !== '_self') return;      // _blank و…
            if (a.hasAttribute('download')) return;
            var raw = a.getAttribute('href') || '';
            if (/^(javascript:|mailto:|tel:|sms:|#)/i.test(raw)) return;
            var url;
            try { url = new URL(a.href, location.href); } catch (err) { return; }
            if (url.origin !== location.origin) return;         // لینک خارجی
            // لنگر درون‌صفحه‌ای (همان مسیر، فقط #hash) → ناوبری نیست
            if (url.pathname === location.pathname && url.search === location.search && url.hash) return;
            show();
        }, true);

        // ارسال فرم هم ناوبری کامل است
        document.addEventListener('submit', function (e) {
            var f = e.target;
            if (!f || f.tagName !== 'FORM') return;
            if (f.target && f.target !== '_self') return;
            var url;
            try { url = new URL(f.getAttribute('action') || location.href, location.href); }
            catch (err) { return; }
            if (url.origin !== location.origin) return;
            show();
        }, true);
    })();
    </script>
</body>
</html>
