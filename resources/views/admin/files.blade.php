@extends('layouts.admin')
@section('title', __('screenA011'))
@section('page-title', __('adminFiles'))
@section('content')

<div class="pending">
<strong>{{ __('adminPendingTitle') }}</strong>
{{ __('adminFilesIntro') ?? 'Read-only file overview.' }}
</div>

<div class="stat-grid" id="files-stats">
<div class="stat info"><div class="lbl">{{ __('adminFilesTotal') ?? 'Total' }}</div><div class="val" id="stat-total">—</div></div>
<div class="stat ok"><div class="lbl">{{ __('adminFilesClean') ?? 'Clean' }}</div><div class="val" id="stat-clean">—</div></div>
<div class="stat warn"><div class="lbl">{{ __('adminFilesPending') ?? 'Pending scan' }}</div><div class="val" id="stat-pending">—</div></div>
<div class="stat err"><div class="lbl">{{ __('adminFilesFailed') ?? 'Scan failed' }}</div><div class="val" id="stat-failed">—</div></div>
</div>

<div class="card">
<h2>{{ __('adminFilesRecent') ?? 'Recent files' }}</h2>
<div id="files-table"><div class="empty"><p>{{ __('adminLoading') }}</p></div></div>
</div>

<script>
(function(){
'use strict';
const $ = id => document.getElementById(id);
function esc(s){return String(s==null?'—':s).replace(/[&<>"']/g,c=>({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c]));}
function setVal(id,v){const el=$(id);if(el)el.textContent=v!=null?v:'—';}

async function load(){
  try{
    const r = await fetch('/api/v1/admin/files',{headers:{'Accept':'application/json'},credentials:'same-origin'});
    if(!r.ok) throw new Error('HTTP '+r.status);
    const j = await r.json();
    const s = j?.data?.stats || j?.stats || {};
    setVal('stat-total', s.total); setVal('stat-clean', s.clean);
    setVal('stat-pending', s.pending); setVal('stat-failed', s.failed);
    const items = j?.data?.items || j?.data || [];
    if(!items.length){
      $('files-table').innerHTML='<div class="empty"><p>{{ __('adminNoFiles') ?? "No files yet." }}</p></div>';
      return;
    }
    const rows = items.map(f=>`<tr>
      <td>${esc(f.name||f.path||'—')}</td>
      <td>${esc(f.mime||'—')}</td>
      <td>${esc(f.size ? (f.size/1024).toFixed(1)+' KB' : '—')}</td>
      <td><span class="badge ${f.scan_status==='clean'?'ok':f.scan_status==='failed'?'err':'warn'}">${esc(f.scan_status||'pending')}</span></td>
      <td>${esc(f.created_at ? new Date(f.created_at).toLocaleDateString() : '—')}</td>
    </tr>`).join('');
    $('files-table').innerHTML=`<table class="table"><thead><tr>
      <th>{{ __('file') ?? "File" }}</th><th>{{ __('type') ?? "Type" }}</th>
      <th>{{ __('size') ?? "Size" }}</th><th>{{ __('scan') ?? "Scan" }}</th>
      <th>{{ __('created') ?? "Created" }}</th>
    </tr></thead><tbody>${rows}</tbody></table>`;
  }catch(e){console.warn(e);$('files-table').innerHTML='<div class="empty"><p>{{ __('loadErrorBody') }}</p></div>';}
}
load();
})();
</script>
@endsection
