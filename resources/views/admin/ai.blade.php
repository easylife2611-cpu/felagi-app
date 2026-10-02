@extends('layouts.admin')
@section('title', __('adminAI'))
@section('page-title', __('adminAI'))
@section('content')

<div class="pending">
    <strong>{{ __('adminPendingTitle') }}</strong>
    {{ __('adminPendingBody') }}
</div>

<div class="stat-grid" id="ai-stats">
    <div class="stat info"><div class="lbl">{{ __('adminAITotalComparisons') }}</div><div class="val" id="stat-ai-total">—</div></div>
    <div class="stat ok"><div class="lbl">{{ __('adminAICompleted') }}</div><div class="val" id="stat-ai-completed">—</div></div>
    <div class="stat err"><div class="lbl">{{ __('adminAIFailed') }}</div><div class="val" id="stat-ai-failed">—</div></div>
    <div class="stat warn"><div class="lbl">{{ __('adminAIProcessing') }}</div><div class="val" id="stat-ai-processing">—</div></div>
</div>

<div class="card">
    <h2>{{ __('adminAIConfig') }}</h2>
    <table class="table">
        <tbody>
            <tr><td>{{ __('adminAIModel') }}</td><td id="ai-model">—</td></tr>
            <tr><td>{{ __('adminAITimeout') }}</td><td id="ai-timeout">—</td></tr>
            <tr><td>{{ __('adminAICriteriaVersion') }}</td><td id="ai-criteria">—</td></tr>
            <tr><td>{{ __('adminAIPromptVersion') }}</td><td id="ai-prompt">—</td></tr>
            <tr><td>{{ __('adminAIMaxAttempts') }}</td><td id="ai-attempts">—</td></tr>
            <tr><td>{{ __('adminAIUserDailyCap') }}</td><td id="ai-user-cap">—</td></tr>
            <tr><td>{{ __('adminAINeedDailyCap') }}</td><td id="ai-need-cap">—</td></tr>
            <tr><td>{{ __('adminAIMaxInputTokens') }}</td><td id="ai-max-in">—</td></tr>
            <tr><td>{{ __('adminAIMaxOutputTokens') }}</td><td id="ai-max-out">—</td></tr>
        </tbody>
    </table>
</div>

<div class="card">
    <h2>{{ __('adminAITotalTokens') }} / {{ __('adminAITotalCost') }}</h2>
    <table class="table">
        <tbody>
            <tr><td>{{ __('adminAITotalTokens') }} (in)</td><td id="ai-tokens-in">—</td></tr>
            <tr><td>{{ __('adminAITotalTokens') }} (out)</td><td id="ai-tokens-out">—</td></tr>
            <tr><td>{{ __('adminAITotalCost') }}</td><td id="ai-cost">—</td></tr>
        </tbody>
    </table>
</div>

<div class="card">
    <h2>{{ __('adminAIRecentFailures') }}</h2>
    <div id="ai-failures">
        <div class="empty"><p>{{ __('adminLoading') }}</p></div>
    </div>
</div>

<script>
(function () {
    const $ = id => document.getElementById(id);
    function esc(s) {
        return String(s == null ? '—' : s).replace(/[&<>"']/g, c => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c]));
    }
    async function load() {
        try {
            const res = await fetch('/api/v1/admin/ai-status', {
                credentials: 'same-origin',
                headers: { 'Accept': 'application/json' }
            });
            if (!res.ok) return;
            const data = await res.json();
            const d = data?.data || {};
            const c = d.config || {};
            const e = d.evaluation || {};
            const t = d.token_limits || {};
            const u = d.usage_stats || {};

            $('stat-ai-total').textContent      = u.total_comparisons ?? '0';
            $('stat-ai-completed').textContent  = u.completed ?? '0';
            $('stat-ai-failed').textContent     = u.failed ?? '0';
            $('stat-ai-processing').textContent = u.processing ?? '0';

            $('ai-model').textContent     = c.model_id || '—';
            $('ai-timeout').textContent   = c.timeout_seconds ?? '—';
            $('ai-criteria').textContent  = c.criteria_version || '—';
            $('ai-prompt').textContent    = c.prompt_version || '—';
            $('ai-attempts').textContent  = e.max_attempts ?? '—';
            $('ai-user-cap').textContent  = e.user_daily_cap ?? '—';
            $('ai-need-cap').textContent  = e.need_daily_cap ?? '—';
            $('ai-max-in').textContent    = t.max_input_tokens ?? '—';
            $('ai-max-out').textContent   = t.max_output_tokens ?? '—';

            $('ai-tokens-in').textContent  = u.total_input_tokens ?? '0';
            $('ai-tokens-out').textContent = u.total_output_tokens ?? '0';
            $('ai-cost').textContent       = (u.total_cost ?? 0).toFixed(4);

            const list = d.recent_failures || [];
            const container = $('ai-failures');
            if (list.length === 0) {
                container.innerHTML = '<div class="empty"><p>{{ __('adminAINoFailures') }}</p></div>';
                return;
            }
            container.innerHTML =
                '<table class="table"><thead><tr>' +
                '<th>ID</th><th>{{ __('adminAIMaxAttempts') }}</th><th>{{ __('adminAIFailed') }}</th>' +
                '</tr></thead><tbody>' +
                list.map(f =>
                    '<tr>' +
                    '<td><code>' + esc(f.comparison_id).substring(0, 8) + '…</code></td>' +
                    '<td>' + esc(f.attempt) + '</td>' +
                    '<td>' + esc(f.failure_code || '—') + '</td>' +
                    '</tr>'
                ).join('') +
                '</tbody></table>';
        } catch (e) { console.warn('ai load failed', e); }
    }
    load();
})();
</script>
@endsection
