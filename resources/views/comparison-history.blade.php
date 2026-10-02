<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>{{ __('comparisonHistoryTitle') }} — {{ __('brand') }}</title>
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
.intro{background:#e8eef4;border-radius:10px;padding:14px 16px;font-size:13px;color:#003366;margin-bottom:16px;line-height:1.5}
.comp-card{background:#fff;border-radius:10px;padding:16px;margin-bottom:12px;box-shadow:0 1px 3px rgba(0,0,0,.08);display:flex;gap:14px;text-decoration:none;color:inherit;transition:box-shadow .15s}
.comp-card:hover{box-shadow:0 4px 12px rgba(0,0,0,.1)}
.comp-card .version{width:52px;height:52px;border-radius:12px;background:#003366;color:#fff;display:flex;flex-direction:column;align-items:center;justify-content:center;flex:0 0 auto}
.comp-card .version .num{font-size:18px;font-weight:700;line-height:1}
.comp-card .version .lbl{font-size:9px;text-transform:uppercase;letter-spacing:.5px;margin-top:2px;opacity:.85}
.comp-card .body{flex:1;min-width:0}
.comp-card .title{font-weight:600;font-size:15px;margin-bottom:4px;color:#192431}
.comp-card .meta{font-size:12px;color:#586675;display:flex;flex-wrap:wrap;gap:12px;margin-bottom:6px}
.badge{display:inline-block;padding:3px 10px;border-radius:999px;font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.5px}
.badge.pending{background:#fff3e0;color:#e65100}
.badge.processing{background:#e3f2fd;color:#0d47a1}
.badge.completed{background:#e8f5e9;color:#1b5e20}
.badge.failed{background:#ffebee;color:#b71c1c}
.badge.cancelled{background:#f5f5f5;color:#616161}
.comp-card .action{display:flex;flex-direction:column;align-items:flex-end;justify-content:space-between;gap:8px}
.arrow{color:#586675;font-size:20px}
.btn{padding:12px 20px;border-radius:8px;font-size:15px;font-weight:600;cursor:pointer;border:none;font-family:inherit;text-decoration:none;text-align:center;display:inline-block;background:#003366;color:#fff;transition:background .15s}
.btn:hover:not(:disabled){background:#002a52}
.btn.sec{background:#eef1f4;color:#192431}
.btn.sec:hover{background:#e0e4e8}
.btn.sm{padding:6px 12px;font-size:12px;border-radius:6px}
.spinner{display:inline-block;width:20px;height:20px;border:3px solid #e0e0e0;border-top-color:#003366;border-radius:50%;animation:spin .8s linear infinite}
@keyframes spin{to{transform:rotate(360deg)}}
.offline-banner{background:#fff8e1;border:1px solid #ffe082;color:#8a6d00;padding:10px 14px;border-radius:8px;font-size:13px;margin-bottom:16px;display:none}
.offline-banner.on{display:block}
.toast{position:fixed;bottom:100px;left:50%;transform:translateX(-50%);background:#192431;color:#fff;padding:12px 20px;border-radius:8px;font-size:14px;z-index:40;opacity:0;pointer-events:none;transition:opacity .2s;max-width:90vw;text-align:center}
.toast.on{opacity:1}
.actions-bottom{margin-top:20px;display:flex;gap:10px;flex-wrap:wrap}
</style>
</head>
<body>
<header>
<a href="#" id="back-btn" title="{{ __('back') }}">&#8592;</a>
<span class="title">{{ __('comparisonHistoryTitle') }}</span>
</header>

<main>
<div class="offline-banner" id="offline-banner">{{ __('offlineBody') }}</div>

<div class="intro">{{ __('comparisonHistoryIntro') }}</div>

<div id="state-loading" class="state" style="padding:60px 20px">
<div class="spinner"></div>
</div>

<div id="state-empty" class="state" hidden>
<div class="icon-big">&#128202;</div>
<h3>{{ __('noComparisonsTitle') }}</h3>
<p>{{ __('noComparisonsBody') }}</p>
<a href="#" id="need-btn-empty" class="btn sec">{{ __('backToNeed') }}</a>
</div>

<div id="state-error" class="state" hidden>
<h3>{{ __('loadErrorTitle') }}</h3>
<p>{{ __('loadErrorBody') }}</p>
<button type="button" class="btn" onclick="loadHistory()">{{ __('retry') }}</button>
</div>

<div id="list" hidden></div>

<div class="actions-bottom" id="bottom-actions" hidden>
<a href="#" id="need-btn" class="btn sec">{{ __('backToNeed') }}</a>
<a href="#" id="new-comp-btn" class="btn">{{ __('newComparison') }}</a>
</div>
</main>

<div class="toast" id="toast"></div>

<script>
(function(){
'use strict';
var LS_TOKEN='felagi_token';
var needId='';
var comparisons=[];

function $(id){return document.getElementById(id);}
function getToken(){return 'session'; /* L305d */}
function esc(s){return String(s==null?'':s).replace(/[&<>"']/g,function(c){return {'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c];});}

function getNeedId(){
  var parts=window.location.pathname.split('/').filter(Boolean);
  return parts.length>=2?parts[1]:'';
}

function toast(msg,ms){ms=ms||2400;var el=$('toast');el.textContent=msg;el.className='toast on';setTimeout(function(){el.className='toast';},ms);}

function showOnly(name){
  ['state-loading','state-empty','state-error','list'].forEach(function(id){
    var el=$(id); if(el)el.hidden=(id!==((name==='content')?'content':'state-'+name));
  });
  var ba=$('bottom-actions');
  if(ba)ba.hidden=(name!=='list');
}

function fmtDate(iso){
  if(!iso)return '—';
  var d=new Date(iso);
  if(isNaN(d))return '—';
  return d.toLocaleDateString()+' '+d.toLocaleTimeString([],{hour:'2-digit',minute:'2-digit'});
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

function renderList(){
  if(!comparisons.length){showOnly('empty');return;}
  $('list').innerHTML=comparisons.map(function(c){
    var s=String(c.status||'').toLowerCase();
    var badge='<span class="badge '+esc(s)+'">'+esc(statusLabel(c.status))+'</span>';
    var offerCount=c.included_offer_count!=null?(c.included_offer_count+' {{ __('offers') }}'):'';
    var requested=fmtDate(c.requested_at);
    return '<a class="comp-card" href="/comparisons/'+encodeURIComponent(c.id)+'">'+
      '<div class="version"><div class="num">#'+(c.version_number||1)+'</div><div class="lbl">{{ __('version') }}</div></div>'+
      '<div class="body">'+
        '<div class="title">'+badge+'</div>'+
        '<div class="meta"><span>&#128197; '+esc(requested)+'</span>'+(offerCount?'<span>&#128203; '+esc(offerCount)+'</span>':'')+'</div>'+
      '</div>'+
      '<div class="action"><span class="arrow">&#8250;</span></div>'+
    '</a>';
  }).join('');
  showOnly('list');
}

function loadHistory(){
  needId=getNeedId();
  if(!needId){showOnly('error');return;}
  var token=getToken();
  /* L305d */

  var backHref='/needs/'+encodeURIComponent(needId);
  $('back-btn').href=backHref;
  $('need-btn').href=backHref;
  $('need-btn-empty').href=backHref;
  $('new-comp-btn').href='/needs/'+encodeURIComponent(needId)+'/compare';

  showOnly('loading');

  fetch('/api/v1/needs/'+encodeURIComponent(needId)+'/comparisons',{
    credentials:'same-origin',headers:{'Accept':'application/json'}
  })
  .then(function(r){
    if(r.status===404)throw new Error('notfound');
    if(r.status===401)throw new Error('auth');
    if(r.status===403)throw new Error('denied');
    if(r.status===429)throw new Error('rate-limited');
    if(!r.ok)throw new Error('HTTP '+r.status);
    return r.json();
  })
  .then(function(j){
    var data=(j&&j.data)?j.data:(Array.isArray(j)?j:[]);
    comparisons=Array.isArray(data)?data:[];
    if(!comparisons.length){
      showOnly('empty');
      return;
    }
    renderList();
  })
  .catch(function(e){
    var m=String(e.message||e);
    if(m==='auth'){localStorage.removeItem(LS_TOKEN);window.location.href='/';return;}
    showOnly('error');
  });
}

window.loadHistory=loadHistory;

window.addEventListener('offline',function(){$('offline-banner').className='offline-banner on';});
window.addEventListener('online',function(){$('offline-banner').className='offline-banner';loadHistory();});

if(navigator.onLine===false){$('offline-banner').className='offline-banner on';}

loadHistory();
})();
</script>
</body>
</html>
