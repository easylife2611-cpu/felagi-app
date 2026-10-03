@extends('layouts.admin')
@section('title', __('screenA013'))
@section('page-title', __('adminBackups'))
@section('content')
<div class="pending">
    <strong>{{ __('adminPendingTitle') }}</strong>
    {{ __('adminPendingBody') }}
</div>

<div class="stat-grid">
    <div class="stat info"><div class="lbl">{{ __('adminTotalBackups') }}</div><div class="val" id="stat-backup-count">—</div></div>
    <div class="stat ok"><div class="lbl">{{ __('adminTotalSize') }}</div><div class="val" id="stat-backup-size">—</div></div>
    <div class="stat info"><div class="lbl">{{ __('adminLastBackup') }}</div><div class="val" id="stat-backup-last" style="font-size:14px;">—</div></div>
</div>

<div class="card">
    <h2>{{ __('adminBackupList') }}</h2>
    <div id="backups-list">
        <div class="empty"><p>{{ __('adminLoading') }}</p></div>
    </div>
</div>

<script>
(function () {
    const $ = id => document.getElementById(id);

    function formatBytes(b) {
        if (b == null) return '—';
        if (b < 1024) return b + ' B';
        if (b < 1024 * 1024) return (b / 1024).toFixed(1) + ' KB';
        return (b / 1024 / 1024).toFixed(2) + ' MB';
    }

    async function load() {
        try {
            const res = await fetch('/api/v1/admin/backups-status', {
                credentials: 'same-origin',
                headers: { 'Accept': 'application/json' }
            });
            if (!res.ok) return;
            const data = await res.json();
            const d = data?.data || {};
            $('stat-backup-count').textContent = d.total_backups ?? '0';
            $('stat-backup-size').textContent  = formatBytes(d.total_size);
            $('stat-backup-last').textContent  = d.last_backup
                ? d.last_backup.name + ' (' + formatBytes(d.last_backup.size) + ')'
                : '—';

            const list = $('backups-list');
            const recent = d.recent_backups || [];
            if (recent.length === 0) {
                list.innerHTML = '<div class="empty"><h3>{{ __('adminNoBackups') }}</h3><p>{{ __('adminBackupsIntro') }}</p></div>';
            } else {
                list.innerHTML = '<table class="table"><thead><tr><th>{{ __("adminNameCol") }}</th><th>{{ __("adminSizeCol") }}</th><th>{{ __("adminDateCol") }}</th></tr></thead><tbody>' +
                    recent.map(b => '<tr><td>' + b.name + '</td><td>' + formatBytes(b.size) + '</td><td>' + b.date + '</td></tr>').join('') +
                    '</tbody></table>';
            }
        } catch (e) {
            console.warn('backups load failed', e);
        }
    }
    load();
})();
</script>
@endsection
