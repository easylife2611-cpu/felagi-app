@extends('layouts.admin')
@section('title', __('screenA004'))
@section('page-title', __('adminFeatures'))
@section('content')

<div class="pending">
    <strong>{{ __('adminPendingTitle') }}</strong>
    {{ __('adminPendingBody') }}
</div>

<div class="stat-grid" id="features-stats">
    <div class="stat info"><div class="lbl">{{ __('adminFeatureStatus') }}</div><div class="val" id="stat-features-total">—</div></div>
    <div class="stat ok"><div class="lbl">{{ __('adminOn') }}</div><div class="val" id="stat-features-on">—</div></div>
    <div class="stat err"><div class="lbl">{{ __('adminOff') }}</div><div class="val" id="stat-features-off">—</div></div>
</div>

<div class="card">
    <h2>{{ __('adminFeatureFlags') }}</h2>
    <div id="features-table">
        <div class="empty"><p>{{ __('adminLoading') }}</p></div>
    </div>
</div>

<script>
(function () {
    const $ = id => document.getElementById(id);
    function esc(s) {
        return String(s == null ? '—' : s).replace(/[&<>"']/g, c => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c]));
    }
    function yesNo(v) {
        return v ? '{{ __('adminOn') }}' : '{{ __('adminOff') }}';
    }
    async function load() {
        try {
            const res = await fetch('/api/v1/admin/features-status', {
                credentials: 'same-origin',
                headers: { 'Accept': 'application/json' }
            });
            if (!res.ok) return;
            const data = await res.json();
            const d = data?.data || {};
            const list = d.features || [];
            const onCount  = list.filter(f => f.effective === true).length;
            const offCount = list.filter(f => f.effective === false).length;
            $('stat-features-total').textContent = d.total ?? list.length;
            $('stat-features-on').textContent    = onCount;
            $('stat-features-off').textContent   = offCount;

            const container = $('features-table');
            if (list.length === 0) {
                container.innerHTML = '<div class="empty"><p>{{ __('adminNoSettings') }}</p></div>';
                return;
            }
            const labels = {
                'adminFeatureNeeds':     '{{ __('adminFeatureNeeds') }}',
                'adminFeatureOffers':    '{{ __('adminFeatureOffers') }}',
                'adminFeatureMessaging': '{{ __('adminFeatureMessaging') }}',
                'adminFeatureAiCompare': '{{ __('adminFeatureAiCompare') }}',
            };
            container.innerHTML =
                '<table class="table"><thead><tr>' +
                '<th>{{ __('adminFeature') }}</th>' +
                '<th>{{ __('adminFeatureStatus') }}</th>' +
                '<th>{{ __('adminVersion') }}</th>' +
                '<th>{{ __('adminDependencies') }}</th>' +
                '</tr></thead><tbody>' +
                list.map(f => {
                    const badge = f.effective === true
                        ? '<span class="badge ok">{{ __('adminOn') }}</span>'
                        : (f.effective === false
                            ? '<span class="badge err">{{ __('adminOff') }}</span>'
                            : '<span class="badge warn">—</span>');
                    const deps = f.dependencies && f.dependencies.length
                        ? f.dependencies.map(d2 => esc(d2.key)).join(', ')
                        : '—';
                    return '<tr>' +
                        '<td>' + esc(labels[f.label_key] || f.key) + '</td>' +
                        '<td>' + badge + '</td>' +
                        '<td>' + esc(f.version) + '</td>' +
                        '<td>' + deps + '</td>' +
                        '</tr>';
                }).join('') +
                '</tbody></table>';
        } catch (e) { console.warn('features load failed', e); }
    }
    load();
})();
</script>
@endsection
