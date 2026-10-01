@extends('layouts.admin')
@section('title', __('adminDashboard'))
@section('page-title', __('adminDashboard'))
@section('content')
<div class="pending">
    <strong>{{ __('adminPendingTitle') }}</strong>
    {{ __('adminPendingBody') }}
</div>

<div class="stat-grid" id="stats">
    <div class="stat info"><div class="lbl">{{ __('adminStatUsers') }}</div><div class="val" id="stat-users">—</div><div class="hint">{{ __('adminStatUsersHint') }}</div></div>
    <div class="stat ok"><div class="lbl">{{ __('adminStatNeeds') }}</div><div class="val" id="stat-needs">—</div><div class="hint">{{ __('adminStatNeedsHint') }}</div></div>
    <div class="stat warn"><div class="lbl">{{ __('adminStatOffers') }}</div><div class="val" id="stat-offers">—</div><div class="hint">{{ __('adminStatOffersHint') }}</div></div>
    <div class="stat info"><div class="lbl">{{ __('adminStatReports') }}</div><div class="val" id="stat-reports">—</div><div class="hint">{{ __('adminStatReportsHint') }}</div></div>
</div>

<div class="card">
    <h2>{{ __('adminRecentActivity') }}</h2>
    <div id="recent-activity" class="activity-list">
        <div class="empty">
            <p>{{ __('adminNoActivity') }}</p>
        </div>
    </div>
</div>

<script>
(function () {
    const csrf = document.querySelector('meta[name="csrf-token"]')?.content || '';

    function setVal(id, v) {
        const el = document.getElementById(id);
        if (el) el.textContent = v != null ? v : '—';
    }

    async function loadDashboard() {
        try {
            const res = await fetch('/api/v1/admin/dashboard-metrics', {
                headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': csrf },
                credentials: 'same-origin'
            });
            if (!res.ok) return;
            const data = await res.json();
            const stats = data?.data?.stats || data?.stats || {};
            setVal('stat-users',   stats.users);
            setVal('stat-needs',   stats.needs);
            setVal('stat-offers',  stats.offers);
            setVal('stat-reports', stats.reports);
        } catch (e) {
            console.warn('Dashboard load failed', e);
        }
    }

    loadDashboard();
})();
</script>
@endsection
