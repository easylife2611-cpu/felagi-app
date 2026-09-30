<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ __('browseNeeds') }} — {{ __('brand') }}</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", system-ui, sans-serif; background: #F4F6F8; color: #192431; min-height: 100vh; }
        header { background: #003366; color: white; padding: 16px 24px; display: flex; justify-content: space-between; align-items: center; }
        header h1 { font-size: 18px; font-weight: 600; }
        header nav { display: flex; gap: 16px; }
        header nav a { color: white; text-decoration: none; font-size: 14px; }
        header nav a:hover { text-decoration: underline; }
        main { max-width: 960px; margin: 32px auto; padding: 0 20px; }
        .card { background: white; border-radius: 12px; padding: 32px; box-shadow: 0 1px 3px rgba(0,0,0,0.08); }
        .placeholder { text-align: center; padding: 60px 20px; color: #586675; }
        .placeholder h2 { color: #003366; margin-bottom: 12px; font-size: 20px; }
        .placeholder p { margin-bottom: 20px; }
        .btn-primary { display: inline-block; background: #003366; color: white; padding: 12px 24px; border-radius: 6px; text-decoration: none; font-weight: 600; }
        .btn-primary:hover { background: #002a52; }
    </style>
</head>
<body>
    <header>
        <h1>{{ __('brand') }}</h1>
        <nav>
            <a href="/profile">{{ __('profile') }}</a>
            <a href="/browse">{{ __('browseNeeds') }}</a>
            <a href="#" onclick="felagiLogout(); return false;">{{ __('logout') }}</a>
        </nav>
    </header>

    <main>
        <div class="card">
            <div class="placeholder">
                <h2>{{ __('browseNeeds') }} — S004</h2>
                <p>Browse screen — under implementation</p>
                <p style="font-size: 13px; color: #888;">Design spec: Product_Design/Final_Screen_by_Screen_Specifications.md → S004</p>
                <a href="/profile" class="btn-primary">← {{ __('profile') }}</a>
            </div>
        </div>
    </main>

    <script>
        function felagiLogout() {
            const token = localStorage.getItem('felagi_token');
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';
            if (token) {
                fetch('/api/v1/auth/logout', {
                    method: 'POST', credentials: 'same-origin',
                    headers: { 'Accept': 'application/json', 'Content-Type': 'application/json', 'Authorization': 'Bearer ' + token, 'X-CSRF-TOKEN': csrfToken }
                }).catch(() => {});
            }
            localStorage.removeItem('felagi_token');
            localStorage.removeItem('felagi_user');
            window.location.href = '/';
        }
    </script>
</body>
</html>
