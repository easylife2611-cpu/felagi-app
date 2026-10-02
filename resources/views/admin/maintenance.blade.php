@extends('layouts.admin')
@section('title', __('adminMaintenance'))
@section('page-title', __('adminMaintenance'))
@section('content')

<div class="pending">
    <strong>{{ __('adminPendingTitle') }}</strong>
    {{ __('adminPendingBody') }}
</div>

<div class="stat-grid" id="maintenance-stats">
    <div class="stat info"><div class="lbl">{{ __('adminMaintenanceMode') }}</div><div class="val" id="stat-maintenance-enabled">—</div></div>
    <div class="stat ok"><div class="lbl">{{ __('adminMaintenanceStatus') }}</div><div class="val" id="stat-maintenance-since">—</div></div>
</div>

<div class="card">
    <h2>{{ __('adminMaintenanceMessage') }}</h2>
    <p id="maintenance-message" style="color:#586675;font-size:14px;">—</p>
</div>

<div class="card">
    <h2>{{ __('adminRefreshCache') }}</h2>
    <div id="cache-status">
        <div class="empty"><p>{{ __('adminLoading') }}</p></div>
    </div>
</div>

<script>
(function () {
    const $ = id => document.getElementById(id);
    function esc(s) {
        return String(s == null ? '—' : s).replace(/[&<>"']/g, c => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c]));
    }
    function yesNo(v) {
        return v ? '{{ __('adminOn') }}' : '{{ __('adminOff') }}';
    }
    async function load() {
        try {
            const res = await fetch('/api/v1/admin/maintenance-status', {
                credentials: 'same-origin',
                headers: { 'Accept': 'application/json' }
            });
            if (!res.ok) return;
            const data = await res.json();
            const d = data?.data || {};
            const m = d.maintenance || {};
            const c = d.cache || {};
            $('stat-maintenance-enabled').textContent = yesNo(m.enabled);
            $('stat-maintenance-since').textContent   = m.since || '—';
            $('maintenance-message').textContent      = m.message || '—';

            const rows = [
                ['config_cached',    c.config_cached],
                ['route_cached',     c.route_cached],
                ['view_cached',      (c.view_cached ?? 0) + ' files'],
                ['storage_writable', yesNo(c.storage_writable)],
                ['last_refresh',     c.last_refresh || '—'],
            ];
            $('cache-status').innerHTML =
                '<table class="table"><thead><tr><th>Key</th><th>Value</th></tr></thead><tbody>' +
                rows.map(([k, v]) => '<tr><td><code>' + esc(k) + '</code></td><td>' + esc(v) + '</td></tr>').join('') +
                '</tbody></table>';
        } catch (e) { console.warn('maintenance load failed', e); }
    }
    load();
})();
</script>
@endsection
