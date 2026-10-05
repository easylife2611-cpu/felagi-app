@extends('layouts.admin')
@section('title', __('screenA021'))
@section('page-title', __('adminReports'))
@section('content')

<div class="pending">
<strong>{{ __('adminPendingTitle') }}</strong>
{{ __('adminReportsIntro') ?? 'Read-only reports overview.' }}
</div>

<div class="stat-grid" id="reports-stats">
<div class="stat info"><div class="lbl">{{ __('adminReportsTotal') ?? 'Total' }}</div><div class="val" id="stat-total">—</div></div>
<div class="stat warn"><div class="lbl">{{ __('adminReportsOpen') ?? 'Open' }}</div><div class="val" id="stat-open">—</div></div>
<div class="stat ok"><div class="lbl">{{ __('adminReportsResolved') ?? 'Resolved' }}</div><div class="val" id="stat-resolved">—</div></div>
<div class="stat err"><div class="lbl">{{ __('adminReportsCritical') ?? 'Critical' }}</div><div class="val" id="stat-critical">—</div></div>
</div>

<div class="card">
<h2>{{ __('adminReportsRecent') ?? 'Recent reports' }}</h2>
<div id="reports-table"><div class="empty"><p>{{ __('adminLoading') }}</p></div></div>
</div>

<script>
(function(){
'use strict';
const $ = id => document.getElementById(id);
function esc(s){return String(s==null?'—':s).replace(/[&<>"']/g,c=>({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c]));}
function setVal(id,v){const el=$(id);if(el)el.textContent=v!=null?v:'—';}

async function load(){
  try{
    const r = await fetch('/api/v1/admin/reports',{headers:{'Accept':'application/json'},credentials:'same-origin'});
    if(!r.ok) throw new Error('HTTP '+r.status);
    const j = await r.json();
    const s = j?.data?.stats || j?.stats || {};
    setVal('stat-total', s.total); setVal('stat-open', s.open);
    setVal('stat-resolved', s.resolved); setVal('stat-critical', s.critical);
    const items = j?.data?.items || j?.data || [];
    if(!items.length){
      $('reports-table').innerHTML='<div class="empty"><p>{{ __('adminNoReports') ?? "No reports yet." }}</p></div>';
      return;
    }
    const rows = items.map(r=>`<tr>
      <td>${esc(r.reason||r.type||'—')}</td>
      <td>${esc(r.entity_type||'—')} ${esc(r.entity_id||'')}</td>
      <td><span class="badge ${r.status==='resolved'?'ok':r.status==='open'?'warn':'info'}">${esc(r.status||'open')}</span></td>
      <td>${esc(r.created_at ? new Date(r.created_at).toLocaleDateString() : '—')}</td>
    </tr>`).join('');
    $('reports-table').innerHTML=`<table class="table"><thead><tr>
      <th>{{ __('reason') ?? "Reason" }}</th><th>{{ __('target') ?? "Target" }}</th>
      <th>{{ __('status') ?? "Status" }}</th><th>{{ __('created') ?? "Created" }}</th>
    </tr></thead><tbody>${rows}</tbody></table>`;
  }catch(e){console.warn(e);$('reports-table').innerHTML='<div class="empty"><p>{{ __('loadErrorBody') }}</p></div>';}
}
load();
})();
</script>
@endsection
