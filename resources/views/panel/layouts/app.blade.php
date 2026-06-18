<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'پنل پرده‌خوان')</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: Tahoma, sans-serif; background: #0f0f0f; color: #e5e5e5; min-height: 100vh; direction: rtl; }
        .panel-container { max-width: 480px; margin: 0 auto; padding: 2rem 1rem; }
        .panel-logo { text-align: center; margin-bottom: 2rem; }
        .panel-logo h1 { font-size: 1.8rem; color: #f59e0b; }
        .panel-logo p { color: #888; font-size: 0.85rem; margin-top: 0.3rem; }
        .panel-card { background: #1a1a1a; border: 1px solid #2a2a2a; border-radius: 16px; padding: 2rem; }
        .panel-card h2 { font-size: 1.2rem; margin-bottom: 1.5rem; color: #fff; }
        .form-group { margin-bottom: 1.2rem; }
        .form-group label { display: block; font-size: 0.85rem; color: #aaa; margin-bottom: 0.4rem; }
        .form-group input { width: 100%; background: #111; border: 1px solid #333; border-radius: 10px; padding: 0.75rem 1rem; color: #fff; font-size: 1rem; font-family: inherit; }
        .form-group input:focus { outline: none; border-color: #f59e0b; }
        .error-msg { color: #ef4444; font-size: 0.8rem; margin-top: 0.3rem; }
        .success-msg { color: #22c55e; font-size: 0.85rem; background: #052e16; border: 1px solid #166534; border-radius: 8px; padding: 0.75rem 1rem; margin-bottom: 1rem; }
        .btn { width: 100%; padding: 0.85rem; border-radius: 10px; border: none; font-size: 1rem; font-family: inherit; cursor: pointer; }
        .btn-primary { background: #f59e0b; color: #000; font-weight: bold; margin-top: 0.5rem; }
        .btn-secondary { background: #2a2a2a; color: #ddd; margin-top: 0.75rem; }
        .panel-link { text-align: center; margin-top: 1.5rem; font-size: 0.85rem; color: #888; }
        .panel-link a { color: #f59e0b; text-decoration: none; }
        .status-icon { text-align: center; font-size: 3rem; margin-bottom: 1rem; }
        .status-text { text-align: center; color: #aaa; line-height: 1.8; }
        .status-text strong { color: #fff; }
    </style>
</head>
<body>
    <div class="panel-container">
        <div class="panel-logo">
            <h1>پرده‌خوان</h1>
            <p>پنل اعضا</p>
        </div>
        @yield('content')
    </div>
</body>
</html>
