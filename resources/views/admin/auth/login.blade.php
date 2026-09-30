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
        .btn{display:flex;align-items:center;justify-content:center;gap:10px;padding:14px 20px;border-radius:8px;font-size:14px;font-weight:600;text-decoration:none;cursor:pointer;border:none;font-family:inherit;transition:background .15s;width:100%}
        .btn-telegram{background:#0088cc;color:#fff}
        .btn-telegram:hover{background:#0077b3}
        .btn-admin{background:#26c281;color:#fff;margin-top:12px}
        .btn-admin:hover{background:#1fa76d}
        .btn-secondary{background:#eef2f6;color:#192431;margin-top:12px}
        .btn-secondary:hover{background:#e0e6ec}
        .foot{margin-top:24px;font-size:12px;color:#8a95a3;text-align:center;line-height:1.6}
        .foot a{color:#003366;text-decoration:none}
        .foot a:hover{text-decoration:underline}
        .divider{text-align:center;color:#8a95a3;font-size:12px;margin:20px 0}
    </style>
</head>
<body>
    <div class="login-card">
        <div class="brand">
            <span class="dot"></span>
            <span class="name">{{ __('brand') }} Admin</span>
        </div>

        <h1>{{ __('admin.auth.login_title') }}</h1>
        <p class="sub">
            {{ __('admin.auth.login_subtitle') }}
        </p>

        @if ($authenticated && !$hasAdminRole)
            <div class="alert alert-warn">
                <strong>{{ __('admin.auth.no_admin_role') }}</strong><br>
                {{ __('admin.auth.no_admin_role_hint') }}
            </div>
        @endif

        @if ($authenticated && $hasAdminRole)
            <div class="alert alert-info">
                {{ __('admin.auth.already_signed_in') }}
            </div>
            <a href="/admin/dashboard" class="btn btn-admin">
                {{ __('admin.auth.go_dashboard') }}
            </a>
        @else
            {{-- Primary: Telegram sign-in (existing S002 flow) --}}
            <a href="/auth/telegram" class="btn btn-telegram">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                    <path d="M9.78 18.65l.28-4.23 7.68-6.92c.34-.31-.07-.46-.52-.19L7.74 13.3 3.64 12c-.88-.25-.89-.86.2-1.3l15.97-6.16c.73-.33 1.43.18 1.15 1.3l-2.72 12.81c-.19.91-.74 1.13-1.5.71L12.6 16.3l-1.99 1.93c-.23.23-.42.42-.83.42z"/>
                </svg>
                {{ __('admin.auth.sign_in_telegram') }}
            </a>

            <div class="divider">{{ __('common.or') }}</div>

            <a href="/" class="btn btn-secondary">
                {{ __('common.back_home') }}
            </a>
        @endif

        <div class="foot">
            {{ __('admin.auth.foot_note') }}<br>
            <a href="/support/report">{{ __('admin.auth.need_help') }}</a>
        </div>
    </div>
</body>
</html>
