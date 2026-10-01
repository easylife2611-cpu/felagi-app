<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>{{ __('comparisonResultTitle') }} — {{ __('brand') }}</title>
<style>
*{box-sizing:border-box;margin:0;padding:0}
body{font-family:system-ui,-apple-system,sans-serif;background:#F4F6F8;color:#192431;line-height:1.5;min-height:100vh;padding-bottom:80px}
header{background:#003366;color:#fff;height:64px;padding:0 16px;display:flex;align-items:center;gap:12px;position:sticky;top:0;z-index:30}
header a{color:#fff;text-decoration:none;font-size:20px;padding:8px;border-radius:6px;line-height:1}
header .title{font-size:16px;font-weight:600;flex:1;overflow:hidden;text-overflow:ellipsis;white-space:nowrap}
main{max-width:720px;margin:0 auto;padding:16px}
.state{padding:60px 20px;text-align:center;color:#586675}
.state h3{color:#003366;font-size:18px;margin:0 0 8px}
.state p{margin:0 0 16px}
.state .icon-big{font-size:48px;margin-bottom:12px;opacity:.4}
.card{background:#fff;border-radius:12px;padding:20px;margin-bottom:16px;box-shadow:0 1px 3px rgba(0,0,0,.08)}
.card h2{font-size:16px;font-weight:600;color:#003366;margin-bottom:14px}
.status-pill{display:inline-block;padding:4px 12px;border-radius:999px;font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.5px}
.status-pill.pending{background:#fff3e0;color:#e65100}
.status-pill.processing{background:#e3f2fd;color:#0d47a1}
.status-pill.completed{background:#e8f5e9;color:#1b5e20}
.status-pill.failed{background:#ffebee;color:#b71c1c}
.status-pill.cancelled{background:#f5f5f5;color:#616161}
.info-row{display:flex;justify-content:space-between;padding:10px 0;border-bottom:1px solid #eef1f4;font-size:14px}
.info-row:last-child{border-bottom:none}
.info-row .lbl{color:#586675}
.info-row .val{font-weight:600}
.rank{display:flex;gap:12px;padding:14px;border-radius:10px;margin-bottom:10px;background:#f6f8fa;align-items:flex-start}
.rank.top{background:#e8f5e9;border:1px solid #c8e6c9}
.rank .pos{width:32px;height:32px;border-radius:50%;background:#003366;color:#fff;display:flex;align-items:center;justify-content:center;font-weight:700;font-size:14px;flex:0 0 auto}
.rank.top .pos{background:#1b5e20}
.rank .body{flex:1;min-width:0}
.rank .name{font-weight:600;font-size:14px;margin-bottom:2px}
.rank .reason{font-size:12px;color:#586675;line-height:1.5}
.ai-note{background:#e3f2fd;border:1px solid #bbdefb;color:#0d47a1;padding:14px 16px;border-radius:10px;font-size:13px;line-height:1.5;margin-bottom:16px}
.ai-note.warn{background:#fff8e1;border-color:#ffe082;color:#8a6d00}
.btn{padding:12px 20px;border-radius:8px;font-size:15px;font-weight:600;cursor:pointer;border:none;font-family:inherit;text-decoration:none;text-align:center;display:inline-block;background:#003366;color:#fff;transition:background .15s}
.btn:hover:not(:disabled){background:#002a52}
.btn.sec{background:#eef1f4;color:#192431}
.btn.sec:hover{background:#e0e4e8}
.actions{display:flex;gap:10px;flex-wrap:wrap;margin-top:16px}
.spinner{display:inline-block;width:20px;height:20px;border:3px solid #e0e0e0;border-top-color:#003366;border-radius:50%;animation:spin .8s linear infinite}
@keyframes spin{to{transform:rotate(360deg)}}
</style>
</head>
<body>
<header>
<a href="#" id="back-btn" title="{{ __('back') }}">&#8592;</a>
<span class="title">{{ __('comparisonResultTitle') }}</span>
</header>

<main>
<div id="state-loading" class="state" style="padding:80px 20px">
<div class="spinner"></div>
<p style="margin-top:12px">{{ __('loading') }}...</p>
</div>

<div id="state-error" class="state" hidden>
<h3>{{ __('loadErrorTitle') }}</h3>
<p>{{ __('loadErrorBody') }}</p>
<a href="/my/needs" class="btn sec" style="display:inline-block;max-width:200px;margin-top:16px">{{ __('backToMyNeeds') }}</a>
</div>

<div id="content" hidden>
<div class="card">
<div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:12px">
<h2 style="margin:0">{{ __('comparisonVersion') }} <span id="version-number">#</span></h2>
<span class="status-pill" id="status-pill"></span>
</div>
<div id="info-rows"></div>
</div>

<div id="ai-note" class="ai-note warn" hidden></div>

<div class="card" id="results-card" hidden>
<h2>{{ __('rankingTitle') }}</h2>
<div id="results-list"></div>
</div>

<div class="card" id="offers-card" hidden>
<h2>{{ __('includedOffers') }}</h2>
<div id="offers-list"></div>
</div>

<div class="actions">
<a href="#" id="need-btn" class="btn sec">{{ __('viewNeed') }}</a>
<a href="#" id="history-btn" class="btn sec">{{ __('viewHistory') }}</a>
</div>
</div>
</main>

<script>
(function(){
'use strict';
var LS_TOKEN='felagi_token';
var compId='';

function $(id){return document.getElementById(id);}
function getToken(){return 'session'; /* L305d */}
function esc(s){return String(s==null?'':s).replace(/[&<>"']/g,function(c){return {'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c];});}

function getCompId(){
  var parts=window.location.pathname.split('/').filter(Boolean);
  return parts.length>=2?parts[1]:'';
}

function showOnly(name){
  ['state-loading','state-error','content'].forEach(function(id){
    var el=$(id); if(el)el.hidden=(id!=='state-'+name);
  });
}

function fmtDate(iso){
  if(!iso)return '—';
  var d=new Date(iso);
  if(isNaN(d))return '—';
  return d.toLocaleDateString()+' '+d.toLocaleTimeString([],{hour:'2-digit',minute:'2-digit'});
}

function statusClass(s){
  return 'status-pill '+String(s||'').toLowerCase();
}

function statusLabel(s){
  return ({
    'PENDING':'{{ __('compStatusPending') }}',
    'PROCESSING':'{{ __('compStatusProcessing') }}',
    'COMPLETED':'{{ __('compStatusCompleted') }}',
    'FAILED':'{{ __('compStatusFailed') }}',
    'CANCELLED':'{{ __('compStatusCancelled') }}'
  })[s]||s||'';
}

function renderInfo(comp){
  var rows=[];
  rows.push(['{{ __('requestedAt') }}', fmtDate(comp.requested_at)]);
  if(comp.completed_at)rows.push(['{{ __('completedAt') }}', fmtDate(comp.completed_at)]);
  if(comp.included_offer_count!=null)rows.push(['{{ __('offerCount') }}', comp.included_offer_count]);
  $('info-rows').innerHTML=rows.map(function(r){
    return '<div class="info-row"><span class="lbl">'+esc(r[0])+'</span><span class="val">'+esc(r[1])+'</span></div>';
  }).join('');
}

function renderAiNote(comp){
  var note=$('ai-note');
  var s=comp.status;
  if(s==='PENDING'||s==='PROCESSING'){
    note.className='ai-note';
    note.innerHTML='<strong>{{ __('aiProcessingTitle') }}</strong><br>{{ __('aiProcessingBody') }}';
    note.hidden=false;
  }else if(s==='FAILED'){
    note.className='ai-note warn';
    note.innerHTML='<strong>{{ __('aiFailedTitle') }}</strong><br>{{ __('aiFailedBody') }}';
    note.hidden=false;
  }else if(s==='COMPLETED'&&(!comp.results||!comp.results.length)){
    note.className='ai-note warn';
    note.innerHTML='<strong>{{ __('aiNoResultsTitle') }}</strong><br>{{ __('aiNoResultsBody') }}';
    note.hidden=false;
  }else{
    note.hidden=true;
  }
}

function renderResults(comp){
  var results=comp.results||[];
  if(!results.length){$('results-card').hidden=true;return;}
  $('results-card').hidden=false;
  var sorted=results.slice().sort(function(a,b){
    return (a.rank||999)-(b.rank||999);
  });
  $('results-list').innerHTML=sorted.map(function(r,i){
    var cls='rank'+(i===0?' top':'');
    var offer=r.offer||{};
    var prov=offer.provider||{};
    var name=prov.full_name||'Offer #'+(r.offer_id||'').slice(0,6);
    var reason=r.reasoning||r.reason||'';
    return '<div class="'+cls+'"><div class="pos">'+(r.rank||i+1)+'</div><div class="body"><div class="name">'+esc(name)+'</div>'+(reason?'<div class="reason">'+esc(reason)+'</div>':'')+'</div></div>';
  }).join('');
}

function renderOffers(comp){
  var cos=comp.comparison_offers||comp.comparisonOffers||[];
  if(!cos.length){$('offers-card').hidden=true;return;}
  $('offers-card').hidden=false;
  $('offers-list').innerHTML=cos.map(function(co){
    var off=co.offer||{};
    var prov=off.provider||{};
    return '<div class="info-row"><span class="lbl">'+esc(prov.full_name||'—')+'</span><span class="val">'+esc((off.currency||'ETB')+' '+(off.offered_price||'—'))+'</span></div>';
  }).join('');
}

function loadComparison(){
  compId=getCompId();
  if(!compId){showOnly('error');return;}
  var token=getToken();
  /* L305d */

  fetch('/api/v1/comparisons/'+encodeURIComponent(compId),{
    credentials:'same-origin',headers:{'Accept':'application/json'}
  })
  .then(function(r){
    if(r.status===404)throw new Error('notfound');
    if(r.status===401)throw new Error('auth');
    if(r.status===403)throw new Error('denied');
    if(!r.ok)throw new Error('HTTP '+r.status);
    return r.json();
  })
  .then(function(j){
    var comp=(j&&j.data)?j.data:j;
    if(!comp||!comp.id){showOnly('error');return;}
    $('version-number').textContent='#'+(comp.version_number||1);
    var sp=$('status-pill');
    sp.className=statusClass(comp.status);
    sp.textContent=statusLabel(comp.status);

    renderInfo(comp);
    renderAiNote(comp);
    renderResults(comp);
    renderOffers(comp);

    var needId=comp.need_id||(comp.need&&comp.need.id);
    if(needId){
      $('need-btn').href='/needs/'+encodeURIComponent(needId);
      $('history-btn').href='/needs/'+encodeURIComponent(needId)+'/comparisons';
      $('back-btn').href='/needs/'+encodeURIComponent(needId);
    }

    showOnly('content');
  })
  .catch(function(e){
    var m=String(e.message||e);
    if(m==='auth'){localStorage.removeItem(LS_TOKEN);window.location.href='/';return;}
    showOnly('error');
  });
}

loadComparison();
})();
</script>
</body>
</html>
