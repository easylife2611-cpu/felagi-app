<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ __('screenS001') }} — {{ __('purpose') }}</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        html, body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", system-ui, sans-serif;
            background: #F4F6F8;
            color: #192431;
            line-height: 1.6;
            min-height: 100vh;
        }
        .page {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 32px 20px;
        }
        .card {
            width: 100%;
            max-width: 420px;
            text-align: center;
        }
        .logo {
            width: 120px;
            height: auto;
            margin: 0 auto 24px;
            display: block;
        }
        .purpose {
            font-size: 16px;
            color: #586675;
            margin-bottom: 36px;
        }
        .field {
            display: block;
            width: 100%;
            padding: 14px 16px;
            border: 1px solid #D1D5DB;
            border-radius: 10px;
            font-size: 15px;
            font-family: inherit;
            background: #fff;
            transition: border-color 0.15s, box-shadow 0.15s;
            margin-bottom: 12px;
        }
        .field:focus {
            outline: none;
            border-color: #003366;
            box-shadow: 0 0 0 3px rgba(0, 51, 102, 0.1);
        }
        .btn {
            display: block;
            width: 100%;
            padding: 14px 16px;
            border: none;
            border-radius: 10px;
            font-size: 15px;
            font-weight: 600;
            font-family: inherit;
            cursor: pointer;
            transition: background 0.15s;
            text-decoration: none;
            text-align: center;
        }
        .btn-primary { background: #003366; color: #fff; }
        .btn-primary:hover { background: #002a52; }
        .btn-primary:disabled { opacity: 0.5; cursor: not-allowed; }
        .btn-secondary {
            background: #fff;
            color: #003366;
            border: 1px solid #D1D5DB;
        }
        .btn-secondary:hover { background: #F9FAFB; border-color: #003366; }
        .divider {
            display: flex;
            align-items: center;
            gap: 12px;
            margin: 24px 0;
            color: #586675;
            font-size: 13px;
        }
        .divider::before, .divider::after {
            content: '';
            flex: 1;
            height: 1px;
            background: #E5E7EB;
        }
        .status {
            margin-top: 12px;
            font-size: 14px;
            min-height: 20px;
            color: #586675;
        }
        .status.error { color: #DC2626; }
        .status.success { color: #16A34A; }
        .terms {
            margin-top: 32px;
            font-size: 12px;
            color: #586675;
            line-height: 1.5;
        }
        .terms a { color: #586675; text-decoration: underline; }
        .hidden { display: none !important; }

        /* OTP section */
        .otp-header {
            font-size: 22px;
            font-weight: 700;
            margin-bottom: 8px;
        }
        .otp-subtitle {
            font-size: 14px;
            color: #586675;
            margin-bottom: 28px;
        }
        .otp-subtitle strong { color: #192431; }
        .otp-inputs {
            display: grid;
            grid-template-columns: repeat(6, 1fr);
            gap: 8px;
            margin-bottom: 24px;
        }
        .otp-inputs input {
            width: 100%;
            aspect-ratio: 1;
            border: 2px solid #E5E7EB;
            border-radius: 10px;
            font-size: 22px;
            font-weight: 700;
            text-align: center;
            font-family: inherit;
            background: #fff;
            transition: border-color 0.15s;
        }
        .otp-inputs input:focus {
            outline: none;
            border-color: #003366;
            background: #F9FAFB;
        }
        .otp-inputs input.filled {
            border-color: #003366;
            background: #EFF6FF;
        }
        .resend-row {
            margin-top: 20px;
            font-size: 13px;
            color: #586675;
        }
        .resend-row button {
            background: none;
            border: none;
            color: #003366;
            font-weight: 600;
            cursor: pointer;
            font-size: 13px;
            font-family: inherit;
            text-decoration: underline;
            padding: 0;
        }
        .resend-row button:disabled {
            color: #586675;
            cursor: not-allowed;
            text-decoration: none;
        }
        .back-link {
            display: block;
            margin-top: 24px;
            font-size: 13px;
            color: #586675;
            text-decoration: none;
        }
        .back-link:hover { color: #003366; }
    
:focus-visible{outline:3px solid #1b5e20;outline-offset:2px;border-radius:6px}
#page-title:focus{outline:none}
.sr-only{position:absolute;width:1px;height:1px;padding:0;margin:-1px;overflow:hidden;clip:rect(0,0,0,0);white-space:nowrap;border:0}
</style>
</head>
<body>
    <main class="page" role="main" aria-labelledby="page-title">

        {{-- ─── Step 1: Email entry ─── --}}
        <div class="card" id="view-email">
            <img src="/assets/brand/felagi-lockup.svg" alt="{{ __('brand') }}" class="logo">
            <p class="purpose">{{ __('purpose') }}</p>

            <input
                type="email"
                id="email-input"
                class="field"
                placeholder="your@email.com"
                autocomplete="email"
                inputmode="email"
            >
            <button type="button" class="btn btn-primary" id="email-continue">
                Continue with Email
            </button>

            <div class="divider">OR</div>

            <button type="button" class="btn btn-secondary" id="telegram-continue">
                ✈️ Continue with Telegram
            </button>

            <div id="telegram-widget-container" style="margin-top: 16px;"></div>
            <div class="status" id="status"></div>

            <p class="terms">
                By continuing, you agree to our
                <a href="/docs/privacy/TERMS_OF_SERVICE.md">{{ __('terms') }}</a>
                and
                <a href="/docs/privacy/PRIVACY_POLICY.md">Privacy Policy</a>.
            </p>
        </div>

        {{-- ─── Step 2: OTP code entry ─── --}}
        <div class="card hidden" id="view-otp">
            <h1 class="otp-header" id="page-title" tabindex="-1">{{ __('checkEmail') }}</h1>
            <p class="otp-subtitle">
                We sent a 6-digit code to<br>
                <strong id="otp-email-display"></strong>
            </p>

            <div class="otp-inputs" id="otp-inputs">
                <input type="text" inputmode="numeric" maxlength="1" autocomplete="one-time-code" data-index="0">
                <input type="text" inputmode="numeric" maxlength="1" data-index="1">
                <input type="text" inputmode="numeric" maxlength="1" data-index="2">
                <input type="text" inputmode="numeric" maxlength="1" data-index="3">
                <input type="text" inputmode="numeric" maxlength="1" data-index="4">
                <input type="text" inputmode="numeric" maxlength="1" data-index="5">
            </div>

            <button type="button" class="btn btn-primary" id="otp-verify" disabled>
                Verify
            </button>

            <div class="status" id="otp-status"></div>

            <div class="resend-row">
                Didn't receive it?
                <button type="button" id="resend-btn" disabled>
                    Resend code
                </button>
                <span id="resend-timer"></span>
            </div>

            <a href="#" class="back-link" id="back-to-email">← Use a different email</a>
        </div>

    </main>

    <script>
    (function() {
        const csrf = document.querySelector('meta[name="csrf-token"]')?.content || '';
        const $ = id => document.getElementById(id);

        const views = {
            email: $('view-email'),
            otp:   $('view-otp'),
        };

        let currentEmail = '';
        let resendCooldown = 0;

        // ─── Email flow ───

        $('email-continue').addEventListener('click', async () => {
            const email = $('email-input').value.trim();
            const status = $('status');

            if (! email || ! /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
                status.textContent = '{{ __("emailInvalid") }}';
                status.className = 'status error';
                return;
            }

            const btn = $('email-continue');
            btn.disabled = true;
            status.textContent = '{{ __("sendingCode") }}';
            status.className = 'status';

            try {
                const res = await fetch('/api/v1/auth/email/request', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrf,
                    },
                    body: JSON.stringify({ email }),
                });

                const data = await res.json();

                if (! res.ok) {
                    throw new Error(data.message || 'Failed to send code');
                }

                currentEmail = email;
                status.textContent = '';
                showOtpView(email);
                startResendCooldown();

            } catch (e) {
                status.textContent = e.message;
                status.className = 'status error';
                btn.disabled = false;
            }
        });

        // Enter key on email input
        $('email-input').addEventListener('keydown', e => {
            if (e.key === 'Enter') $('email-continue').click();
        });

        function showOtpView(email) {
            views.email.classList.add('hidden');
            views.otp.classList.remove('hidden');
            $('otp-email-display').textContent = email;
            $('otp-inputs').querySelectorAll('input').forEach(i => i.value = '');
            $('otp-status').textContent = '';
            $('otp-verify').disabled = true;
            setTimeout(() => $('otp-inputs').querySelector('input[data-index="0"]').focus(), 100);
        }

        $('back-to-email').addEventListener('click', e => {
            e.preventDefault();
            views.otp.classList.add('hidden');
            views.email.classList.remove('hidden');
            $('email-continue').disabled = false;
            $('email-input').focus();
        });

        // ─── OTP input handling ───

        const otpInputs = [...$('otp-inputs').querySelectorAll('input')];

        otpInputs.forEach((input, idx) => {
            input.addEventListener('input', e => {
                const v = e.target.value.replace(/\D/g, '');
                e.target.value = v.slice(0, 1);
                e.target.classList.toggle('filled', e.target.value !== '');

                if (e.target.value && idx < 5) {
                    otpInputs[idx + 1].focus();
                }
                updateVerifyButton();

                if (getOtpCode().length === 6) {
                    $('otp-verify').focus();
                }
            });

            input.addEventListener('keydown', e => {
                if (e.key === 'Backspace' && ! e.target.value && idx > 0) {
                    otpInputs[idx - 1].focus();
                }
                if (e.key === 'ArrowLeft' && idx > 0) otpInputs[idx - 1].focus();
                if (e.key === 'ArrowRight' && idx < 5) otpInputs[idx + 1].focus();
                if (e.key === 'Enter' && ! $('otp-verify').disabled) $('otp-verify').click();
            });

            input.addEventListener('paste', e => {
                e.preventDefault();
                const paste = (e.clipboardData || window.clipboardData).getData('text').replace(/\D/g, '').slice(0, 6);
                paste.split('').forEach((c, i) => {
                    if (otpInputs[i]) {
                        otpInputs[i].value = c;
                        otpInputs[i].classList.toggle('filled', c !== '');
                    }
                });
                const nextEmpty = otpInputs.findIndex(i => ! i.value);
                (nextEmpty >= 0 ? otpInputs[nextEmpty] : otpInputs[5]).focus();
                updateVerifyButton();
            });
        });

        function getOtpCode() {
            return otpInputs.map(i => i.value).join('');
        }

        function updateVerifyButton() {
            $('otp-verify').disabled = getOtpCode().length !== 6;
        }

        // ─── Verify OTP ───

        $('otp-verify').addEventListener('click', () => {
            const code = getOtpCode();
            if (code.length !== 6) return;

            // Submit as HTML form (creates web session)
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = '/auth/email/verify-web';
            form.style.display = 'none';

            const fields = {
                _token: csrf,
                email:  currentEmail,
                code:   code,
            };

            for (const [k, v] of Object.entries(fields)) {
                const input = document.createElement('input');
                input.name = k;
                input.value = v;
                form.appendChild(input);
            }

            document.body.appendChild(form);
            form.submit();
        });

        // ─── Resend ───

        function startResendCooldown() {
            resendCooldown = 30;
            const btn = $('resend-btn');
            const timer = $('resend-timer');
            btn.disabled = true;
            timer.textContent = '';

            const interval = setInterval(() => {
                resendCooldown--;
                if (resendCooldown <= 0) {
                    clearInterval(interval);
                    btn.disabled = false;
                    timer.textContent = '';
                } else {
                    timer.textContent = `(${resendCooldown}s)`;
                }
            }, 1000);
        }

        $('resend-btn').addEventListener('click', async () => {
            if (resendCooldown > 0) return;

            try {
                const res = await fetch('/api/v1/auth/email/request', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrf,
                    },
                    body: JSON.stringify({ email: currentEmail }),
                });

                if (! res.ok) throw new Error('Failed to resend');

                startResendCooldown();
                $('otp-status').textContent = '{{ __("newCodeSent") }}';
                $('otp-status').className = 'status success';

            } catch (e) {
                $('otp-status').textContent = e.message;
                $('otp-status').className = 'status error';
            }
        });

        // ─── Telegram flow (secondary) ───

        $('telegram-continue').addEventListener('click', async () => {
            const status = $('status');
            const btn = $('telegram-continue');
            btn.disabled = true;
            status.textContent = '{{ __("connectingTelegram") }}';
            status.className = 'status';

            try {
                const res = await fetch('/api/v1/auth/telegram/widget/start', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrf,
                    },
                    body: JSON.stringify({ return_uri: window.location.origin + '/' }),
                });

                const data = await res.json();
                if (! res.ok) throw new Error(data.message || 'Failed');

                const payload = data.data || data;
                if (! payload.bot_username) throw new Error('Missing widget config');

                btn.style.display = 'none';

                window.__felagiState = new URL(payload.callback_url, window.location.origin)
                    .searchParams.get('state');

                const container = $('telegram-widget-container');
                container.innerHTML = '';

                const script = document.createElement('script');
                script.async = true;
                script.src = 'https://telegram.org/js/telegram-widget.js?22';
                script.setAttribute('data-telegram-login', payload.bot_username);
                script.setAttribute('data-size', 'large');
                script.setAttribute('data-request-access', 'write');
                script.setAttribute('data-userpic', 'true');
                script.setAttribute('data-onauth', 'onFelagiTelegramAuth(user)');
                container.appendChild(script);

                status.textContent = '';

            } catch (e) {
                status.textContent = e.message;
                status.className = 'status error';
                btn.disabled = false;
            }
        });

        window.onFelagiTelegramAuth = async function(user) {
            const status = $('status');
            status.textContent = '{{ __("signingIn") }}';
            status.className = 'status';

            try {
                const payload = Object.assign({}, user, { state: window.__felagiState });

                const res = await fetch(
                    '/api/v1/auth/telegram/widget/callback?' + new URLSearchParams(payload).toString(),
                    {
                        method: 'GET',
                        credentials: 'same-origin',
                        headers: { 'Accept': 'application/json' },
                    }
                );

                const data = await res.json();

                if (data.success && data.data?.redirect_url) {
                    window.location.href = data.data.redirect_url;
                } else if (res.ok && data.data?.handoff_code) {
                    window.location.href = '/?handoff_code=' + encodeURIComponent(data.data.handoff_code);
                } else {
                    status.textContent = '{{ __("signInFailed") }}';
                    status.className = 'status error';
                }
            } catch (e) {
                status.textContent = '{{ __("signInFailed") }}';
                status.className = 'status error';
            }
        };
    })();
    </script>
</body>
</html>
