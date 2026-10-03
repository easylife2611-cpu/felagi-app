@extends('layouts.admin')
@section('title', __('screenA005'))
@section('page-title', __('adminMarketplace'))
@section('content')

<div class="pending">
    <strong>{{ __('adminPendingTitle') }}</strong>
    {{ __('adminPendingBody') }}
</div>

<div class="stat-grid" id="marketplace-stats">
    <div class="stat info"><div class="lbl">{{ __('adminReportOpen') }}</div><div class="val" id="stat-mk-open">—</div></div>
    <div class="stat warn"><div class="lbl">{{ __('adminReportInReview') }}</div><div class="val" id="stat-mk-in-review">—</div></div>
    <div class="stat ok"><div class="lbl">{{ __('adminReportResolved') }}</div><div class="val" id="stat-mk-resolved">—</div></div>
</div>

<div class="card">
    <h2>{{ __('adminMarketplace') }}</h2>
    <table class="table">
        <tbody>
            <tr><td>{{ __('adminMaxOffers') }}</td><td id="mk-max-offers">—</td></tr>
            <tr><td>{{ __('adminMaxOpenNeeds') }}</td><td id="mk-max-open-needs">—</td></tr>
            <tr><td>{{ __('adminOfferDeadlineMaxDays') }}</td><td id="mk-deadline">—</td></tr>
        </tbody>
    </table>
</div>

<div class="card">
    <h2>{{ __('adminReports') }}</h2>
    <div id="mk-reports">
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
            const res = await fetch('/api/v1/admin/marketplace-status', {
                credentials: 'same-origin',
                headers: { 'Accept': 'application/json' }
            });
            if (!res.ok) return;
            const data = await res.json();
            const d = data?.data || {};
            const s = d.settings || {};
            const r = d.report_stats || {};

            $('stat-mk-open').textContent      = r.open ?? '0';
            $('stat-mk-in-review').textContent = r.in_review ?? '0';
            $('stat-mk-resolved').textContent  = r.resolved ?? '0';

            $('mk-max-offers').textContent     = s.max_offers_per_comparison ?? '—';
            $('mk-max-open-needs').textContent = s.max_open_needs_per_user ?? '—';
            $('mk-deadline').textContent       = s.offer_deadline_max_days ?? '—';

            const list = d.recent_reports || [];
            const container = $('mk-reports');
            if (list.length === 0) {
                container.innerHTML = '<div class="empty"><p>{{ __('adminNoReports') }}</p></div>';
                return;
            }
            container.innerHTML =
                '<table class="table"><thead><tr>' +
                '<th>{{ __('adminReportType') }}</th>' +
                '<th>{{ __('adminReportStatus') }}</th>' +
                '<th>{{ __('adminReportAssigned') }}</th>' +
                '<th>{{ __('adminReportResolution') }}</th>' +
                '</tr></thead><tbody>' +
                list.map(rp =>
                    '<tr>' +
                    '<td>' + esc(rp.reason_code) + '</td>' +
                    '<td>' + esc(rp.status) + '</td>' +
                    '<td>' + esc(rp.assigned || '—') + '</td>' +
                    '<td>' + esc(rp.resolution || '—') + '</td>' +
                    '</tr>'
                ).join('') +
                '</tbody></table>';
        } catch (e) { console.warn('marketplace load failed', e); }
    }
    load();
})();
</script>
@endsection
