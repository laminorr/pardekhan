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
        .auth-logo .sub { font-size: 0.68rem; color: var(--ink-faint); letter-spacing: 3px; margin-top: 3px; font-weight: 600; }

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
    </style>
    @stack('styles')
</head>
<body>
    <div class="auth-wrap">
        <div class="auth-logo">
            <div class="name">پرده‌خوان</div>
            <div class="sub">باشگاه اعضا</div>
        </div>
        @yield('content')
    </div>
</body>
</html>
