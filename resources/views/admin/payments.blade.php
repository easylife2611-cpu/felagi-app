@extends('layouts.admin')
@section('title', __('screenA007'))
@section('page-title', __('adminPayments'))
@section('content')

<div class="pending">
<strong>{{ __('adminPendingTitle') }}</strong>
{{ __('adminPaymentsIntro') ?? 'Read-only payments view.' }}
</div>

<div class="stat-grid" id="pay-stats">
<div class="stat info"><div class="lbl">{{ __('adminPayTotal') ?? 'Total' }}</div><div class="val" id="stat-total">—</div></div>
<div class="stat ok"><div class="lbl">{{ __('adminPaySucceeded') ?? 'Succeeded' }}</div><div class="val" id="stat-ok">—</div></div>
<div class="stat warn"><div class="lbl">{{ __('adminPayPending') ?? 'Pending' }}</div><div class="val" id="stat-pending">—</div></div>
<div class="stat err"><div class="lbl">{{ __('adminPayFailed') ?? 'Failed' }}</div><div class="val" id="stat-failed">—</div></div>
</div>

<div class="card">
<h2>{{ __('adminPayList') ?? 'Payments' }}</h2>
<div id="payments-table"><div class="empty"><p>{{ __('adminLoading') }}</p></div></div>
</div>

<script>
(function(){
'use strict';
const $ = id => document.getElementById(id);
function esc(s){return String(s==null?'—':s).replace(/[&<>"']/g,c=>({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c]));}
function setVal(id,v){const el=$(id);if(el)el.textContent=v!=null?v:'—';}

async function load(){
  try{
    const r = await fetch('/api/v1/admin/payments',{headers:{'Accept':'application/json'},credentials:'same-origin'});
    if(!r.ok) throw new Error('HTTP '+r.status);
    const j = await r.json();
    const s = j?.data?.stats || j?.stats || {};
    setVal('stat-total', s.total); setVal('stat-ok', s.succeeded);
    setVal('stat-pending', s.pending); setVal('stat-failed', s.failed);
    const items = j?.data?.items || j?.data || [];
    if(!items.length){
      $('payments-table').innerHTML='<div class="empty"><p>{{ __('adminNoPayments') ?? "No payments yet." }}</p></div>';
      return;
    }
    const rows = items.map(p=>`<tr>
      <td>${esc(p.id)}</td>
      <td>${esc(p.provider)}</td>
      <td><strong>${esc(p.amount)} ${esc(p.currency||'ETB')}</strong></td>
      <td><span class="badge ${p.status==='succeeded'?'ok':p.status==='failed'?'err':'warn'}">${esc(p.status)}</span></td>
      <td>${esc(p.created_at ? new Date(p.created_at).toLocaleString() : '—')}</td>
    </tr>`).join('');
    $('payments-table').innerHTML=`<table class="table"><thead><tr>
      <th>ID</th><th>{{ __('provider') ?? "Provider" }}</th>
      <th>{{ __('amount') ?? "Amount" }}</th><th>{{ __('status') ?? "Status" }}</th>
      <th>{{ __('created') ?? "Created" }}</th></tr></thead><tbody>${rows}</tbody></table>`;
  }catch(e){console.warn(e);$('payments-table').innerHTML='<div class="empty"><p>{{ __('loadErrorBody') }}</p></div>';}
}
load();
})();
</script>
@endsection
