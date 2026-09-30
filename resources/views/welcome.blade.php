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

                // Store state for onauth callback
                window.__felagiState = new URL(payload.callback_url, window.location.origin)
                    .searchParams.get('state');

                const script = document.createElement('script');
                script.async = true;
                script.src = 'https://telegram.org/js/telegram-widget.js?22';
                script.setAttribute('data-telegram-login', payload.bot_username);
                script.setAttribute('data-size', 'large');
                script.setAttribute('data-request-access', 'write');
                script.setAttribute('data-userpic', 'true');
                // Use data-onauth (JS callback) instead of data-auth-url (popup redirect)
                script.setAttribute('data-onauth', 'onTelegramAuth(user)');
                container.appendChild(script);

                btn.disabled = false;
            } catch (e) {
                btn.disabled = false;
                console.error('SignIn error:', e);
                status.textContent = errorMsg;
            }
        }

        // Telegram widget calls this on successful auth (data-onauth mode)
        window.onTelegramAuth = async function(user) {
            const status = document.getElementById('status');
            status.textContent = 'Signing in...';

            // DEBUG
            console.log('[FELAGI] onTelegramAuth called');
            console.log('[FELAGI] user =', JSON.stringify(user, null, 2));
            console.log('[FELAGI] state =', window.__felagiState);

            try {
                // Merge state into user payload
                const payload = Object.assign({}, user, { state: window.__felagiState });
                console.log('[FELAGI] payload =', JSON.stringify(payload, null, 2));

                const res = await fetch('/api/v1/auth/telegram/widget/callback?' + new URLSearchParams(payload).toString(), {
                    method: 'GET',
                    credentials: 'same-origin',
                    headers: { 'Accept': 'application/json' }
                });

                console.log('[FELAGI] callback HTTP status =', res.status);
                const data = await res.json();
                console.log('[FELAGI] callback response =', JSON.stringify(data, null, 2));

                if (data.success && data.data && data.data.handoff_code) {
                    status.textContent = 'Exchanging token...';

                    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';
                    const exchRes = await fetch('/api/v1/auth/telegram/exchange', {
                        method: 'POST',
                        credentials: 'same-origin',
                        headers: {
                            'Accept': 'application/json',
                            'Content-Type': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest',
                            'X-CSRF-TOKEN': csrfToken
                        },
                        body: JSON.stringify({ handoff_code: data.data.handoff_code, device_name: 'web-widget' })
                    });

                    const exchData = await exchRes.json();

                    if (exchData.success && exchData.data && exchData.data.access_token) {
                        localStorage.setItem('felagi_token', exchData.data.access_token);
                        localStorage.setItem('felagi_user', JSON.stringify(exchData.data.user));
                        status.textContent = 'Success! Redirecting...';
                        window.location.href = '/profile';
                    } else {
                        status.textContent = 'Exchange failed.';
                        console.error('Exchange error:', exchData);
                    }
                } else {
                    const errCode = data.error?.code || 'UNKNOWN';
                    const errMsg = data.error?.message || 'No message';
                    status.textContent = 'Login failed: ' + errCode;
                    console.error('[FELAGI] callback failed:', errCode, errMsg, data);
                }
            } catch (e) {
                status.textContent = 'Error during sign-in.';
                console.error('onTelegramAuth error:', e);
            }
        };

        // Check if user is already signed in
        (function checkSignedIn() {
            const token = localStorage.getItem('felagi_token');
            const userJson = localStorage.getItem('felagi_user');

            if (!token || !userJson) return;

            try {
                // Already signed in → go to profile
                window.location.href = '/profile';
                return;
            } catch (e) {
                console.error('[FELAGI] Failed to parse user:', e);
            }
        })();

        function showSignedIn(user) {
            const btn = document.getElementById('signin-btn');
            const container = document.getElementById('widget-container');
            const status = document.getElementById('status');

            // Hide login widget
            if (btn) btn.style.display = 'none';
            if (container) container.innerHTML = '';

            // Show logged-in state
            if (status) {
                status.innerHTML = '\n' +
                    '<div style="padding: 20px; background: #e8f5e9; border-radius: 8px; max-width: 400px; margin: 20px auto;">\n' +
                    '  <div style="font-size: 18px; font-weight: 600; color: #1b5e20; margin-bottom: 8px;">\n' +
                    '    እንኳን ደህና መጡ, ' + (user.full_name || 'ተጠቃሚ') + '!\n' +
                    '  </div>\n' +
                    '  <div style="color: #2e7d32; margin-bottom: 16px;">\n' +
                    '    ✅ በቴሌግራም ገብተዋል\n' +
                    '  </div>\n' +
                    '  <button onclick="felagiLogout()" style="background: #c62828; color: white; padding: 10px 20px; border: none; border-radius: 6px; cursor: pointer; font-size: 14px;">\n' +
                    '    ውጣ (Logout)\n' +
                    '  </button>\n' +
                    '</div>';
            }
        }

        window.felagiLogout = async function() {
            const token = localStorage.getItem('felagi_token');

            if (token) {
                try {
                    await fetch('/api/v1/auth/logout', {
                        method: 'POST',
                        credentials: 'same-origin',
                        headers: {
                            'Accept': 'application/json',
                            'Content-Type': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest',
                            'Authorization': 'Bearer ' + token,
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
                        }
                    });
                } catch (e) {
                    console.warn('[FELAGI] Logout API failed (clearing locally)', e);
                }
            }

            localStorage.removeItem('felagi_token');
            localStorage.removeItem('felagi_user');
            window.location.href = '/';
        };

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
                    window.location.href = '/profile';
                } else {
                    console.error('Exchange failed:', data);
                }
            })
            .catch(e => console.error('Exchange error:', e));
        })();
    </script>
</body>
</html>
