@extends('layouts.admin')
@section('title', __('screenA020'))
@section('page-title', __('adminMonetization'))
@section('content')

<div class="pending">
    <strong>{{ __('adminPendingTitle') }}</strong>
    {{ __('adminPendingBody') }}
</div>

<div class="stat-grid" id="monetization-stats">
    <div class="stat info"><div class="lbl">{{ __('adminStatRevenue') }}</div><div class="val" id="stat-mon-revenue">—</div></div>
    <div class="stat ok"><div class="lbl">{{ __('adminStatActiveBoosts') }}</div><div class="val" id="stat-mon-boosts">—</div></div>
    <div class="stat warn"><div class="lbl">{{ __('adminStatUnlocks') }}</div><div class="val" id="stat-mon-unlocks">—</div></div>
</div>

<div class="card">
    <h2>{{ __('adminPaymentsMaster') }}</h2>
    <table class="table">
        <thead><tr><th>Key</th><th>{{ __('adminFeatureStatus') }}</th><th>{{ __('adminVersion') }}</th></tr></thead>
        <tbody>
            <tr>
                <td><code>feature.boosts</code></td>
                <td id="mon-boost-enabled">—</td>
                <td id="mon-boost-source">—</td>
            </tr>
            <tr>
                <td><code>feature.payments</code></td>
                <td id="mon-payments-enabled">—</td>
                <td id="mon-payments-source">—</td>
            </tr>
        </tbody>
    </table>
</div>

<div class="card">
    <h2>{{ __('adminOfferUnlock') }}</h2>
    <table class="table">
        <tbody>
            <tr><td>{{ __('adminFeatureStatus') }}</td><td id="mon-unlock-enabled">—</td></tr>
            <tr><td>{{ __('adminOfferFee') }}</td><td id="mon-unlock-fee">—</td></tr>
            <tr><td>{{ __('adminOfferCurrency') }}</td><td id="mon-unlock-currency">—</td></tr>
        </tbody>
    </table>
</div>

<div class="card">
    <div style="display:flex;flex-wrap:wrap;justify-content:space-between;align-items:center;gap:8px;margin-bottom:12px;">
        <h2 style="margin:0;">{{ __('adminBoostPackages') }}</h2>
        <button type="button" class="btn sm sec" id="boost-packages-propose"
                title="Editable via A017 Settings — WP-13 change lifecycle (draft → validate → preview → publish)">
            {{ __('adminProposeChange') }}
        </button>
    </div>
    <div id="mon-packages">
        <div class="empty"><p>{{ __('adminLoading') }}</p></div>
    </div>
    <p style="color:#586675;font-size:12px;margin-top:10px;line-height:1.5;">
        Setting key: <code>boostPackages</code> · Risk: HIGH · Editable via A017 Settings
        using the WP-13 change lifecycle (reauth + confirmation required).
    </p>
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
            const res = await fetch('/api/v1/admin/monetization-status', {
                credentials: 'same-origin',
                headers: { 'Accept': 'application/json' }
            });
            if (!res.ok) return;
            const data = await res.json();
            const d = data?.data || {};
            const m = d.master || {};
            const u = d.offer_unlock || {};
            const s = d.stats || {};

            $('stat-mon-revenue').textContent = (s.total_revenue ?? 0).toFixed(2) + ' ' + (u.currency || 'ETB');
            $('stat-mon-boosts').textContent  = s.active_boosts ?? '0';
            $('stat-mon-unlocks').textContent = s.total_unlocks ?? '0';

            $('mon-boost-enabled').innerHTML    = yesNo(m.boost_enabled);
            $('mon-payments-enabled').innerHTML = yesNo(m.payments_enabled);
            $('mon-boost-source').textContent   = m.source || '—';
            $('mon-payments-source').textContent = m.source || '—';

            $('mon-unlock-enabled').innerHTML  = yesNo(u.enabled);
            $('mon-unlock-fee').textContent    = u.fee ?? '—';
            $('mon-unlock-currency').textContent = u.currency || '—';

            const list = d.packages || [];
            const container = $('mon-packages');
            if (list.length === 0) {
                container.innerHTML = '<div class="empty"><p>{{ __('adminNoPackages') }}</p></div>';
                return;
            }
            container.innerHTML =
                '<table class="table"><thead><tr>' +
                '<th>{{ __('adminBoostPackages') }}</th>' +
                '<th>{{ __('adminOfferFee') }}</th>' +
                '<th>{{ __('adminFeatureStatus') }}</th>' +
                '</tr></thead><tbody>' +
                list.map(p =>
                    '<tr>' +
                    '<td>' + esc(p.duration_days) + ' days</td>' +
                    '<td>' + esc(p.price) + ' ' + esc(p.currency) + '</td>' +
                    '<td>' + yesNo(p.active) + '</td>' +
                    '</tr>'
                ).join('') +
                '</tbody></table>';
        } catch (e) { console.warn('monetization load failed', e); }
    }
    document.getElementById('boost-packages-propose')?.addEventListener('click', function(){
        alert('Boost packages are edited via A017 Settings using the WP-13 change lifecycle.\n\n' +
              'Setting key: boostPackages\n' +
              'Risk: HIGH (reauth + confirmation required)\n' +
              'Endpoint: POST /api/v1/admin/changes with setting_key="boostPackages"');
    });
    load();
})();
</script>
@endsection
