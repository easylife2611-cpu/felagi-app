@extends('layouts.admin')
@section('title', __('adminRecovery'))
@section('page-title', __('adminRecovery'))
@section('content')
<div class="pending">
    <strong>{{ __('adminPendingTitle') }}</strong>
    {{ __('adminPendingBody') }}
</div>

<div class="stat-grid">
    <div class="stat info"><div class="lbl">Restore Points</div><div class="val" id="rec-points">—</div></div>
    <div class="stat ok"><div class="lbl">Last Backup</div><div class="val" id="rec-last" style="font-size:14px;">—</div></div>
</div>

<div class="card">
    <h2>{{ __('adminRecoverySteps') }}</h2>
    <ol id="rec-steps" style="padding-left:20px;line-height:2;"></ol>
</div>

<div class="card">
    <h2>{{ __('adminRecentRestorePoints') }}</h2>
    <table class="table">
        <thead><tr><th>{{ __('adminNameCol') }}</th><th>{{ __('adminSizeCol') }}</th><th>{{ __('adminDateCol') }}</th></tr></thead>
        <tbody id="rec-list"><tr><td colspan="3">Loading...</td></tr></tbody>
    </table>
</div>

<script>
(function () {
    const $ = id => document.getElementById(id);
    function fb(b) {
        if (b == null) return '—';
        if (b < 1024) return b + ' B';
        if (b < 1048576) return (b/1024).toFixed(1) + ' KB';
        return (b/1048576).toFixed(2) + ' MB';
    }
    async function load() {
        try {
            const res = await fetch('/api/v1/admin/recovery-status', { credentials: 'same-origin', headers: { 'Accept': 'application/json' } });
            if (!res.ok) return;
            const d = (await res.json())?.data || {};
            $('rec-points').textContent = d.available_restore_points ?? 0;
            $('rec-last').textContent = d.last_backup ? d.last_backup.name + ' (' + fb(d.last_backup.size) + ')' : '—';

            const steps = d.recovery_steps || [];
            $('rec-steps').innerHTML = steps.map(s => '<li>' + s.label + '</li>').join('');

            const items = d.recent_restore_points || [];
            const tb = $('rec-list');
            tb.innerHTML = items.length
                ? items.map(r => '<tr><td>' + r.name + '</td><td>' + fb(r.size) + '</td><td>' + r.date + '</td></tr>').join('')
                : '<tr><td colspan="3">No restore points</td></tr>';
        } catch (e) { console.warn('recovery load', e); }
    }
    load();
})();
</script>
@endsection
