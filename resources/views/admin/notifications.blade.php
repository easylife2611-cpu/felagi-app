@extends('layouts.admin')
@section('title', __('screenA010'))
@section('page-title', __('adminNotifications'))
@section('content')

<div class="pending">
    <strong>{{ __('adminPendingTitle') }}</strong>
    {{ __('adminPendingBody') }}
</div>

<div class="stat-grid" id="notifications-stats">
    <div class="stat info"><div class="lbl">{{ __('adminNotificationPending') }}</div><div class="val" id="stat-nf-pending">—</div></div>
    <div class="stat ok"><div class="lbl">{{ __('adminNotificationSent') }}</div><div class="val" id="stat-nf-sent">—</div></div>
    <div class="stat err"><div class="lbl">{{ __('adminNotificationFailed') }}</div><div class="val" id="stat-nf-failed">—</div></div>
    <div class="stat warn"><div class="lbl">{{ __('adminNotificationSkipped') }}</div><div class="val" id="stat-nf-skipped">—</div></div>
</div>

<div class="card">
    <h2>{{ __('adminNotificationByChannel') }}</h2>
    <table class="table">
        <tbody>
            <tr><td>IN_APP</td><td id="nf-in-app">—</td></tr>
            <tr><td>TELEGRAM</td><td id="nf-telegram">—</td></tr>
        </tbody>
    </table>
</div>

<div class="card">
    <h2>{{ __('adminNotificationRecent') }}</h2>
    <div id="nf-recent">
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
            const res = await fetch('/api/v1/admin/notifications-status', {
                credentials: 'same-origin',
                headers: { 'Accept': 'application/json' }
            });
            if (!res.ok) return;
            const data = await res.json();
            const d = data?.data || {};
            const s = d.by_status || {};
            const c = d.by_channel || {};

            $('stat-nf-pending').textContent = s.pending ?? '0';
            $('stat-nf-sent').textContent    = s.sent ?? '0';
            $('stat-nf-failed').textContent  = s.failed ?? '0';
            $('stat-nf-skipped').textContent = s.skipped ?? '0';

            $('nf-in-app').textContent   = c.in_app ?? '0';
            $('nf-telegram').textContent = c.telegram ?? '0';

            const list = d.recent || [];
            const container = $('nf-recent');
            if (list.length === 0) {
                container.innerHTML = '<div class="empty"><p>{{ __('adminNotificationNoRecent') }}</p></div>';
                return;
            }
            container.innerHTML =
                '<table class="table"><thead><tr>' +
                '<th>{{ __('adminNotificationType') }}</th>' +
                '<th>{{ __('adminNotificationChannel') }}</th>' +
                '<th>{{ __('adminNotificationDelivery') }}</th>' +
                '</tr></thead><tbody>' +
                list.map(n =>
                    '<tr>' +
                    '<td>' + esc(n.type) + '</td>' +
                    '<td>' + esc(n.channel) + '</td>' +
                    '<td>' + esc(n.delivery_status) + '</td>' +
                    '</tr>'
                ).join('') +
                '</tbody></table>';
        } catch (e) { console.warn('notifications load failed', e); }
    }
    load();
})();
</script>
@endsection
