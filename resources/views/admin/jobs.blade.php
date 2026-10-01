@extends('layouts.admin')
@section('title', __('adminJobs'))
@section('page-title', __('adminJobs'))
@section('content')
<div class="pending">
    <strong>{{ __('adminPendingTitle') }}</strong>
    {{ __('adminPendingBody') }}
</div>

<div class="stat-grid" id="jobs-stats">
    <div class="stat info"><div class="lbl">{{ __('adminStatPending') }}</div><div class="val" id="stat-jobs-pending">—</div></div>
    <div class="stat ok"><div class="lbl">{{ __('adminStatProcessed') }}</div><div class="val" id="stat-jobs-processed">—</div></div>
    <div class="stat err"><div class="lbl">{{ __('adminStatFailed') }}</div><div class="val" id="stat-jobs-failed">—</div></div>
</div>

<div class="card">
    <h2>{{ __('adminJobQueue') }}</h2>
    <p id="jobs-driver" style="color:#586675;font-size:14px;">—</p>
    <div id="jobs-failed-list"></div>
</div>

<script>
(function () {
    const csrf = document.querySelector('meta[name="csrf-token"]')?.content || '';
    const $ = id => document.getElementById(id);

    async function load() {
        try {
            const res = await fetch('/api/v1/admin/jobs-status', {
                credentials: 'same-origin',
                headers: { 'Accept': 'application/json' }
            });
            if (!res.ok) return;
            const data = await res.json();
            const d = data?.data || {};
            $('stat-jobs-pending').textContent   = d.pending ?? '—';
            $('stat-jobs-processed').textContent = d.processed ?? '—';
            $('stat-jobs-failed').textContent    = d.failed ?? '—';
            $('jobs-driver').textContent = 'Driver: ' + (d.driver || 'unknown');

            const list = $('jobs-failed-list');
            const failed = d.recent_failed || [];
            if (failed.length === 0) {
                list.innerHTML = '<div class="empty"><p>{{ __('adminNoFailedJobs') }}</p></div>';
            } else {
                list.innerHTML = '<table class="table"><thead><tr><th>ID</th><th>Queue</th><th>Failed at</th></tr></thead><tbody>' +
                    failed.map(j => '<tr><td>' + j.id + '</td><td>' + (j.queue || '—') + '</td><td>' + (j.failed_at || '—') + '</td></tr>').join('') +
                    '</tbody></table>';
            }
        } catch (e) {
            console.warn('jobs load failed', e);
        }
    }
    load();
})();
</script>
@endsection
