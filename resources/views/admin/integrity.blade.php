@extends('layouts.admin')
@section('title', __('adminIntegrity'))
@section('page-title', __('adminIntegrity'))
@section('content')
<div class="pending">
    <strong>{{ __('adminPendingTitle') }}</strong>
    {{ __('adminPendingBody') }}
</div>

<div class="card">
    <h2>{{ __('adminIntegrityChecks') }}</h2>
    <table class="table">
        <thead><tr><th>{{ __('adminCheck') }}</th><th>{{ __('adminStatus') }}</th><th>{{ __('adminMessage') }}</th></tr></thead>
        <tbody id="integrity-checks">
            <tr><td colspan="3">{{ __('adminLoading') }}</td></tr>
        </tbody>
    </table>
</div>

<div class="card">
    <h2>{{ __('adminStorageDirectories') }}</h2>
    <table class="table">
        <thead><tr><th>{{ __('adminPathCol') }}</th><th>{{ __('adminStatusCol') }}</th><th>{{ __('adminWritableCol') }}</th></tr></thead>
        <tbody id="integrity-storage"></tbody>
    </table>
</div>

<script>
(function () {
    const $ = id => document.getElementById(id);

    function badgeClass(status) {
        if (status === 'ok')   return 'ok';
        if (status === 'fail') return 'fail';
        if (status === 'warn') return 'warn';
        return 'unknown';
    }
    function badgeText(status) {
        if (status === 'ok')   return 'OK';
        if (status === 'fail') return 'FAIL';
        if (status === 'warn') return 'WARN';
        return 'UNKNOWN';
    }

    async function load() {
        try {
            const res = await fetch('/api/v1/admin/integrity-status', {
                credentials: 'same-origin',
                headers: { 'Accept': 'application/json' }
            });
            if (!res.ok) return;
            const data = await res.json();
            const d = data?.data || {};

            const tbody = $('integrity-checks');
            const checks = {
                'Config cache': d.config_cache,
                'Route cache':  d.route_cache,
                'View cache':   d.view_cache,
                'DB tables':    d.db_tables,
                'Log errors':   d.log_errors,
            };
            tbody.innerHTML = '';
            Object.entries(checks).forEach(([name, info]) => {
                if (!info) return;
                const tr = document.createElement('tr');
                tr.innerHTML =
                    '<td>' + name + '</td>' +
                    '<td><span class="badge ' + badgeClass(info.status) + '">' + badgeText(info.status) + '</span></td>' +
                    '<td style="font-size:13px;color:#586675;">' + (info.message || '—') + '</td>';
                tbody.appendChild(tr);
            });

            const storage = d.storage_dirs || {};
            const stbody = $('integrity-storage');
            stbody.innerHTML = '';
            Object.entries(storage).forEach(([name, info]) => {
                const tr = document.createElement('tr');
                tr.innerHTML =
                    '<td>' + name + '</td>' +
                    '<td><span class="badge ' + badgeClass(info.status) + '">' + badgeText(info.status) + '</span></td>' +
                    '<td>' + (info.writable ? '✓' : '✗') + '</td>';
                stbody.appendChild(tr);
            });
        } catch (e) {
            console.warn('integrity load failed', e);
        }
    }
    load();
})();
</script>
@endsection
