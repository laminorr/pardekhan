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
    </style>
    @stack('styles')
</head>
<body>
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
</body>
</html>
