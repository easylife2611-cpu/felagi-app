@extends('layouts.admin')

@section('title', __('admin.ads.title', [], 'Sponsored Ads'))

@section('content')
<div class="admin-shell">
    <header class="admin-header">
        <div>
            <h1>{{ __('admin.ads.title', [], 'Sponsored Ads') }}</h1>
            <p class="muted">{{ __('admin.ads.subtitle', [], 'Governed sponsored placement control center') }}</p>
        </div>
        <div class="admin-actions">
            <a href="/admin/monetization" class="btn btn-secondary">{{ __('common.back', [], 'Back') }}</a>
        </div>
    </header>

    {{-- Master Status --}}
    <section class="card">
        <div class="card-header">
            <h2>{{ __('admin.ads.master_status', [], 'Master Status') }}</h2>
            <span class="badge badge-off" id="master-badge">OFF</span>
        </div>
        <p class="muted">
            {{ __('admin.ads.master_help', [], 'Turning OFF stops new ad rendering globally without deleting campaign history.') }}
        </p>
        <div class="row">
            <label class="switch">
                <input type="checkbox" id="master-toggle">
                <span>{{ __('admin.ads.master_toggle', [], 'Sponsored Ads Master') }}</span>
            </label>
        </div>
    </section>

    {{-- Placement Switches --}}
    <section class="card">
        <h2>{{ __('admin.ads.placements', [], 'Placement-Level Switches') }}</h2>
        <ul class="placement-list">
            <li>
                <label class="switch">
                    <input type="checkbox" class="placement-toggle" data-placement="AD_BROWSE_INLINE_01">
                    <span>{{ __('admin.ads.placement.browse', [], 'Browse Sponsored Ads') }}</span>
                </label>
            </li>
            <li>
                <label class="switch">
                    <input type="checkbox" class="placement-toggle" data-placement="AD_SEARCH_RESULTS_INLINE_01">
                    <span>{{ __('admin.ads.placement.search', [], 'Search Sponsored Ads') }}</span>
                </label>
            </li>
            <li>
                <label class="switch">
                    <input type="checkbox" class="placement-toggle" data-placement="AD_NEED_DETAIL_BOTTOM_01">
                    <span>{{ __('admin.ads.placement.need_detail', [], 'Need Detail Sponsored Ads') }}</span>
                </label>
            </li>
        </ul>
    </section>

    {{-- Campaign Counts --}}
    <section class="card">
        <h2>{{ __('admin.ads.campaigns', [], 'Campaigns') }}</h2>
        <div class="stat-grid">
            <div class="stat">
                <span class="stat-label">{{ __('admin.ads.status.draft', [], 'Draft') }}</span>
                <span class="stat-value" id="count-draft">0</span>
            </div>
            <div class="stat">
                <span class="stat-label">{{ __('admin.ads.status.scheduled', [], 'Scheduled') }}</span>
                <span class="stat-value" id="count-scheduled">0</span>
            </div>
            <div class="stat">
                <span class="stat-label">{{ __('admin.ads.status.active', [], 'Active') }}</span>
                <span class="stat-value" id="count-active">0</span>
            </div>
            <div class="stat">
                <span class="stat-label">{{ __('admin.ads.status.paused', [], 'Paused') }}</span>
                <span class="stat-value" id="count-paused">0</span>
            </div>
            <div class="stat">
                <span class="stat-label">{{ __('admin.ads.status.ended', [], 'Ended') }}</span>
                <span class="stat-value" id="count-ended">0</span>
            </div>
        </div>

        <div class="actions">
            <button class="btn btn-primary" id="create-campaign">
                {{ __('admin.ads.create_campaign', [], 'Create Campaign') }}
            </button>
        </div>
    </section>

    {{-- Today --}}
    <section class="card">
        <h2>{{ __('admin.ads.today', [], 'Today') }}</h2>
        <div class="stat-grid">
            <div class="stat">
                <span class="stat-label">{{ __('admin.ads.impressions', [], 'Impressions') }}</span>
                <span class="stat-value muted">—</span>
                <span class="muted small">{{ __('admin.ads.unknown_note', [], 'UNKNOWN until instrumentation') }}</span>
            </div>
            <div class="stat">
                <span class="stat-label">{{ __('admin.ads.clicks', [], 'Clicks') }}</span>
                <span class="stat-value muted">—</span>
                <span class="muted small">{{ __('admin.ads.unknown_note', [], 'UNKNOWN until instrumentation') }}</span>
            </div>
        </div>
    </section>

    {{-- Campaign List --}}
    <section class="card">
        <h2>{{ __('admin.ads.campaign_list', [], 'All Campaigns') }}</h2>
        <div id="campaign-list" class="campaign-list">
            <p class="muted">{{ __('admin.ads.loading', [], 'Loading campaigns...') }}</p>
        </div>
    </section>

    {{-- Create Campaign Wizard (hidden by default) --}}
    <section class="card" id="wizard" style="display:none">
        <h2>{{ __('admin.ads.wizard.title', [], 'Create Campaign') }}</h2>
        <ol class="wizard-steps">
            <li>{{ __('admin.ads.wizard.step1', [], 'Sponsor') }}</li>
            <li>{{ __('admin.ads.wizard.step2', [], 'Creative') }}</li>
            <li>{{ __('admin.ads.wizard.step3', [], 'Destination') }}</li>
            <li>{{ __('admin.ads.wizard.step4', [], 'Placement') }}</li>
            <li>{{ __('admin.ads.wizard.step5', [], 'Schedule') }}</li>
            <li>{{ __('admin.ads.wizard.step6', [], 'Targeting / Frequency') }}</li>
            <li>{{ __('admin.ads.wizard.step7', [], 'Preview') }}</li>
            <li>{{ __('admin.ads.wizard.step8', [], 'Publish') }}</li>
        </ol>
        <p class="muted">
            {{ __('admin.ads.wizard.note', [], 'Full wizard UI is a progressive enhancement — API endpoints are ready.') }}
        </p>
    </section>

    {{-- Diagnostics --}}
    <section class="card">
        <h2>{{ __('admin.ads.diagnostics', [], 'Diagnostics') }}</h2>
        <ul class="diagnostic-list">
            <li><strong>master_enabled:</strong> <span id="diag-master">false</span></li>
            <li><strong>policy_version:</strong> <span id="diag-policy">1.4.0</span></li>
            <li><strong>placements_registered:</strong> <span id="diag-placements">3</span></li>
            <li><strong>tracking:</strong> <span class="muted">NOT_AVAILABLE</span></li>
        </ul>
    </section>
</div>

<script>
    const API = '/api/v1/admin/ads';
    const HEADERS = {
        'Accept': 'application/json',
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
    };

    async function loadOverview() {
        try {
            const r = await fetch(API, { headers: HEADERS });
            const j = await r.json();
            if (!j.success) return;

            const data = j.data;
            document.getElementById('master-badge').textContent = data.master_enabled ? 'ON' : 'OFF';
            document.getElementById('master-toggle').checked = data.master_enabled;
            document.getElementById('diag-master').textContent = data.master_enabled ? 'true' : 'false';

            document.getElementById('count-draft').textContent = data.counts.draft;
            document.getElementById('count-scheduled').textContent = data.counts.scheduled;
            document.getElementById('count-active').textContent = data.counts.active;
            document.getElementById('count-paused').textContent = data.counts.paused;
            document.getElementById('count-ended').textContent = data.counts.ended;

            document.querySelectorAll('.placement-toggle').forEach(el => {
                const p = el.dataset.placement;
                el.checked = !!data.placements[p];
            });
        } catch (e) {
            console.error('loadOverview failed', e);
        }
    }

    document.getElementById('master-toggle')?.addEventListener('change', async (e) => {
        const enabled = e.target.checked;
        // Master switch uses governed Admin change contract in production.
        // Placeholder here: cache write is the canonical toggle target.
        console.log('Master toggle requested:', enabled);
        document.getElementById('master-badge').textContent = enabled ? 'ON' : 'OFF';
    });

    document.getElementById('create-campaign')?.addEventListener('click', () => {
        document.getElementById('wizard').style.display = 'block';
    });

    loadOverview();
</script>

<style>
    .admin-shell { padding: 2rem; max-width: 1200px; margin: 0 auto; }
    .admin-header { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 2rem; }
    .muted { color: #666; }
    .small { font-size: 0.85rem; }
    .card { background: #fff; border: 1px solid #e5e7eb; border-radius: 8px; padding: 1.5rem; margin-bottom: 1.5rem; }
    .card h2 { margin-top: 0; }
    .card-header { display: flex; justify-content: space-between; align-items: center; }
    .badge { padding: 0.25rem 0.75rem; border-radius: 4px; font-weight: 600; }
    .badge-off { background: #fee2e2; color: #991b1b; }
    .row { display: flex; align-items: center; gap: 1rem; margin-top: 0.75rem; }
    .switch { display: flex; align-items: center; gap: 0.5rem; cursor: pointer; }
    .placement-list { list-style: none; padding: 0; }
    .placement-list li { padding: 0.5rem 0; border-bottom: 1px solid #f3f4f6; }
    .stat-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(140px, 1fr)); gap: 1rem; margin: 1rem 0; }
    .stat { display: flex; flex-direction: column; padding: 1rem; background: #f9fafb; border-radius: 6px; }
    .stat-label { font-size: 0.85rem; color: #666; }
    .stat-value { font-size: 1.5rem; font-weight: 700; }
    .actions { margin-top: 1rem; }
    .btn { padding: 0.5rem 1rem; border-radius: 6px; text-decoration: none; border: none; cursor: pointer; }
    .btn-primary { background: #2563eb; color: #fff; }
    .btn-secondary { background: #e5e7eb; color: #111827; }
    .campaign-list { margin-top: 1rem; }
    .wizard-steps { counter-reset: step; list-style: none; padding: 0; display: flex; flex-wrap: wrap; gap: 0.5rem; }
    .wizard-steps li { padding: 0.5rem 1rem; background: #f3f4f6; border-radius: 6px; font-size: 0.85rem; }
    .diagnostic-list { list-style: none; padding: 0; font-family: monospace; font-size: 0.9rem; }
</style>
@endsection
