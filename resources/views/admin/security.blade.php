@extends('layouts.admin')
@section('title', __('adminSecurity'))
@section('page-title', __('adminSecurity'))
@section('content')
<div class="pending">
    <strong>{{ __('adminPendingTitle') }}</strong>
    {{ __('adminPendingBody') }}
</div>

<div class="stat-grid">
    <div class="stat info"><div class="lbl">Total Users</div><div class="val" id="stat-sec-users">—</div></div>
    <div class="stat ok"><div class="lbl">2FA Enabled</div><div class="val" id="stat-sec-totp">—</div></div>
    <div class="stat warn"><div class="lbl">Suspended</div><div class="val" id="stat-sec-suspended">—</div></div>
    <div class="stat info"><div class="lbl">Admins</div><div class="val" id="stat-sec-admins">—</div></div>
</div>

<div class="card">
    <h2>Auth Attempts (24h)</h2>
    <p style="color:#586675;font-size:14px;">Total: <strong id="stat-sec-24h">—</strong></p>
    <table class="table">
        <thead><tr><th>ID</th><th>Return URI</th><th>Consumed</th><th>Expired</th><th>Created</th></tr></thead>
        <tbody id="sec-attempts"><tr><td colspan="5">Loading...</td></tr></tbody>
    </table>
</div>

<script>
(function () {
    const $ = id => document.getElementById(id);
    async function load() {
        try {
            const res = await fetch('/api/v1/admin/security-status', {
                credentials: 'same-origin',
                headers: { 'Accept': 'application/json' }
            });
            if (!res.ok) return;
            const d = (await res.json())?.data || {};
            $('stat-sec-users').textContent = d.total_users ?? '—';
            $('stat-sec-totp').textContent = d.totp_enabled ?? '—';
            $('stat-sec-suspended').textContent = d.suspended ?? '—';
            $('stat-sec-admins').textContent = d.admin_count ?? '—';
            $('stat-sec-24h').textContent = d.attempts_24h ?? '—';

            const tb = $('sec-attempts');
            const items = d.recent_attempts || [];
            if (!items.length) { tb.innerHTML = '<tr><td colspan="5">No attempts</td></tr>'; return; }
            tb.innerHTML = items.map(a =>
                '<tr><td>' + a.id + '</td><td>' + (a.return_uri || '—') + '</td><td>' + (a.consumed ? '✓' : '—') + '</td><td>' + (a.expired ? '✓' : '—') + '</td><td>' + (a.created_at || '—') + '</td></tr>'
            ).join('');
        } catch (e) { console.warn('security load', e); }
    }
    load();
})();
</script>
@endsection
