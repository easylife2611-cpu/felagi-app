<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ __('screenS001') }} — {{ __('purpose') }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Ethiopic:wght@400;500;600;700;800;900&family=Inter:wght@400;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root{
            --navy-950:#001a33;--navy-900:#002b52;--navy-800:#003366;--navy-700:#0b4d83;
            --orange-700:#b35c00;--orange-600:#e8851d;--orange-500:#FF9933;--orange-400:#ffb366;--orange-100:#fff2e0;
            --ink-900:#0d1a2b;--ink-700:#132238;--ink-500:#3c4d61;--ink-300:#5f7185;--ink-200:#8fa0b3;
            --line-100:#eef2f6;--line-200:#e3e9ef;--line-300:#d5dde6;
            --surface:#fff;--canvas:#f4f7fa;--canvas-2:#eef3f8;
            --success-600:#0e6b34;--danger-600:#a32e21;
            --r-sm:12px;--r-md:16px;--r-lg:22px;--r-pill:999px;
            --f-am:'Noto Sans Ethiopic','Inter',system-ui,sans-serif;
            --f-en:'Inter','Noto Sans Ethiopic',system-ui,sans-serif;
            --ease:cubic-bezier(.16,.84,.44,1);
            --ease-out:cubic-bezier(.22,1,.36,1);
        }
        *{box-sizing:border-box;margin:0;padding:0}
        html,body{background:linear-gradient(160deg,#0a1626 0%,#001a33 50%,#00101f 100%);min-height:100vh;font-family:var(--f-am);color:var(--ink-900);line-height:1.55;-webkit-font-smoothing:antialiased}
        body{display:flex;align-items:center;justify-content:center;padding:20px}
        a{color:inherit;text-decoration:none}
        button{font-family:inherit;cursor:pointer;border:0;background:transparent;color:inherit}
        input{font-family:inherit;font-size:inherit}
        :focus-visible{outline:3px solid var(--orange-500);outline-offset:2px;border-radius:6px}
        .hidden{display:none!important}
        .sr-only{position:absolute;width:1px;height:1px;padding:0;margin:-1px;overflow:hidden;clip:rect(0,0,0,0);white-space:nowrap;border:0}

        .wrap{width:100%;max-width:480px;animation:fadeUp .6s var(--ease-out)}
        @keyframes fadeUp{from{opacity:0;transform:translateY(20px)}to{opacity:1;transform:translateY(0)}}

        .card{background:var(--surface);border-radius:28px;overflow:hidden;box-shadow:0 32px 80px rgba(0,0,0,.35),0 12px 32px rgba(0,0,0,.2);animation:fadeUp .5s var(--ease-out)}

        .hero{background:radial-gradient(ellipse at top right,rgba(255,153,51,.18),transparent 55%),radial-gradient(ellipse at bottom left,rgba(26,107,176,.35),transparent 60%),linear-gradient(140deg,var(--navy-950),var(--navy-800));color:#fff;padding:36px 26px 32px;position:relative;overflow:hidden}
        .hero::after{content:'';position:absolute;inset:0;background:radial-gradient(ellipse at top right,rgba(255,153,51,.1),transparent 55%);pointer-events:none}
        .hero>*{position:relative;z-index:1}
        .hero-sm{padding:32px 26px 28px;text-align:center}

        .brand{display:flex;align-items:center;gap:10px;font-size:19px;font-weight:900;letter-spacing:-.02em;margin-bottom:22px}
        .hero-sm .brand{justify-content:center;margin-bottom:16px}
        .brand svg{flex-shrink:0}

        .eyebrow{font-size:12px;font-weight:700;letter-spacing:.12em;text-transform:uppercase;color:var(--orange-400);margin-bottom:8px;font-family:var(--f-en)}
        .hero-sm .eyebrow{text-align:center}

        .hero h1{font-size:32px;font-weight:900;letter-spacing:-.03em;line-height:1.15;margin-bottom:12px}
        .hero-sm h1{font-size:22px;text-align:center;margin-bottom:8px}

        .hero p{font-size:14px;opacity:.82;line-height:1.6;margin-bottom:22px;font-weight:500}
        .hero-sm p{font-size:13px;text-align:center;margin-bottom:0}

        .stats{display:grid;grid-template-columns:repeat(3,1fr);gap:8px;background:rgba(255,255,255,.08);border:1px solid rgba(255,255,255,.12);border-radius:var(--r-md);padding:14px;backdrop-filter:blur(20px);-webkit-backdrop-filter:blur(20px)}
        .stat{text-align:center}
        .stat .v{font-size:19px;font-weight:900;letter-spacing:-.02em;font-family:var(--f-en)}
        .stat .l{font-size:10.5px;opacity:.72;margin-top:2px;font-weight:600}

        .actions{padding:24px 22px 26px;display:flex;flex-direction:column;gap:10px}

        .btn{display:flex;align-items:center;justify-content:center;gap:8px;width:100%;padding:14px 18px;border-radius:var(--r-md);font-size:15px;font-weight:700;font-family:inherit;cursor:pointer;transition:all .18s var(--ease);text-align:center;text-decoration:none;border:1.5px solid transparent}
        .btn-primary{background:linear-gradient(135deg,var(--orange-500),var(--orange-600));color:#fff;box-shadow:0 8px 20px rgba(255,153,51,.28)}
        .btn-primary:hover{transform:translateY(-1px);box-shadow:0 12px 24px rgba(255,153,51,.36)}
        .btn-navy{background:var(--navy-800);color:#fff}
        .btn-navy:hover{background:var(--navy-700)}
        .btn-white{background:#fff;color:var(--navy-800);border-color:var(--line-300)}
        .btn-white:hover{background:var(--canvas-2);border-color:var(--navy-700)}
        .btn-link{background:transparent;color:var(--ink-500);font-weight:600;font-size:13.5px;padding:10px}
        .btn-link:hover{color:var(--navy-800)}
        .btn:disabled{opacity:.55;cursor:not-allowed;transform:none!important}

        .divider{display:flex;align-items:center;gap:12px;margin:6px 0;color:var(--ink-300);font-size:12px;font-weight:700;letter-spacing:.06em;text-transform:uppercase;font-family:var(--f-en)}
        .divider::before,.divider::after{content:'';flex:1;height:1px;background:var(--line-200)}

        .field{display:block;width:100%;padding:14px 16px;border:1.5px solid var(--line-300);border-radius:var(--r-md);font-size:15px;font-family:inherit;background:#fff;transition:border-color .15s,box-shadow .15s}
        .field:focus{outline:none;border-color:var(--navy-800);box-shadow:0 0 0 3px rgba(0,51,102,.1)}

        .status{margin-top:8px;font-size:13.5px;min-height:20px;text-align:center;color:var(--ink-500)}
        .status.error{color:var(--danger-600)}
        .status.success{color:var(--success-600)}

        .otp-inputs{display:grid;grid-template-columns:repeat(6,1fr);gap:8px;margin-bottom:16px}
        .otp-inputs input{width:100%;aspect-ratio:1;border:2px solid var(--line-200);border-radius:10px;font-size:20px;font-weight:800;text-align:center;font-family:var(--f-en);background:#fff;transition:border-color .15s,background .15s}
        .otp-inputs input:focus{outline:none;border-color:var(--navy-800);background:var(--canvas-2)}
        .otp-inputs input.filled{border-color:var(--navy-800);background:#eff6ff}

        .resend-row{margin-top:6px;font-size:13px;color:var(--ink-500);text-align:center}
        .resend-row button{color:var(--navy-800);font-weight:700;text-decoration:underline;font-size:13px;padding:0}
        .resend-row button:disabled{color:var(--ink-200);cursor:not-allowed;text-decoration:none}

        .legal{margin-top:16px;font-size:11.5px;color:var(--ink-300);line-height:1.5;text-align:center}
        .legal a{color:var(--ink-500);text-decoration:underline}

        @media (max-width:420px){
            body{padding:12px}
            .hero{padding:28px 20px 24px}
            .hero h1{font-size:26px}
            .actions{padding:20px 18px 22px}
        }
    </style>
</head>
<body>
<main class="wrap" role="main" aria-labelledby="page-title">

<!-- VIEW 1: Premium Welcome -->
<div id="view-welcome" class="card">
    <div class="hero">
        <div class="brand">
            <svg width="32" height="32" viewBox="0 0 32 32" fill="none" aria-hidden="true"><circle cx="16" cy="16" r="15" fill="url(#felagiGradW)"/><path d="M9 10 L9 22 M9 12 L18 12 M9 17 L16 17 M18 12 L18 22" stroke="#ffffff" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"/><circle cx="23" cy="10" r="3" fill="#FF9933"/><path d="M20 20 L25 25" stroke="#ffffff" stroke-width="2.2" stroke-linecap="round" opacity="0.6"/><defs><linearGradient id="felagiGradW" x1="0" y1="0" x2="32" y2="32"><stop offset="0" stop-color="#00264d"/><stop offset="1" stop-color="#0b4d83"/></linearGradient></defs></svg>
            <span>{{ __('brand') }}</span>
        </div>
        <div class="eyebrow">{{ __('welcomeEyebrow') }}</div>
        <h1 id="page-title" tabindex="-1">{!! nl2br(e(__('welcomeHeroTitle'))) !!}</h1>
        <p>{{ __('welcomeHeroBody') }}</p>
        <div class="stats">
            <div class="stat"><div class="v">2.4K+</div><div class="l">{{ __('statNeeds') }}</div></div>
            <div class="stat"><div class="v">890+</div><div class="l">{{ __('statProviders') }}</div></div>
            <div class="stat"><div class="v">4.8★</div><div class="l">{{ __('statRating') }}</div></div>
        </div>
    </div>
    <div class="actions">
        <a href="/needs/new" class="btn btn-primary">＋ {{ __('createNeed') }}</a>
        <a href="/browse" class="btn btn-white">{{ __('browseNeeds') }} →</a>
        <div class="divider">{{ __('signInMethods') }}</div>
        <button type="button" id="telegram-continue" class="btn btn-navy">✈️ {{ __('signInTelegram') }}</button>
        <button type="button" id="email-continue" class="btn btn-white">{{ __('signInEmail') }}</button>
        <a href="/browse" class="btn btn-link">{{ __('continueAsGuest') }}</a>
        <div id="telegram-widget-container"></div>
        <div class="status" id="status"></div>
        <p class="legal"><a href="/docs/privacy/TERMS_OF_SERVICE.md">{{ __('terms') }}</a> · <a href="/docs/privacy/PRIVACY_POLICY.md">{{ __('privacy') ?? 'Privacy' }}</a></p>
    </div>
</div>

<!-- VIEW 2: Email Entry -->
<div id="view-email" class="card hidden">
    <div class="hero hero-sm">
        <div class="brand">
            <svg width="28" height="28" viewBox="0 0 32 32" fill="none" aria-hidden="true"><circle cx="16" cy="16" r="15" fill="url(#felagiGradE)"/><path d="M9 10 L9 22 M9 12 L18 12 M9 17 L16 17 M18 12 L18 22" stroke="#ffffff" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"/><circle cx="23" cy="10" r="3" fill="#FF9933"/><defs><linearGradient id="felagiGradE" x1="0" y1="0" x2="32" y2="32"><stop offset="0" stop-color="#00264d"/><stop offset="1" stop-color="#0b4d83"/></linearGradient></defs></svg>
            <span>{{ __('brand') }}</span>
        </div>
        <div class="eyebrow">{{ __('signInEmail') }}</div>
        <h1>{{ __('purpose') }}</h1>
    </div>
    <div class="actions">
        <input type="email" id="email-input" class="field" placeholder="your@email.com" autocomplete="email" inputmode="email">
        <button type="button" id="email-submit" class="btn btn-primary">{{ __('continue') }}</button>
        <a href="#" id="email-back" class="btn btn-link">← {{ __('back') }}</a>
        <div class="status" id="email-status"></div>
    </div>
</div>

<!-- VIEW 3: OTP -->
<div id="view-otp" class="card hidden">
    <div class="hero hero-sm">
        <div class="brand">
            <svg width="28" height="28" viewBox="0 0 32 32" fill="none" aria-hidden="true"><circle cx="16" cy="16" r="15" fill="url(#felagiGradO)"/><path d="M9 10 L9 22 M9 12 L18 12 M9 17 L16 17 M18 12 L18 22" stroke="#ffffff" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"/><circle cx="23" cy="10" r="3" fill="#FF9933"/><defs><linearGradient id="felagiGradO" x1="0" y1="0" x2="32" y2="32"><stop offset="0" stop-color="#00264d"/><stop offset="1" stop-color="#0b4d83"/></linearGradient></defs></svg>
            <span>{{ __('brand') }}</span>
        </div>
        <h1 id="otp-title" tabindex="-1">{{ __('checkEmail') }}</h1>
        <p>We sent a 6-digit code to <strong id="otp-email-display"></strong></p>
    </div>
    <div class="actions">
        <div class="otp-inputs" id="otp-inputs">
            <input type="text" inputmode="numeric" maxlength="1" autocomplete="one-time-code" data-index="0">
            <input type="text" inputmode="numeric" maxlength="1" data-index="1">
            <input type="text" inputmode="numeric" maxlength="1" data-index="2">
            <input type="text" inputmode="numeric" maxlength="1" data-index="3">
            <input type="text" inputmode="numeric" maxlength="1" data-index="4">
            <input type="text" inputmode="numeric" maxlength="1" data-index="5">
        </div>
        <button type="button" id="otp-verify" class="btn btn-primary" disabled>{{ __('confirm') }}</button>
        <div class="resend-row">
            <button type="button" id="resend-btn" disabled>Resend code</button>
            <span id="resend-timer"></span>
        </div>
        <a href="#" id="otp-back" class="btn btn-link">← {{ __('back') }}</a>
        <div class="status" id="otp-status"></div>
    </div>
</div>

</main>
<script>
(function() {
    const csrf = document.querySelector('meta[name="csrf-token"]')?.content || '';
    const $ = id => document.getElementById(id);
    const views = {
        welcome: $('view-welcome'),
        email: $('view-email'),
        otp: $('view-otp'),
    };
    let currentEmail = '';
    let resendCooldown = 0;

    function show(name) {
        Object.values(views).forEach(v => v.classList.add('hidden'));
        views[name].classList.remove('hidden');
    }

    // ─── Email flow ───
    $('email-continue').addEventListener('click', () => show('email'));
    $('email-back').addEventListener('click', e => { e.preventDefault(); show('welcome'); });
    $('email-input').addEventListener('keydown', e => { if (e.key === 'Enter') $('email-submit').click(); });

    $('email-submit').addEventListener('click', async () => {
        const email = $('email-input').value.trim();
        const status = $('email-status');
        if (!email || !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
            status.textContent = '{{ __("emailInvalid") }}';
            status.className = 'status error';
            return;
        }
        const btn = $('email-submit');
        btn.disabled = true;
        status.textContent = '{{ __("sendingCode") }}';
        status.className = 'status';
        try {
            const res = await fetch('/api/v1/auth/email/request', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': csrf },
                body: JSON.stringify({ email }),
            });
            const data = await res.json();
            if (!res.ok) throw new Error(data.message || 'Failed');
            currentEmail = email;
            status.textContent = '';
            $('otp-email-display').textContent = email;
            $('otp-inputs').querySelectorAll('input').forEach(i => i.value = '');
            $('otp-status').textContent = '';
            $('otp-verify').disabled = true;
            show('otp');
            startResendCooldown();
            setTimeout(() => $('otp-inputs').querySelector('input[data-index="0"]').focus(), 100);
        } catch (e) {
            status.textContent = e.message;
            status.className = 'status error';
            btn.disabled = false;
        }
    });

    // ─── OTP inputs ───
    const otpInputs = [...$('otp-inputs').querySelectorAll('input')];
    function getOtpCode() { return otpInputs.map(i => i.value).join(''); }
    function updateVerifyButton() { $('otp-verify').disabled = getOtpCode().length !== 6; }

    otpInputs.forEach((input, idx) => {
        input.addEventListener('input', e => {
            e.target.value = e.target.value.replace(/\D/g, '').slice(0, 1);
            e.target.classList.toggle('filled', e.target.value !== '');
            if (e.target.value && idx < 5) otpInputs[idx + 1].focus();
            updateVerifyButton();
            if (getOtpCode().length === 6) $('otp-verify').focus();
        });
        input.addEventListener('keydown', e => {
            if (e.key === 'Backspace' && !e.target.value && idx > 0) otpInputs[idx - 1].focus();
            if (e.key === 'ArrowLeft' && idx > 0) otpInputs[idx - 1].focus();
            if (e.key === 'ArrowRight' && idx < 5) otpInputs[idx + 1].focus();
            if (e.key === 'Enter' && !$('otp-verify').disabled) $('otp-verify').click();
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
            const nextEmpty = otpInputs.findIndex(i => !i.value);
            (nextEmpty >= 0 ? otpInputs[nextEmpty] : otpInputs[5]).focus();
            updateVerifyButton();
        });
    });

    // ─── Verify OTP ───
    $('otp-verify').addEventListener('click', () => {
        const code = getOtpCode();
        if (code.length !== 6) return;
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '/auth/email/verify-web';
        form.style.display = 'none';
        const fields = { _token: csrf, email: currentEmail, code };
        for (const [k, v] of Object.entries(fields)) {
            const input = document.createElement('input');
            input.name = k;
            input.value = v;
            form.appendChild(input);
        }
        document.body.appendChild(form);
        form.submit();
    });

    $('otp-back').addEventListener('click', e => { e.preventDefault(); show('email'); });

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
                timer.textContent = ' (' + resendCooldown + 's)';
            }
        }, 1000);
    }

    $('resend-btn').addEventListener('click', async () => {
        if (resendCooldown > 0) return;
        try {
            const res = await fetch('/api/v1/auth/email/request', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': csrf },
                body: JSON.stringify({ email: currentEmail }),
            });
            if (!res.ok) throw new Error('Failed');
            startResendCooldown();
            $('otp-status').textContent = '{{ __("newCodeSent") }}';
            $('otp-status').className = 'status success';
        } catch (e) {
            $('otp-status').textContent = e.message;
            $('otp-status').className = 'status error';
        }
    });

    // ─── Telegram ───
    $('telegram-continue').addEventListener('click', async () => {
        const status = $('status');
        const btn = $('telegram-continue');
        btn.disabled = true;
        status.textContent = '{{ __("connectingTelegram") }}';
        status.className = 'status';
        try {
            const res = await fetch('/api/v1/auth/telegram/widget/start', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': csrf },
                body: JSON.stringify({ return_uri: window.location.origin + '/' }),
            });
            const data = await res.json();
            if (!res.ok) throw new Error(data.message || 'Failed');
            const payload = data.data || data;
            if (!payload.bot_username) throw new Error('Missing widget config');
            btn.style.display = 'none';
            window.__felagiState = new URL(payload.callback_url, window.location.origin).searchParams.get('state');
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
                { method: 'GET', credentials: 'same-origin', headers: { 'Accept': 'application/json' } }
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
