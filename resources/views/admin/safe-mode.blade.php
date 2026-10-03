@extends('layouts.admin')
@section('title', __('adminSafeMode'))
@section('page-title', __('adminSafeMode'))
@section('content')
<div class="pending">
    <strong>{{ __('adminPendingTitle') }}</strong>
    {{ __('adminPendingBody') }}
</div>

<div class="card">
    <h2>{{ __('adminSafeModeStatus') }}</h2>
    <p>Status: <span id="sm-status" class="badge">—</span></p>
    <p style="color:#586675;font-size:14px;" id="sm-reason"></p>
    <p style="color:#586675;font-size:12px;" id="sm-since"></p>
</div>

<div class="card">
    <h2>{{ __('adminSystemChecklist') }}</h2>
    <table class="table">
        <thead><tr><th>{{ __('adminItemCol') }}</th><th>{{ __('adminStatusCol') }}</th></tr></thead>
        <tbody id="sm-checklist"><tr><td colspan="2">Loading...</td></tr></tbody>
    </table>
</div>

<div class="card">
    <h2>{{ __('adminAffectedFeatures') }}</h2>
    <table class="table">
        <thead><tr><th>{{ __('adminFeatureCol') }}</th><th>{{ __('adminBlockedCol') }}</th></tr></thead>
        <tbody id="sm-affected"><tr><td colspan="2">Loading...</td></tr></tbody>
    </table>
</div>

<script>
(function () {
    const $ = id => document.getElementById(id);
    function badge(s) {
        if (s === 'ok')   return 'ok';
        if (s === 'fail') return 'fail';
        if (s === 'warn') return 'warn';
        return 'unknown';
    }
    function badgeTxt(s) {
        if (s === 'ok') return 'OK';
        if (s === 'fail') return 'FAIL';
        if (s === 'warn') return 'WARN';
        return 'UNKNOWN';
    }
    async function load() {
        try {
            const res = await fetch('/api/v1/admin/safe-mode-status', { credentials: 'same-origin', headers: { 'Accept': 'application/json' } });
            if (!res.ok) return;
            const d = (await res.json())?.data || {};
            const enabled = !!d.enabled;
            const el = $('sm-status');
            el.textContent = enabled ? '{{ __("adminSafeModeEnabled") }}' : '{{ __("adminSafeModeDisabled") }}';
            el.className = 'badge ' + (enabled ? 'fail' : 'ok');
            if (d.reason) $('sm-reason').textContent = '{{ __("adminSafeModeReason") }}: ' + d.reason;
            if (d.since)  $('sm-since').textContent = '{{ __("adminSafeModeSince") }}: ' + d.since;

            const cl = d.checklist || [];
            $('sm-checklist').innerHTML = cl.map(c =>
                '<tr><td>' + c.item + '</td><td><span class="badge ' + badge(c.status) + '">' + badgeTxt(c.status) + '</span></td></tr>'
            ).join('') || '<tr><td colspan="2">No data</td></tr>';

            const af = d.affected || [];
            $('sm-affected').innerHTML = af.map(a =>
                '<tr><td>' + a.feature + '</td><td>' + (a.blocked ? '🚫 Blocked' : '✓ Allowed') + '</td></tr>'
            ).join('') || '<tr><td colspan="2">No data</td></tr>';
        } catch (e) { console.warn('safe mode load', e); }
    }
    load();
})();
</script>
@endsection
