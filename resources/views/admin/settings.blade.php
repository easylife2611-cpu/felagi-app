@extends('layouts.admin')
@section('title', __('adminSettings'))
@section('page-title', __('adminSettings'))
@section('content')

<div class="pending">
    <strong>{{ __('adminPendingTitle') }}</strong>
    {{ __('adminPendingBody') }}
</div>

<div class="stat-grid" id="settings-stats">
    <div class="stat info"><div class="lbl">{{ __('adminSettingTotal') }}</div><div class="val" id="stat-settings-total">—</div></div>
    <div class="stat ok"><div class="lbl">{{ __('adminSettingFreeze') }}</div><div class="val" id="stat-settings-freeze">—</div></div>
</div>

<div class="card">
    <h2>{{ __('adminPlatformSettings') }}</h2>
    <div id="settings-table">
        <div class="empty"><p>{{ __('adminLoading') }}</p></div>
    </div>
</div>

<script>
(function () {
    const $ = id => document.getElementById(id);
    function esc(s) {
        return String(s == null ? '—' : s).replace(/[&<>"']/g, c => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c]));
    }
    async function load() {
        try {
            const res = await fetch('/api/v1/admin/settings-status', {
                credentials: 'same-origin',
                headers: { 'Accept': 'application/json' }
            });
            if (!res.ok) return;
            const data = await res.json();
            const d = data?.data || {};
            $('stat-settings-total').textContent  = d.total ?? '0';
            $('stat-settings-freeze').textContent = d.freeze?.enabled ? 'ON' : 'OFF';
            const list = d.settings || [];
            const container = $('settings-table');
            if (list.length === 0) {
                container.innerHTML = '<div class="empty"><p>{{ __('adminNoSettings') }}</p></div>';
                return;
            }
            container.innerHTML =
                '<table class="table"><thead><tr>' +
                '<th>{{ __('adminSettingKey') }}</th>' +
                '<th>{{ __('adminSettingValue') }}</th>' +
                '<th>{{ __('adminSettingRisk') }}</th>' +
                '<th>{{ __('adminSettingVersion') }}</th>' +
                '</tr></thead><tbody>' +
                list.map(s =>
                    '<tr>' +
                    '<td><code>' + esc(s.key) + '</code></td>' +
                    '<td>' + esc(s.effective) + '</td>' +
                    '<td>' + esc(s.risk) + '</td>' +
                    '<td>' + esc(s.version) + '</td>' +
                    '</tr>'
                ).join('') +
                '</tbody></table>';
        } catch (e) { console.warn('settings load failed', e); }
    }
    load();
})();
</script>
@endsection
