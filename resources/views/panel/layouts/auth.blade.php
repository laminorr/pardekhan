<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="format-detection" content="telephone=no">
    <title>@yield('title', 'پرده‌خوان')</title>

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
            --pine: #2f5d50; --pine-deep: #1f4d40; --pine-bright: #3f7a68; --burnt: #c2552f;
            --bg: #eceeec; --surface: #ffffff; --green-soft: #e8efec; --green-tint: #eaf3ef;
            --ink: #16181a; --ink-dim: #8b8f93; --ink-faint: #9aa09c; --ink-mid: #6a7470;
            --border: #ededeb; --danger: #c2552f; --success: #2f5d50;
        }
        html { background: var(--bg); }
        * { box-sizing: border-box; margin: 0; padding: 0; -webkit-tap-highlight-color: transparent; }
        body {
            font-family: 'Vazirmatn', sans-serif; background: var(--bg); color: var(--ink);
            min-height: 100vh; direction: rtl; line-height: 1.6;
            display: flex; align-items: center; justify-content: center; padding: 1.5rem;
            -webkit-font-smoothing: antialiased;
        }
        body::before {
            content: ''; position: fixed; top: -120px; left: 50%; transform: translateX(-50%);
            width: 420px; height: 420px;
            background: radial-gradient(circle, rgba(47,93,80,0.1) 0%, transparent 68%);
            pointer-events: none;
        }
        .auth-wrap { width: 100%; max-width: 410px; position: relative; z-index: 1; }
        .auth-logo { text-align: center; margin-bottom: 1.75rem; }
        .auth-logo .name { font-size: 1.9rem; font-weight: 800; letter-spacing: -0.5px; color: var(--pine); }
        .auth-logo .sub { font-size: 0.68rem; color: var(--ink-faint); letter-spacing: 0; margin-top: 3px; font-weight: 600; }

        .auth-card {
            background: linear-gradient(180deg, #ffffff, #fbfcfb);
            border: 1px solid var(--border); border-radius: 26px; padding: 2rem 1.75rem;
            box-shadow: 0 1px 0 #fff, 0 24px 48px -32px rgba(47,93,80,0.45);
        }
        .auth-card h2 { font-size: 1.4rem; font-weight: 800; color: var(--ink); margin-bottom: 0.4rem; letter-spacing: -0.02em; }
        .auth-card .lead { font-size: 0.85rem; color: var(--ink-dim); margin-bottom: 1.75rem; line-height: 1.7; }

        .field { margin-bottom: 1.1rem; }
        .field label { display: block; font-size: 0.8rem; color: var(--ink-mid); margin-bottom: 0.5rem; font-weight: 500; }
        .field input {
            width: 100%; background: var(--surface); border: 1px solid var(--border);
            border-radius: 14px; padding: 0.9rem 1rem; color: var(--ink);
            font-size: 1rem; font-family: inherit; transition: border-color 0.2s;
        }
        .field input:focus { outline: none; border-color: var(--pine); }

        .btn {
            display: flex; align-items: center; justify-content: center; gap: 6px;
            width: 100%; padding: 0.95rem; border-radius: 15px; border: none;
            font-family: inherit; font-size: 0.95rem; font-weight: 700; cursor: pointer;
            text-decoration: none; transition: transform 0.15s; margin-top: 0.5rem;
        }
        .btn:active { transform: scale(0.98); }
        .btn-gold, .btn-primary { background: var(--pine); color: #fff; box-shadow: 0 8px 20px rgba(47,93,80,0.25); }
        .btn-ghost { background: var(--surface); color: var(--ink); border: 1px solid var(--border); margin-top: 0.75rem; }

        .alert { padding: 0.85rem 1rem; border-radius: 13px; font-size: 0.85rem; margin-bottom: 1rem; }
        .alert-success { background: var(--green-tint); border: 1px solid #c5ddd2; color: var(--pine-deep); }
        .alert-danger { background: #fbeae4; border: 1px solid #f0cdbe; color: var(--burnt); }

        .auth-foot { text-align: center; margin-top: 1.5rem; font-size: 0.85rem; color: var(--ink-dim); }
        .auth-foot a { color: var(--pine); text-decoration: none; font-weight: 700; }

        .status-box { text-align: center; padding: 1rem 0; }
        .status-icon { width: 72px; height: 72px; border-radius: 22px; margin: 0 auto 1.25rem; display: flex; align-items: center; justify-content: center; }
        .status-icon.wait { background: var(--green-soft); border: 1px solid #c5ddd2; color: var(--pine); }
        .status-icon.reject { background: #fbeae4; border: 1px solid #f0cdbe; color: var(--burnt); }
        .status-box h2 { font-size: 1.25rem; color: var(--ink); margin-bottom: 0.6rem; font-weight: 800; }
        .status-box p { font-size: 0.88rem; color: var(--ink-dim); line-height: 1.8; }

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
            width: min(25vmin, 160px);
            gap: min(3vmin, 19px);
        }
        #pk-loader .pk-bar { height: min(4vmin, 26px); border-radius: 8px; }
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
        @media (prefers-reduced-motion: reduce) {
            #pk-loader .pk-extras { opacity: 1; animation: none; transform: translateX(-50%); }
            #pk-loader .pk-hg-rotor { animation: none; }
            #pk-loader .pk-hg-fall { animation: none; opacity: 1; }
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
    <div class="auth-wrap">
        <div class="auth-logo">
            <div class="name">پرده‌خوان</div>
            <div class="sub">باشگاه اعضا</div>
        </div>
        @yield('content')
    </div>

    {{-- ── کنترل لودر برند: پنهان‌سازی هنگام آماده‌شدن، نمایش دوباره بین صفحات، ایمنی ── --}}
    <script>
    (function () {
        var loader = document.getElementById('pk-loader');
        if (!loader) return;

        var MIN_MS = 300;      // حداقل زمان نمایش تا سوسو نزند
        var SAFETY_MS = 5000;  // ایمنی: هرگز صفحه را قفل نکن
        var shownAt = Date.now();

        function hide() {
            var wait = Math.max(0, MIN_MS - (Date.now() - shownAt));
            setTimeout(function () { loader.classList.add('pk-hide'); }, wait);
        }
        function forceHide() { loader.classList.add('pk-hide'); }
        function show() {
            shownAt = Date.now();
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
