<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>@yield('title', 'پرده‌خوان')</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Vazirmatn:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        :root {
            --gold-1: #f0dca8;
            --gold-2: #d4af6a;
            --gold-3: #b8923f;
            --gold-deep: #8a6d28;
            --bg: #08080a;
            --surface: #121214;
            --surface-2: #1a1a1d;
            --border: rgba(255,255,255,0.07);
            --gold-border: rgba(212,175,106,0.22);
            --text: #ece9e4;
            --text-dim: #93908a;
            --text-faint: #5e5b55;
            --danger: #e2655a;
            --success: #5dca8f;
        }
        * { box-sizing: border-box; margin: 0; padding: 0; -webkit-tap-highlight-color: transparent; }
        body {
            font-family: 'Vazirmatn', sans-serif;
            background: var(--bg);
            color: var(--text);
            min-height: 100vh;
            direction: rtl;
            line-height: 1.6;
        }
        .phone {
            max-width: 430px; margin: 0 auto; min-height: 100vh;
            background: var(--bg); position: relative; overflow-x: hidden;
        }
        .phone::before {
            content: ''; position: fixed; top: -150px; right: calc(50% - 215px);
            width: 350px; height: 350px;
            background: radial-gradient(circle, rgba(212,175,106,0.07) 0%, transparent 65%);
            pointer-events: none; z-index: 0;
        }
        .wrap { padding: 1.5rem 1.25rem 2.5rem; position: relative; z-index: 1; }
        svg { display: block; }

        /* هدر */
        .topbar { display: flex; align-items: center; justify-content: space-between; margin-bottom: 1.75rem; }
        .brand-text .name { font-size: 1.35rem; font-weight: 800; color: #fff; line-height: 1.1;
            background: linear-gradient(135deg, #f0dca8 0%, #d4af6a 50%, #b8923f 100%);
            -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text; }
        .brand-text .sub { font-size: 0.62rem; color: var(--gold-2); letter-spacing: 2.5px; margin-top: 2px; }
        .top-actions { display: flex; gap: 0.6rem; }
        .icon-btn {
            width: 42px; height: 42px; border-radius: 13px; background: var(--surface);
            border: 1px solid var(--border); display: flex; align-items: center; justify-content: center;
            color: var(--text-dim); position: relative; text-decoration: none;
        }
        .icon-btn .ndot { position: absolute; top: 10px; right: 11px; width: 7px; height: 7px;
            border-radius: 50%; background: var(--gold-2); border: 2px solid var(--surface); }

        /* عنوان صفحه */
        .page-head { margin-bottom: 1.5rem; }
        .page-title { font-size: 1.5rem; font-weight: 800; color: #fff; }
        .page-sub { font-size: 0.85rem; color: var(--text-dim); margin-top: 0.25rem; }

        /* کارت عمومی */
        .card {
            background: var(--surface); border: 1px solid var(--border);
            border-radius: 20px; padding: 1.25rem; margin-bottom: 1rem;
        }
        .card-gold {
            background: linear-gradient(150deg, rgba(212,175,106,0.13) 0%, rgba(138,109,40,0.03) 55%), var(--surface);
            border: 1px solid var(--gold-border);
        }

        /* عنوان بخش */
        .section-head { display: flex; align-items: center; justify-content: space-between; margin: 1.75rem 0 1rem; }
        .section-title { font-size: 0.95rem; font-weight: 700; color: #fff; display: flex; align-items: center; gap: 0.55rem; }
        .section-title .bar { width: 3px; height: 16px; background: linear-gradient(var(--gold-1),var(--gold-3)); border-radius: 2px; }
        .see-all { font-size: 0.72rem; color: var(--gold-2); text-decoration: none; display: flex; align-items: center; gap: 3px; }

        /* دکمه‌ها */
        .btn {
            display: flex; align-items: center; justify-content: center; gap: 6px;
            width: 100%; padding: 0.9rem; border-radius: 14px; border: none;
            font-family: inherit; font-size: 0.95rem; font-weight: 700; cursor: pointer;
            text-decoration: none; transition: transform 0.15s;
        }
        .btn:active { transform: scale(0.98); }
        .btn-gold {
            background: linear-gradient(135deg, var(--gold-1), var(--gold-3));
            color: #1a1408; box-shadow: 0 6px 18px rgba(212,175,106,0.22);
        }
        .btn-ghost { background: var(--surface-2); color: var(--text); border: 1px solid var(--border); }

        /* فرم */
        .field { margin-bottom: 1.1rem; }
        .field label { display: block; font-size: 0.8rem; color: var(--text-dim); margin-bottom: 0.5rem; }
        .field input, .field textarea, .field select {
            width: 100%; background: #0d0d0f; border: 1px solid var(--border);
            border-radius: 13px; padding: 0.85rem 1rem; color: var(--text);
            font-size: 1rem; font-family: inherit; transition: border-color 0.2s;
        }
        .field input:focus, .field textarea:focus, .field select:focus { outline: none; border-color: var(--gold-2); }

        /* پیام‌ها */
        .alert { padding: 0.85rem 1rem; border-radius: 12px; font-size: 0.85rem; margin-bottom: 1rem; }
        .alert-success { background: rgba(93,202,143,0.1); border: 1px solid rgba(93,202,143,0.3); color: var(--success); }
        .alert-danger { background: rgba(226,101,90,0.1); border: 1px solid rgba(226,101,90,0.3); color: var(--danger); }

        /* لینک پایین */
        .back-link {
            display: flex; align-items: center; justify-content: center; gap: 6px;
            padding: 0.9rem; background: var(--surface); border: 1px solid var(--border);
            border-radius: 14px; color: var(--text-dim); text-decoration: none;
            font-size: 0.9rem; margin-top: 1rem;
        }
    </style>
    @stack('styles')
</head>
<body>
    <div class="phone">
        <div class="wrap">
            @yield('content')
        </div>
    </div>
    @stack('scripts')
</body>
</html>
