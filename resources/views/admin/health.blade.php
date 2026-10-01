@extends('layouts.admin')
@section('title', __('adminHealth'))
@section('page-title', __('adminHealth'))
@section('content')
<div class="pending">
    <strong>{{ __('adminPendingTitle') }}</strong>
    {{ __('adminPendingBody') }}
</div>

<div class="card">
    <h2>{{ __('adminSystemHealth') }}</h2>
    <table class="table">
        <thead>
            <tr>
                <th>{{ __('adminComponent') }}</th>
                <th>{{ __('adminStatus') }}</th>
                <th>{{ __('adminLatency') }}</th>
            </tr>
        </thead>
        <tbody id="health-components">
            <tr><td colspan="3">{{ __('adminLoading') }}</td></tr>
        </tbody>
    </table>
</div>

<script>
(function () {
    const csrf = document.querySelector('meta[name="csrf-token"]')?.content || '';

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

    async function loadHealth() {
        const tbody = document.getElementById('health-components');
        try {
            const res = await fetch('/api/v1/admin/health-status', {
                headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': csrf },
                credentials: 'same-origin'
            });
            if (!res.ok) {
                tbody.innerHTML = '<tr><td colspan="3">Failed to load health</td></tr>';
                return;
            }
            const data = await res.json();
            const comps = data?.data || {};
            tbody.innerHTML = '';
            Object.entries(comps).forEach(([name, info]) => {
                if (name === 'computed_at') return;
                const tr = document.createElement('tr');
                tr.innerHTML =
                    '<td>' + name.charAt(0).toUpperCase() + name.slice(1) + '</td>' +
                    '<td><span class="badge ' + badgeClass(info.status) + '">' + badgeText(info.status) + '</span></td>' +
                    '<td>' + (info.latency_ms != null ? info.latency_ms + ' ms' : '—') + '</td>';
                tbody.appendChild(tr);
            });
        } catch (e) {
            tbody.innerHTML = '<tr><td colspan="3">Failed to load health</td></tr>';
        }
    }

    loadHealth();
})();
</script>
@endsection
