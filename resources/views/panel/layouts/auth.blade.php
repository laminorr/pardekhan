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
            --gold-1: #f0dca8; --gold-2: #d4af6a; --gold-3: #b8923f; --gold-deep: #8a6d28;
            --bg: #08080a; --surface: #121214; --surface-2: #1a1a1d;
            --border: rgba(255,255,255,0.07); --gold-border: rgba(212,175,106,0.22);
            --text: #ece9e4; --text-dim: #93908a; --text-faint: #5e5b55;
            --danger: #e2655a; --success: #5dca8f;
        }
        * { box-sizing: border-box; margin: 0; padding: 0; -webkit-tap-highlight-color: transparent; }
        body {
            font-family: 'Vazirmatn', sans-serif; background: var(--bg); color: var(--text);
            min-height: 100vh; direction: rtl; line-height: 1.6;
            display: flex; align-items: center; justify-content: center; padding: 1.5rem;
        }
        body::before {
            content: ''; position: fixed; top: -100px; left: 50%; transform: translateX(-50%);
            width: 400px; height: 400px;
            background: radial-gradient(circle, rgba(212,175,106,0.08) 0%, transparent 65%);
            pointer-events: none;
        }
        .auth-wrap { width: 100%; max-width: 400px; position: relative; z-index: 1; }
        .auth-logo { text-align: center; margin-bottom: 2rem; }
        .auth-logo .name {
            font-size: 2rem; font-weight: 900; letter-spacing: 0.5px;
            background: linear-gradient(135deg, #f0dca8 0%, #d4af6a 50%, #b8923f 100%);
            -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text;
        }
        .auth-logo .sub { font-size: 0.7rem; color: var(--gold-2); letter-spacing: 3px; margin-top: 4px; }
        .auth-card {
            background: linear-gradient(150deg, rgba(212,175,106,0.08) 0%, rgba(138,109,40,0.02) 55%), var(--surface);
            border: 1px solid var(--gold-border); border-radius: 24px; padding: 2rem 1.75rem;
        }
        .auth-card h2 { font-size: 1.3rem; font-weight: 700; color: #fff; margin-bottom: 0.4rem; }
        .auth-card .lead { font-size: 0.85rem; color: var(--text-dim); margin-bottom: 1.75rem; line-height: 1.7; }

        .field { margin-bottom: 1.1rem; }
        .field label { display: block; font-size: 0.8rem; color: var(--text-dim); margin-bottom: 0.5rem; }
        .field input {
            width: 100%; background: #0d0d0f; border: 1px solid var(--border);
            border-radius: 13px; padding: 0.9rem 1rem; color: var(--text);
            font-size: 1rem; font-family: inherit; transition: border-color 0.2s;
        }
        .field input:focus { outline: none; border-color: var(--gold-2); }

        .btn {
            display: flex; align-items: center; justify-content: center; gap: 6px;
            width: 100%; padding: 0.95rem; border-radius: 14px; border: none;
            font-family: inherit; font-size: 0.95rem; font-weight: 700; cursor: pointer;
            text-decoration: none; transition: transform 0.15s; margin-top: 0.5rem;
        }
        .btn:active { transform: scale(0.98); }
        .btn-gold { background: linear-gradient(135deg, var(--gold-1), var(--gold-3)); color: #1a1408;
            box-shadow: 0 6px 18px rgba(212,175,106,0.22); }
        .btn-ghost { background: var(--surface-2); color: var(--text); border: 1px solid var(--border); margin-top: 0.75rem; }

        .alert { padding: 0.85rem 1rem; border-radius: 12px; font-size: 0.85rem; margin-bottom: 1rem; }
        .alert-success { background: rgba(93,202,143,0.1); border: 1px solid rgba(93,202,143,0.3); color: var(--success); }
        .alert-danger { background: rgba(226,101,90,0.1); border: 1px solid rgba(226,101,90,0.3); color: var(--danger); }

        .auth-foot { text-align: center; margin-top: 1.5rem; font-size: 0.85rem; color: var(--text-dim); }
        .auth-foot a { color: var(--gold-2); text-decoration: none; font-weight: 600; }

        .status-box { text-align: center; padding: 1rem 0; }
        .status-icon {
            width: 72px; height: 72px; border-radius: 22px; margin: 0 auto 1.25rem;
            display: flex; align-items: center; justify-content: center;
        }
        .status-icon.wait { background: rgba(212,175,106,0.1); border: 1px solid var(--gold-border); color: var(--gold-1); }
        .status-icon.reject { background: rgba(226,101,90,0.1); border: 1px solid rgba(226,101,90,0.3); color: var(--danger); }
        .status-box h2 { font-size: 1.2rem; color: #fff; margin-bottom: 0.6rem; }
        .status-box p { font-size: 0.88rem; color: var(--text-dim); line-height: 1.8; }
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
