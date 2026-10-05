@extends('layouts.admin')
@section('title', __('screenA009'))
@section('page-title', __('adminContent'))
@section('content')

<div class="pending">
<strong>{{ __('adminPendingTitle') }}</strong>
{{ __('adminContentIntro') ?? 'Read-only content overview.' }}
</div>

<div class="stat-grid" id="content-stats">
<div class="stat info"><div class="lbl">{{ __('adminContentNeeds') ?? 'Needs' }}</div><div class="val" id="stat-needs">—</div></div>
<div class="stat ok"><div class="lbl">{{ __('adminContentOffers') ?? 'Offers' }}</div><div class="val" id="stat-offers">—</div></div>
<div class="stat warn"><div class="lbl">{{ __('adminContentDrafts') ?? 'Drafts' }}</div><div class="val" id="stat-drafts">—</div></div>
<div class="stat err"><div class="lbl">{{ __('adminContentFlagged') ?? 'Flagged' }}</div><div class="val" id="stat-flagged">—</div></div>
</div>

<div class="card">
<h2>{{ __('adminContentRecent') ?? 'Recent content' }}</h2>
<div id="content-table"><div class="empty"><p>{{ __('adminLoading') }}</p></div></div>
</div>

<script>
(function(){
'use strict';
const $ = id => document.getElementById(id);
function esc(s){return String(s==null?'—':s).replace(/[&<>"']/g,c=>({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c]));}
function setVal(id,v){const el=$(id);if(el)el.textContent=v!=null?v:'—';}

async function load(){
  try{
    const r = await fetch('/api/v1/admin/content',{headers:{'Accept':'application/json'},credentials:'same-origin'});
    if(!r.ok) throw new Error('HTTP '+r.status);
    const j = await r.json();
    const s = j?.data?.stats || j?.stats || {};
    setVal('stat-needs', s.needs); setVal('stat-offers', s.offers);
    setVal('stat-drafts', s.drafts); setVal('stat-flagged', s.flagged);
    const items = j?.data?.items || j?.data || [];
    if(!items.length){
      $('content-table').innerHTML='<div class="empty"><p>{{ __('adminNoContent') ?? "No content yet." }}</p></div>';
      return;
    }
    const rows = items.map(c=>`<tr>
      <td>${esc(c.type||'—')}</td>
      <td><strong>${esc(c.title||'—')}</strong></td>
      <td>${esc(c.status||'—')}</td>
      <td>${esc(c.created_at ? new Date(c.created_at).toLocaleDateString() : '—')}</td>
    </tr>`).join('');
    $('content-table').innerHTML=`<table class="table"><thead><tr>
      <th>{{ __('type') ?? "Type" }}</th><th>{{ __('title') ?? "Title" }}</th>
      <th>{{ __('status') ?? "Status" }}</th><th>{{ __('created') ?? "Created" }}</th>
    </tr></thead><tbody>${rows}</tbody></table>`;
  }catch(e){console.warn(e);$('content-table').innerHTML='<div class="empty"><p>{{ __('loadErrorBody') }}</p></div>';}
}
load();
})();
</script>
@endsection
