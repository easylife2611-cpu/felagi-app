@extends('layouts.admin')
@section('title', __('screenA002'))
@section('page-title', __('adminTelegram'))
@section('content')

<div class="pending">
<strong>{{ __('adminPendingTitle') }}</strong>
{{ __('adminTelegramIntro') ?? 'Read-only Telegram publications overview.' }}
</div>

<div class="stat-grid" id="tg-stats">
<div class="stat info"><div class="lbl">{{ __('adminTgDestinations') ?? 'Destinations' }}</div><div class="val" id="stat-dest">—</div></div>
<div class="stat ok"><div class="lbl">{{ __('adminTgPublished') ?? 'Published' }}</div><div class="val" id="stat-pub">—</div></div>
<div class="stat warn"><div class="lbl">{{ __('adminTgPending') ?? 'Pending' }}</div><div class="val" id="stat-pending">—</div></div>
<div class="stat err"><div class="lbl">{{ __('adminTgFailed') ?? 'Failed' }}</div><div class="val" id="stat-failed">—</div></div>
</div>

<div class="card">
<h2>{{ __('adminTgDestinations') ?? 'Destinations' }}</h2>
<div id="tg-destinations"><div class="empty"><p>{{ __('adminLoading') }}</p></div></div>
</div>

<div class="card">
<h2>{{ __('adminTgPublications') ?? 'Recent publications' }}</h2>
<div id="tg-publications"><div class="empty"><p>{{ __('adminLoading') }}</p></div></div>
</div>

<script>
(function(){
'use strict';
const $ = id => document.getElementById(id);
function esc(s){return String(s==null?'—':s).replace(/[&<>"']/g,c=>({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c]));}
function setVal(id,v){const el=$(id);if(el)el.textContent=v!=null?v:'—';}

async function loadStats(){
  try{
    const r = await fetch('/api/v1/admin/telegram',{headers:{'Accept':'application/json'},credentials:'same-origin'});
    if(!r.ok) return;
    const j = await r.json();
    const s = j?.data?.stats || j?.stats || {};
    setVal('stat-dest', s.destinations); setVal('stat-pub', s.published);
    setVal('stat-pending', s.pending); setVal('stat-failed', s.failed);
  }catch(e){}
}

async function loadDestinations(){
  try{
    const r = await fetch('/api/v1/admin/telegram/destinations',{headers:{'Accept':'application/json'},credentials:'same-origin'});
    if(!r.ok) throw new Error('HTTP '+r.status);
    const j = await r.json();
    const items = j?.data?.items || j?.data || [];
    if(!items.length){
      $('tg-destinations').innerHTML='<div class="empty"><p>{{ __('adminTgNoDestinations') ?? "No destinations configured." }}</p></div>';
      return;
    }
    const rows = items.map(d=>`<tr>
      <td><strong>${esc(d.name||'—')}</strong></td>
      <td>${esc(d.chat_id||'—')}</td>
      <td><span class="badge ${d.active?'ok':'info'}">${d.active?'active':'inactive'}</span></td>
      <td>${esc(d.created_at ? new Date(d.created_at).toLocaleDateString() : '—')}</td>
    </tr>`).join('');
    $('tg-destinations').innerHTML=`<table class="table"><thead><tr>
      <th>{{ __('name') ?? "Name" }}</th><th>Chat ID</th>
      <th>{{ __('status') ?? "Status" }}</th><th>{{ __('created') ?? "Created" }}</th>
    </tr></thead><tbody>${rows}</tbody></table>`;
  }catch(e){console.warn(e);$('tg-destinations').innerHTML='<div class="empty"><p>{{ __('loadErrorBody') }}</p></div>';}
}

async function loadPublications(){
  try{
    const r = await fetch('/api/v1/admin/telegram/publications',{headers:{'Accept':'application/json'},credentials:'same-origin'});
    if(!r.ok) throw new Error('HTTP '+r.status);
    const j = await r.json();
    const items = j?.data?.items || j?.data || [];
    if(!items.length){
      $('tg-publications').innerHTML='<div class="empty"><p>{{ __('adminTgNoPublications') ?? "No publications yet." }}</p></div>';
      return;
    }
    const rows = items.map(p=>`<tr>
      <td>${esc(p.need_id||'—')}</td>
      <td>${esc(p.destination?.name || p.destination_id || '—')}</td>
      <td><span class="badge ${p.status==='published'?'ok':p.status==='failed'?'err':'warn'}">${esc(p.status||'—')}</span></td>
      <td>${esc(p.published_at ? new Date(p.published_at).toLocaleString() : '—')}</td>
    </tr>`).join('');
    $('tg-publications').innerHTML=`<table class="table"><thead><tr>
      <th>{{ __('need') ?? "Need" }}</th><th>{{ __('destination') ?? "Destination" }}</th>
      <th>{{ __('status') ?? "Status" }}</th><th>{{ __('published') ?? "Published" }}</th>
    </tr></thead><tbody>${rows}</tbody></table>`;
  }catch(e){console.warn(e);$('tg-publications').innerHTML='<div class="empty"><p>{{ __('loadErrorBody') }}</p></div>';}
}

loadStats();loadDestinations();loadPublications();
})();
</script>
@endsection
