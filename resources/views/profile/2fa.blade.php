<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('2fa.title') }} — Felagi</title>
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif; background: #f5f5f5; margin: 0; padding: 24px; color: #1a1a1a; }
        .container { max-width: 640px; margin: 0 auto; }
        h1 { font-size: 24px; margin: 0 0 24px; }
        .card { background: #fff; box-shadow: 0 1px 3px rgba(0,0,0,0.08); border-radius: 8px; padding: 24px; margin-bottom: 24px; }
        .row { display: flex; align-items: center; justify-content: space-between; }
        .badge { padding: 4px 12px; border-radius: 999px; font-size: 13px; font-weight: 600; }
        .badge.on { background: #d1fae5; color: #065f46; }
        .badge.off { background: #e5e7eb; color: #374151; }
        .btn { padding: 10px 18px; border: none; border-radius: 6px; font-size: 14px; font-weight: 600; cursor: pointer; color: #fff; }
        .btn-blue { background: #2563eb; }
        .btn-blue:hover { background: #1d4ed8; }
        .btn-green { background: #16a34a; }
        .btn-green:hover { background: #15803d; }
        .btn-red { background: #dc2626; }
        .btn-red:hover { background: #b91c1c; }
        .hidden { display: none !important; }
        input[type="text"] { border: 1px solid #d1d5db; border-radius: 6px; padding: 10px 14px; font-size: 18px; letter-spacing: 4px; text-align: center; width: 140px; }
        #qr-holder { background: #fff; border: 1px solid #e5e7eb; border-radius: 8px; padding: 12px; display: inline-block; }
        #qr-holder svg { display: block; width: 240px; height: 240px; }
        code { background: #f3f4f6; padding: 2px 8px; border-radius: 4px; font-family: monospace; }
        ul#recovery-list { list-style: none; padding: 12px; margin: 12px 0; background: #f9fafb; border-radius: 6px; display: grid; grid-template-columns: 1fr 1fr; gap: 8px; font-family: monospace; font-size: 13px; }
        .text-sm { font-size: 14px; color: #6b7280; margin-top: 6px; }
        .text-xs { font-size: 12px; color: #9ca3af; }
        .mt-3 { margin-top: 12px; }
        .mt-6 { margin-top: 24px; }
        .border-t { border-top: 1px solid #e5e7eb; padding-top: 16px; margin-top: 16px; }
        .link { color: #2563eb; text-decoration: underline; background: none; border: none; cursor: pointer; font-size: 13px; padding: 0; }
        .msg { font-size: 14px; margin-top: 8px; }
        .msg.err { color: #dc2626; }
        .msg.ok { color: #16a34a; }
    </style>
</head>
<body>
<div class="container">
    <h1>{{ __('2fa.title') }}</h1>

    <div class="card">
        <div class="row">
            <div>
                <strong>{{ __('2fa.status') }}</strong>
                <p class="text-sm">
                    @if($status['enabled'])
                        {{ __('2fa.enabled_since', ['date' => $status['enabled_at']]) }}
                    @else
                        {{ __('2fa.disabled') }}
                    @endif
                </p>
                @if($status['enabled'])
                    <p class="text-xs">{{ __('2fa.recovery_left', ['count' => $status['codes_left']]) }}</p>
                @endif
            </div>
            <span class="badge {{ $status['enabled'] ? 'on' : 'off' }}">
                {{ $status['enabled'] ? __('2fa.status_on') : __('2fa.status_off') }}
            </span>
        </div>
    </div>

    <div class="card {{ $status['enabled'] ? 'hidden' : '' }}" id="enroll-section">
        <h2 style="font-size:18px;margin:0 0 16px;">{{ __('2fa.enroll_heading') }}</h2>
        <button id="btn-start" class="btn btn-blue">{{ __('2fa.enroll_start') }}</button>

        <div id="qr-block" class="hidden mt-6">
            <p class="text-sm">{{ __('2fa.scan_prompt') }}</p>
            <div id="qr-holder" class="mt-3"></div>
            <p class="text-xs mt-3">
                {{ __('2fa.manual_secret') }}: <code id="secret-text"></code>
            </p>

            <div class="mt-6">
                <label style="display:block;font-size:14px;font-weight:600;margin-bottom:8px;">{{ __('2fa.enter_code') }}</label>
                <input id="enroll-code" type="text" inputmode="numeric" maxlength="6" />
                <button id="btn-verify" class="btn btn-green" style="margin-left:8px;">{{ __('2fa.verify_button') }}</button>
                <p id="enroll-msg" class="msg"></p>
            </div>
        </div>

        <div id="recovery-block" class="hidden border-t">
            <h3 style="font-size:16px;margin:0 0 8px;">{{ __('2fa.recovery_heading') }}</h3>
            <p class="text-sm">{{ __('2fa.recovery_hint') }}</p>
            <ul id="recovery-list"></ul>
            <button id="btn-download" class="link">{{ __('2fa.download_codes') }}</button>
        </div>
    </div>

    <div class="card {{ $status['enabled'] ? '' : 'hidden' }}" id="disable-section">
        <h2 style="font-size:18px;margin:0 0 16px;color:#b91c1c;">{{ __('2fa.disable_heading') }}</h2>
        <p class="text-sm">{{ __('2fa.disable_hint') }}</p>
        <input id="disable-code" type="text" inputmode="numeric" maxlength="6" />
        <button id="btn-disable" class="btn btn-red" style="margin-left:8px;">{{ __('2fa.disable_button') }}</button>
        <p id="disable-msg" class="msg"></p>
    </div>
</div>

<script>
const csrf = '{{ csrf_token() }}';
const api = (path, opts = {}) => fetch(`/api/v1/auth/2fa${path}`, {
    method: opts.method || 'GET',
    headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
        'X-CSRF-TOKEN': csrf,
    },
    credentials: 'same-origin',
    body: opts.body ? JSON.stringify(opts.body) : undefined,
}).then(r => r.json().then(d => ({ ok: r.ok, status: r.status, data: d })));

const el = id => document.getElementById(id);
const t = {
    invalid:  @json(__('2fa.invalid_code')),
    success:  @json(__('2fa.enroll_success')),
    disabled: @json(__('2fa.disable_success')),
    network:  @json(__('2fa.network_error')),
};

el('btn-start')?.addEventListener('click', async () => {
    const r = await api('/enroll/start', { method: 'POST' });
    if (!r.ok) { alert(t.network); return; }
    el('qr-holder').innerHTML = r.data.data.qr_svg;
    el('secret-text').textContent = r.data.data.secret;
    el('qr-block').classList.remove('hidden');
    el('btn-start').classList.add('hidden');
});

el('btn-verify')?.addEventListener('click', async () => {
    const code = el('enroll-code').value.trim();
    if (code.length !== 6) { el('enroll-msg').textContent = t.invalid; el('enroll-msg').className = 'msg err'; return; }
    const r = await api('/enroll/verify', { method: 'POST', body: { code } });
    if (!r.ok) { el('enroll-msg').textContent = r.data.message || t.invalid; el('enroll-msg').className = 'msg err'; return; }
    el('enroll-msg').textContent = t.success;
    el('enroll-msg').className = 'msg ok';
    el('recovery-block').classList.remove('hidden');
    const list = el('recovery-list');
    list.innerHTML = '';
    (r.data.data.recovery_codes || []).forEach(c => {
        const li = document.createElement('li');
        li.textContent = c;
        list.appendChild(li);
    });
    el('btn-download').onclick = () => {
        const blob = new Blob((r.data.data.recovery_codes || []).join('\n'), { type: 'text/plain' });
        const a = document.createElement('a');
        a.href = URL.createObjectURL(blob);
        a.download = 'felagi-2fa-recovery-codes.txt';
        a.click();
    };
});

el('btn-disable')?.addEventListener('click', async () => {
    const code = el('disable-code').value.trim();
    if (code.length !== 6) { el('disable-msg').textContent = t.invalid; el('disable-msg').className = 'msg err'; return; }
    const r = await api('/disable', { method: 'POST', body: { code } });
    if (!r.ok) { el('disable-msg').textContent = r.data.message || t.invalid; el('disable-msg').className = 'msg err'; return; }
    el('disable-msg').textContent = t.disabled;
    el('disable-msg').className = 'msg ok';
    setTimeout(() => location.reload(), 800);
});
</script>
</body>
</html>
