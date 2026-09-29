<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ __('screenS001') }} — {{ __('brand') }}</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        html, body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", system-ui, sans-serif;
            background: #F4F6F8;
            color: #192431;
            line-height: 1.6;
            min-height: 100vh;
        }
        .welcome {
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 32px 20px;
            text-align: center;
        }
        .brand-lockup {
            max-width: 200px;
            height: auto;
            margin-bottom: 32px;
        }
        .purpose {
            font-size: 18px;
            max-width: 480px;
            color: #586675;
            margin-bottom: 40px;
        }
        .btn-primary {
            display: inline-block;
            background: #003366;
            color: white;
            padding: 14px 32px;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            text-decoration: none;
            border: none;
            cursor: pointer;
            transition: background 0.2s ease;
            min-width: 240px;
            font-family: inherit;
        }
        .btn-primary:hover { background: #002a52; }
        .btn-primary:disabled { opacity: 0.6; cursor: not-allowed; }
        .btn-primary:focus-visible {
            outline: 2px solid #FF9933;
            outline-offset: 2px;
        }
    </style>
</head>
<body>
    <main class="welcome">
        <img
            src="/assets/brand/felagi-lockup.svg"
            alt="{{ __('brand') }}"
            class="brand-lockup"
        >
        <p class="purpose">{{ __('purpose') }}</p>
        <button
            type="button"
            class="btn-primary"
            id="signin-btn"
            onclick="startTelegramSignIn()"
        >
            {{ __('signIn') }}
        </button>
    </main>

    <script>
        async function startTelegramSignIn() {
            const btn = document.getElementById('signin-btn');
            const errorMsg = @json(__('signInError'));
            btn.disabled = true;

            try {
                const res = await fetch('/api/v1/auth/telegram/start', {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify({ return_uri: window.location.origin + '/' })
                });

                if (!res.ok) {
                    throw new Error('HTTP ' + res.status);
                }

                const data = await res.json();

                // Handle multiple possible response shapes (LOCKED design not yet
                // prescribing exact field name — accept known variants)
                const url =
                    data.url ||
                    data.auth_url ||
                    data.redirect_url ||
                    data.telegram_url ||
                    data.authorization_url;

                if (url) {
                    window.location.href = url;
                } else {
                    btn.disabled = false;
                    alert(errorMsg);
                }
            } catch (e) {
                btn.disabled = false;
                alert(errorMsg);
            }
        }
    </script>
</body>
</html>
