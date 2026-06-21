<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>@yield('title', 'پرده‌خوان')</title>
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
            background: #eceeec;
            color: var(--ink);
            min-height: 100vh; direction: rtl; line-height: 1.6;
            -webkit-font-smoothing: antialiased;
        }
        .phone {
            max-width: 430px; margin: 0 auto; min-height: 100vh;
            position: relative; overflow-x: hidden; padding-bottom: 92px;
            background: linear-gradient(180deg, #f3f5f4 0%, #eef1ef 100%);
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
        .card { background: var(--surface); border: 1px solid var(--border);
            border-radius: 22px; padding: 1.3rem; margin-bottom: 1rem;
            box-shadow: 0 2px 14px rgba(40,60,50,0.04); }

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
    </style>
    @stack('styles')
</head>
<body>
    <div class="phone">
        <div class="wrap">
            @yield('content')
        </div>
        @yield('nav')
    </div>
    @stack('scripts')
</body>
</html>
