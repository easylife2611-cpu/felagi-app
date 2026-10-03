<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ __('admin.auth.login_title') }} — {{ __('brand') }}</title>
    <style>
        *{box-sizing:border-box;margin:0;padding:0}
        body{font-family:system-ui,-apple-system,sans-serif;background:#0a2540;color:#e3e8ed;min-height:100vh;display:flex;align-items:center;justify-content:center;padding:20px}
        .login-card{background:#fff;color:#192431;border-radius:12px;padding:40px;max-width:440px;width:100%;box-shadow:0 20px 60px rgba(0,0,0,.3)}
        .brand{display:flex;align-items:center;gap:10px;margin-bottom:24px}
        .brand .dot{width:12px;height:12px;background:#26c281;border-radius:50%}
        .brand .name{font-size:22px;font-weight:700;color:#0a2540}
        h1{font-size:20px;font-weight:700;color:#0a2540;margin-bottom:8px}
        .sub{color:#586675;font-size:14px;margin-bottom:24px;line-height:1.5}
        .alert{padding:12px 14px;border-radius:8px;font-size:13px;margin-bottom:16px}
        .alert-warn{background:#fff8e1;border:1px solid #ffe082;color:#8a6d00}
        .alert-info{background:#e3f2fd;border:1px solid #90caf9;color:#0d47a1}
        .alert-err{background:#fee2e2;border:1px solid #fca5a5;color:#991b1b}
        .btn{display:flex;align-items:center;justify-content:center;gap:10px;padding:14px 20px;border-radius:8px;font-size:14px;font-weight:600;text-decoration:none;cursor:pointer;border:none;font-family:inherit;transition:background .15s;width:100%}
        .btn-telegram{background:#0077b3;color:#fff}
        .btn-telegram:hover{background:#006699}
        .btn-secondary{background:#eef2f6;color:#192431;margin-top:12px}
        .btn-secondary:hover{background:#e0e6ec}
        .foot{margin-top:24px;font-size:12px;color:#586675;text-align:center;line-height:1.6}
        .foot a{color:#003366;text-decoration:none}
        #tg-widget{display:flex;justify-content:center;margin:20px 0}
        #status{margin-top:12px;font-size:13px;color:#586675;text-align:center}
    </style>
</head>
<body>
    <div class="login-card">
        <div class="brand">
            <span class="dot"></span>
            <span class="name">{{ __('brand') }} Admin</span>
        </div>

        <h1>{{ __('admin.auth.login_title') }}</h1>
        <p class="sub">{{ __('admin.auth.login_subtitle') }}</p>

        @if ($errors->any())
            <div class="alert alert-err">
                @foreach ($errors->all() as $error)
                    <div>{{ $error }}</div>
                @endforeach
            </div>
        @endif

        @if ($authenticated && !$hasAdminRole)
            <div class="alert alert-warn">
                <strong>{{ __('admin.auth.no_admin_role') }}</strong><br>
                {{ __('admin.auth.no_admin_role_hint') }}
            </div>
        @endif

        @if ($authenticated && $hasAdminRole)
            <div class="alert alert-info">{{ __('admin.auth.already_signed_in') }}</div>
            <a href="/admin/dashboard" class="btn btn-telegram">{{ __('admin.auth.go_dashboard') }}</a>
        @else
            {{-- Telegram Login Widget (L302 — restored per L296) --}}
            <div id="tg-widget"></div>
            <div id="status" style="margin-top:12px;color:#586675;font-size:14px;">{{ __('admin.auth.loading_telegram') }}</div>

            <form id="tg-form" method="POST" action="{{ route('admin.login.telegram') }}" style="display:none">
                @csrf
                <input type="hidden" name="id" id="tg-id">
                <input type="hidden" name="first_name" id="tg-first_name">
                <input type="hidden" name="last_name" id="tg-last_name">
                <input type="hidden" name="username" id="tg-username">
                <input type="hidden" name="photo_url" id="tg-photo_url">
                <input type="hidden" name="auth_date" id="tg-auth_date">
                <input type="hidden" name="hash" id="tg-hash">
            </form>

            <script>
                window.onTelegramAuth = function (user) {
                    document.getElementById('status').textContent = '{{ __("adminSigningYouIn") }}';
                    document.getElementById('tg-id').value         = user.id;
                    document.getElementById('tg-first_name').value = user.first_name || '';
                    document.getElementById('tg-last_name').value  = user.last_name || '';
                    document.getElementById('tg-username').value   = user.username || '';
                    document.getElementById('tg-photo_url').value  = user.photo_url || '';
                    document.getElementById('tg-auth_date').value  = user.auth_date;
                    document.getElementById('tg-hash').value       = user.hash;
                    document.getElementById('tg-form').submit();
                };
            </script>
            <script async
                    src="https://telegram.org/js/telegram-widget.js?22"
                    data-telegram-login="{{ config('services.telegram.bot_username', 'FelagiMarketBot') }}"
                    data-size="large"
                    data-request-access="write"
                    data-userpic="true"
                    data-onauth="onTelegramAuth(user)"></script>
            <script>
                setTimeout(() => {
                    const s = document.getElementById('status');
                    const w = document.getElementById('tg-widget');
                    if (s && w && w.children.length > 0) s.style.display = 'none';
                }, 1500);
            </script>

            <a href="/" class="btn btn-secondary" style="margin-top:16px;display:inline-block;">{{ __('common.back_home') }}</a>
        @endif

        <div class="foot">
            {{ __('admin.auth.foot_note') }}<br>
            <a href="/support/report">{{ __('admin.auth.need_help') }}</a>
        </div>
    </div>
</body>
</html>
