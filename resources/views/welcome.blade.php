<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ __('screenS001') }} — {{ __('brand') }}</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        html, body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", system-ui, sans-serif;
            background: #F4F6F8; color: #192431; line-height: 1.6; min-height: 100vh;
        }
        .welcome {
            min-height: 100vh; display: flex; flex-direction: column;
            align-items: center; justify-content: center;
            padding: 32px 20px; text-align: center;
        }
        .brand-lockup { max-width: 200px; height: auto; margin-bottom: 32px; }
        .purpose { font-size: 18px; max-width: 480px; color: #586675; margin-bottom: 40px; }
        .btn-primary {
            display: inline-block; background: #003366; color: white;
            padding: 14px 32px; border-radius: 8px; font-size: 16px;
            font-weight: 600; text-decoration: none; border: none; cursor: pointer;
            transition: background 0.2s ease; min-width: 240px; font-family: inherit;
        }
        .btn-primary:hover { background: #002a52; }
        .btn-primary:disabled { opacity: 0.6; cursor: not-allowed; }
        .btn-primary:focus-visible { outline: 2px solid #FF9933; outline-offset: 2px; }
        #widget-container { min-height: 50px; margin-top: 16px; display: flex; align-items: center; justify-content: center; }
        .status { margin-top: 16px; font-size: 14px; color: #586675; }
    </style>
</head>
<body>
    <main class="welcome">
        <img src="/assets/brand/felagi-lockup.svg" alt="{{ __('brand') }}" class="brand-lockup">
        <p class="purpose">{{ __('purpose') }}</p>
        <button type="button" class="btn-primary" id="signin-btn" onclick="startTelegramSignIn()">
            {{ __('signIn') }}
        </button>
        <div id="widget-container"></div>
        <div id="status" class="status"></div>
    </main>

    <script>
        async function startTelegramSignIn() {
            const btn = document.getElementById('signin-btn');
            const container = document.getElementById('widget-container');
            const status = document.getElementById('status');
            const errorMsg = @json(__('signInError'));
            btn.disabled = true;
            status.textContent = '';

            try {
                const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';
                const res = await fetch('/api/v1/auth/telegram/widget/start', {
                    method: 'POST',
                    credentials: 'same-origin',
                    headers: {
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: JSON.stringify({ return_uri: window.location.origin + '/' })
                });

                if (!res.ok) throw new Error('HTTP ' + res.status);

                const data = await res.json();
                const payload = data.data || data;

                if (!payload.bot_username || !payload.callback_url) {
                    throw new Error('Missing widget config');
                }

                btn.style.display = 'none';
                container.innerHTML = '';

                const script = document.createElement('script');
                script.async = true;
                script.src = 'https://telegram.org/js/telegram-widget.js?22';
                script.setAttribute('data-telegram-login', payload.bot_username);
                script.setAttribute('data-size', 'large');
                script.setAttribute('data-auth-url', payload.callback_url);
                script.setAttribute('data-request-access', 'write');
                script.setAttribute('data-userpic', 'true');
                container.appendChild(script);

                btn.disabled = false;
            } catch (e) {
                btn.disabled = false;
                console.error('SignIn error:', e);
                status.textContent = errorMsg;
            }
        }

        (function checkHandoff() {
            const params = new URLSearchParams(window.location.search);
            const handoffCode = params.get('handoff_code');
            if (!handoffCode) return;

            window.history.replaceState({}, '', window.location.pathname);

            fetch('/api/v1/auth/telegram/exchange', {
                method: 'POST',
                credentials: 'same-origin',
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({ handoff_code: handoffCode, device_name: 'web-widget' })
            })
            .then(r => r.json())
            .then(data => {
                if (data.success && data.data?.access_token) {
                    localStorage.setItem('felagi_token', data.data.access_token);
                    localStorage.setItem('felagi_user', JSON.stringify(data.data.user));
                    window.location.href = '/?signed_in=1';
                } else {
                    console.error('Exchange failed:', data);
                }
            })
            .catch(e => console.error('Exchange error:', e));
        })();
    </script>
</body>
</html>
