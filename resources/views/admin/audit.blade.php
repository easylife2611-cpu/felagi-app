@extends('layouts.admin')
@section('title', __('screenA016'))
@section('page-title', __('adminAudit'))
@section('content')

<div class="pending">
<strong>{{ __('adminPendingTitle') }}</strong>
{{ __('adminAuditIntro') ?? 'Read-only audit log.' }}
</div>

<div class="stat-grid" id="audit-stats">
<div class="stat info"><div class="lbl">{{ __('adminAuditTotal') ?? 'Total' }}</div><div class="val" id="stat-total">—</div></div>
<div class="stat ok"><div class="lbl">{{ __('adminAuditToday') ?? 'Today' }}</div><div class="val" id="stat-today">—</div></div>
<div class="stat warn"><div class="lbl">{{ __('adminAudit7d') ?? 'Last 7 days' }}</div><div class="val" id="stat-7d">—</div></div>
<div class="stat err"><div class="lbl">{{ __('adminAuditCritical') ?? 'Critical' }}</div><div class="val" id="stat-critical">—</div></div>
</div>

<div class="card">
<div style="display:flex;gap:12px;align-items:center;margin-bottom:12px;flex-wrap:wrap">
<h2 style="margin:0;flex:1">{{ __('adminAuditRecent') ?? 'Recent events' }}</h2>
<input type="search" id="audit-search" placeholder="{{ __('search') ?? 'Search…' }}" style="padding:8px 12px;border:1px solid #d8dee5;border-radius:6px;font-size:13px;min-width:220px">
</div>
<div id="audit-table"><div class="empty"><p>{{ __('adminLoading') }}</p></div></div>
</div>

<script>
(function(){
'use strict';
const $ = id => document.getElementById(id);
function esc(s){return String(s==null?'—':s).replace(/[&<>"']/g,c=>({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c]));}
function setVal(id,v){const el=$(id);if(el)el.textContent=v!=null?v:'—';}

async function loadStats(){
  try{
    const r = await fetch('/api/v1/admin/audit',{headers:{'Accept':'application/json'},credentials:'same-origin'});
    if(!r.ok) return;
    const j = await r.json();
    const s = j?.data?.stats || j?.stats || {};
    setVal('stat-total', s.total); setVal('stat-today', s.today);
    setVal('stat-7d', s.last_7d); setVal('stat-critical', s.critical);
  }catch(e){}
}

async function loadEvents(){
  const q = new URLSearchParams();
  const kw = $('audit-search')?.value.trim();
  if(kw) q.set('q', kw);
  try{
    const r = await fetch('/api/v1/admin/audit?'+q.toString(),{headers:{'Accept':'application/json'},credentials:'same-origin'});
    if(!r.ok) throw new Error('HTTP '+r.status);
    const j = await r.json();
    const items = j?.data?.items || j?.data || [];
    if(!items.length){
      $('audit-table').innerHTML='<div class="empty"><p>{{ __('adminNoAudit') ?? "No audit events yet." }}</p></div>';
      return;
    }
    const rows = items.map(a=>`<tr>
      <td>${esc(a.action||'—')}</td>
      <td>${esc(a.actor?.full_name || a.actor_name || a.actor_id || '—')}</td>
      <td>${esc(a.target_type||'—')} ${esc(a.target_id||'')}</td>
      <td>${esc(a.ip||'—')}</td>
      <td>${esc(a.created_at ? new Date(a.created_at).toLocaleString() : '—')}</td>
    </tr>`).join('');
    $('audit-table').innerHTML=`<table class="table"><thead><tr>
      <th>{{ __('action') ?? "Action" }}</th><th>{{ __('actor') ?? "Actor" }}</th>
      <th>{{ __('target') ?? "Target" }}</th><th>IP</th>
      <th>{{ __('when') ?? "When" }}</th>
    </tr></thead><tbody>${rows}</tbody></table>`;
  }catch(e){console.warn(e);$('audit-table').innerHTML='<div class="empty"><p>{{ __('loadErrorBody') }}</p></div>';}
}

let t=null;
$('audit-search')?.addEventListener('input',()=>{clearTimeout(t);t=setTimeout(loadEvents,300);});
loadStats();loadEvents();
})();
</script>
@endsection
