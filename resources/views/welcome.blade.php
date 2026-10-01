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
        .errors {
            margin-top: 20px; padding: 12px 16px; border-radius: 8px;
            background: #fee2e2; border: 1px solid #fca5a5; color: #991b1b;
            font-size: 13px; max-width: 480px;
        }
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

        @if ($errors->any())
            <div class="errors" role="alert">
                @foreach ($errors->all() as $error)
                    <div>{{ $error }}</div>
                @endforeach
            </div>
        @endif
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

                window.__felagiState = new URL(payload.callback_url, window.location.origin)
                    .searchParams.get('state');

                const script = document.createElement('script');
                script.async = true;
                script.src = 'https://telegram.org/js/telegram-widget.js?22';
                script.setAttribute('data-telegram-login', payload.bot_username);
                script.setAttribute('data-size', 'large');
                script.setAttribute('data-request-access', 'write');
                script.setAttribute('data-userpic', 'true');
                script.setAttribute('data-onauth', 'onTelegramAuth(user)');
                container.appendChild(script);

                btn.disabled = false;
            } catch (e) {
                btn.disabled = false;
                console.error('SignIn error:', e);
                status.textContent = errorMsg;
            }
        }

        window.onTelegramAuth = async function(user) {
            const status = document.getElementById('status');
            status.textContent = 'Signing in...';

            try {
                const payload = Object.assign({}, user, { state: window.__felagiState });

                const res = await fetch('/api/v1/auth/telegram/widget/callback?' + new URLSearchParams(payload).toString(), {
                    method: 'GET',
                    credentials: 'same-origin',
                    headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
                });

                const data = await res.json();
                if (data.success && data.data && data.data.redirect_url) {
                    window.location.href = data.data.redirect_url;
                } else if (res.ok) {
                    window.location.href = '/browse';
                } else {
                    status.textContent = 'Sign-in failed. Please try again.';
                }
            } catch (e) {
                console.error('Callback error:', e);
                status.textContent = 'Sign-in failed. Please try again.';
            }
        };
    </script>

    {{-- L294/L296 — Telegram widget iframe title (accessibility).
         Telegram injects an iframe without a title. We observe it and
         set an accessible name. Kept from L294. --}}
    <script id="s002-widget-title-observer">
        (function () {
            function tagIframe() {
                const iframes = document.querySelectorAll('iframe[id^="telegram-login-"]');
                iframes.forEach(function (iframe) {
                    if (!iframe.title) {
                        iframe.title = 'Telegram sign-in';
                        iframe.setAttribute('aria-label', 'Telegram sign-in');
                    }
                });
            }
            tagIframe();
            const observer = new MutationObserver(tagIframe);
            observer.observe(document.body, { childList: true, subtree: true });
            setTimeout(function () { observer.disconnect(); }, 30000);
        })();
    </script>
</body>
</html>
