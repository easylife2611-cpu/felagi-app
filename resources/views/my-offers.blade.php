<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>{{ __('screenS013') }} — {{ __('brand') }}</title>
<style>
*{box-sizing:border-box;margin:0;padding:0}
body{font-family:system-ui,-apple-system,sans-serif;background:#F4F6F8;color:#192431;line-height:1.5;min-height:100vh;padding-bottom:80px}
header{background:#003366;color:#fff;height:64px;padding:0 16px;display:flex;align-items:center;gap:12px;position:sticky;top:0;z-index:30}
header a{color:#fff;text-decoration:none;font-size:20px;padding:8px;border-radius:6px;line-height:1}
header a:hover{background:rgba(255,255,255,.12)}
header .title{font-size:16px;font-weight:600;flex:1}
header .count{background:rgba(255,255,255,.18);padding:4px 10px;border-radius:999px;font-size:12px;font-weight:600}
main{max-width:960px;margin:0 auto;padding:16px}
.filters{display:flex;gap:8px;overflow-x:auto;padding-bottom:12px;margin-bottom:12px;scrollbar-width:none}
.filters::-webkit-scrollbar{display:none}
.chip{flex:0 0 auto;padding:8px 16px;border:1px solid #d0d7de;border-radius:999px;background:#fff;cursor:pointer;font-size:14px;font-family:inherit;color:#192431;white-space:nowrap}
.chip.active{background:#003366;color:#fff;border-color:#003366}
.feed{display:grid;grid-template-columns:1fr;gap:12px}
@media(min-width:640px){.feed{grid-template-columns:1fr 1fr}}
@media(min-width:1024px){.feed{grid-template-columns:1fr 1fr 1fr}}
.card{background:#fff;border-radius:10px;padding:16px;text-decoration:none;color:inherit;box-shadow:0 1px 3px rgba(0,0,0,.08);display:flex;flex-direction:column;gap:8px}
.card:hover{box-shadow:0 4px 12px rgba(0,0,0,.1)}
.card-top{display:flex;justify-content:space-between;align-items:flex-start;gap:8px}
.need-cat{display:inline-block;padding:3px 10px;border-radius:999px;background:#e8eef4;color:#003366;font-size:11px;font-weight:600;max-width:60%;overflow:hidden;text-overflow:ellipsis;white-space:nowrap}
.badge{display:inline-block;padding:3px 10px;border-radius:999px;font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.5px;flex:0 0 auto}
.badge.pending{background:#fff3e0;color:#e65100}
.badge.accepted{background:#e8f5e9;color:#1b5e20}
.badge.rejected{background:#f5f5f5;color:#616161}
.badge.withdrawn{background:#f5f5f5;color:#616161}
.card h3{margin:0;font-size:16px;font-weight:600;color:#192431;line-height:1.3;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden}
.card .price{color:#1b5e20;font-weight:700;font-size:18px}
.card .price .cur{font-size:12px;font-weight:600;margin-right:2px}
.card .msg{font-size:13px;color:#586675;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden}
.card .meta{font-size:12px;color:#586675;margin-top:auto;padding-top:8px;border-top:1px solid #eef1f4;display:flex;justify-content:space-between}
.state{padding:60px 20px;text-align:center;color:#586675}
.state h3{color:#003366;font-size:18px;margin:0 0 8px}
.state p{margin:0 0 16px}
.btn{padding:12px 20px;border-radius:8px;font-size:15px;font-weight:600;cursor:pointer;border:none;font-family:inherit;text-decoration:none;text-align:center;display:inline-block;background:#003366;color:#fff;transition:background .15s}
.btn:hover:not(:disabled){background:#002a52}
.btn.sec{background:#eef1f4;color:#192431}
.btn.sec:hover{background:#e0e4e8}
.skeleton-card{background:#fff;border-radius:10px;padding:16px;box-shadow:0 1px 3px rgba(0,0,0,.08)}
.sk-line{height:12px;background:#eef1f4;border-radius:4px;margin-bottom:10px}
.sk-line.w40{width:40%}.sk-line.w70{width:70%}.sk-line.w90{width:90%}.sk-line.w100{width:100%}
.load-more-wrap{text-align:center;padding:24px 0}
.fab{position:fixed;right:16px;bottom:calc(80px + env(safe-area-inset-bottom));width:56px;height:56px;border-radius:50%;background:#003366;color:#fff;display:flex;align-items:center;justify-content:center;font-size:28px;text-decoration:none;box-shadow:0 4px 12px rgba(0,51,102,.35);z-index:25}
main{padding-bottom:calc(112px + env(safe-area-inset-bottom))}
.fab:hover{background:#002a52}
.bn{position:fixed;bottom:0;left:0;right:0;height:64px;background:#fff;border-top:1px solid #eef1f4;display:flex;z-index:20;box-shadow:0 -1px 3px rgba(0,0,0,.04)}
.bn a{flex:1;display:flex;flex-direction:column;align-items:center;justify-content:center;gap:2px;text-decoration:none;color:#586675;font-size:11px}
.bn a:hover{color:#003366}
.bn a.active{color:#003366;font-weight:600}
.bn .ic{font-size:20px;line-height:1}
@media(min-width:600px){
.bn{position:fixed;left:0;top:0;bottom:0;right:auto;width:88px;height:100vh;flex-direction:column;border-top:none;border-right:1px solid #eef1f4;padding:80px 0 16px;box-shadow:1px 0 3px rgba(0,0,0,.04);z-index:25;background:#fff}
.bn a{padding:14px 4px;font-size:10px;gap:4px}
body{padding-left:88px;padding-bottom:16px}
header{margin-left:-88px;padding-left:calc(88px + 16px)}
.fab{bottom:24px;right:24px}
}
@media(min-width:1200px){
.bn{width:240px;padding-top:96px}
.bn a{flex-direction:row;justify-content:flex-start;padding:14px 24px;font-size:14px;gap:14px}
.bn .ic{font-size:22px}
body{padding-left:240px}
header{margin-left:-240px;padding-left:calc(240px + 16px)}
}
.offline-banner{background:#fff8e1;border:1px solid #ffe082;color:#8a6d00;padding:10px 14px;border-radius:8px;font-size:13px;margin-bottom:16px;display:none}
.offline-banner.on{display:block}

:focus-visible{outline:3px solid #1b5e20;outline-offset:2px;border-radius:6px}
#page-title:focus{outline:none}
.sr-only{position:absolute;width:1px;height:1px;padding:0;margin:-1px;overflow:hidden;clip:rect(0,0,0,0);white-space:nowrap;border:0}
</style>
</head>
<body>
<header>
<a href="/browse" title="{{ __('back') }}">&#8592;</a>
<span class="title">{{ __('myOffersTitle') }}</span>
<span class="count" id="offer-count" hidden>0</span>
</header>

<main role="main" aria-labelledby="page-title">
<h1 id="page-title" tabindex="-1" class="sr-only">{{ __('screenS013') }}</h1>
<div class="offline-banner" id="offline-banner">{{ __('offlineBody') }}</div>

<div class="filters" id="filters">
<button type="button" class="chip active" data-status="">{{ __('filterAll') }}</button>
<button type="button" class="chip" data-status="PENDING">{{ __('offerStatusPending') }}</button>
<button type="button" class="chip" data-status="ACCEPTED">{{ __('offerStatusAccepted') }}</button>
<button type="button" class="chip" data-status="REJECTED">{{ __('offerStatusRejected') }}</button>
<button type="button" class="chip" data-status="WITHDRAWN">{{ __('offerStatusWithdrawn') }}</button>
</div>

<div id="state-loading" class="feed">
<div class="skeleton-card"><div class="sk-line w40"></div><div class="sk-line w90"></div><div class="sk-line w70"></div></div>
<div class="skeleton-card"><div class="sk-line w40"></div><div class="sk-line w90"></div><div class="sk-line w70"></div></div>
<div class="skeleton-card"><div class="sk-line w40"></div><div class="sk-line w90"></div><div class="sk-line w70"></div></div>
</div>

<div id="state-content" class="feed" hidden></div>

<div id="state-empty" class="state" hidden>
<h3>{{ __('noMyOffersTitle') }}</h3>
<p>{{ __('noMyOffersBody') }}</p>
<a href="/browse" class="btn">{{ __('browseNeeds') }}</a>
</div>

<div id="state-error" class="state" hidden>
<h3>{{ __('loadErrorTitle') }}</h3>
<p>{{ __('loadErrorBody') }}</p>
<button type="button" class="btn" onclick="loadOffers()">{{ __('retry') }}</button>
</div>

<div class="load-more-wrap" id="load-more-wrap" hidden>
<button type="button" class="btn sec" id="load-more-btn">{{ __('loadMore') }}</button>
</div>
</main>

<a href="/browse" class="fab" title="{{ __('browseNeeds') }}">&#128269;</a>

<nav class="bn" role="navigation">
<a href="/browse"><span class="ic">&#128269;</span><span>{{ __('navBrowse') }}</span></a>
<a href="/my/needs"><span class="ic">&#128203;</span><span>{{ __('navMyNeeds') }}</span></a>
<a href="/my/offers" class="active" aria-current="page"><span class="ic">&#127991;</span><span>{{ __('navMyOffers') }}</span></a>
<a href="/notifications"><span class="ic">&#128276;</span><span>{{ __('navNotifications') }}</span></a>
<a href="/profile"><span class="ic">&#128100;</span><span>{{ __('navProfile') }}</span></a>
</nav>

<script>
(function(){
'use strict';
var LS_TOKEN='felagi_token';
var state={status:'',page:1,perPage:20,hasMore:false,items:[],loading:false};

function $(id){return document.getElementById(id);}
function getToken(){return 'session'; /* L305d — cookie auth */}
function esc(s){return String(s==null?'':s).replace(/[&<>"']/g,function(c){return {'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c];});}

function showOnly(name){
  ['state-loading','state-content','state-empty','state-error'].forEach(function(id){
    var el=$(id); if(el)el.hidden=(id!==((name==='content')?'state-content':'state-'+name));
  var loading=$("state-loading");
  if(name!=="loading" && loading){ loading.hidden=true; loading.replaceChildren(); }

  });
}

function fmtPrice(it){
  var cur=it.currency||'ETB';
  var p=it.offered_price!=null?Number(it.offered_price).toFixed(2):'-';
  return {cur:cur,price:p};
}

function fmtDate(iso){
  if(!iso)return '';
  var d=new Date(iso);
  if(isNaN(d))return '';
  return d.toLocaleDateString();
}

function statusLabel(s){
  return ({
    'PENDING':'{{ __('offerStatusPending') }}',
    'ACCEPTED':'{{ __('offerStatusAccepted') }}',
    'REJECTED':'{{ __('offerStatusRejected') }}',
    'WITHDRAWN':'{{ __('offerStatusWithdrawn') }}'
  })[s]||s||'';
}

function renderItems(){
  var wrap=$('state-content');
  if(!state.items.length){wrap.innerHTML='';return;}
  wrap.innerHTML=state.items.map(function(it){
    var need=it.need||{};
    var pr=fmtPrice(it);
    var status=String(it.status||'PENDING').toLowerCase();
    var badge='<span class="badge '+esc(status)+'">'+esc(statusLabel(it.status))+'</span>';
    var needTitle=esc(need.title||'—');
    var msg=esc((it.proposal_message||'').slice(0,100));
    var created=it.created_at?fmtDate(it.created_at):'';
    var needStatus=need.status?esc(need.status):'';
    return '<a class="card" href="/offers/'+encodeURIComponent(it.id)+'">'+
      '<div class="card-top"><span class="need-cat">'+needTitle+'</span>'+badge+'</div>'+
      '<div class="price"><span class="cur">'+esc(pr.cur)+'</span>'+esc(pr.price)+'</div>'+
      '<div class="msg">'+msg+'</div>'+
      '<div class="meta"><span>'+esc(created)+'</span><span>'+needStatus+'</span></div>'+
    '</a>';
  }).join('');
}

function buildQuery(){
  var p=new URLSearchParams();
  if(state.status)p.set('status',state.status);
  p.set('page',state.page);
  p.set('per_page',state.perPage);
  return p.toString();
}

function loadOffers(){
  if(state.loading)return;
  state.loading=true;

  var token=getToken();
  if(false){/* cookie auth - always has session */}

  if(state.page===1&&state.items.length===0)showOnly('loading');

  fetch('/api/v1/my/offers?'+buildQuery(),{
    credentials:'same-origin',headers:{'Accept':'application/json'}
  })
  .then(function(r){
    if(r.status===401){localStorage.removeItem(LS_TOKEN);localStorage.removeItem('felagi_user');window.location.href='/';return null;}
    if(r.status===429)throw new Error('rate-limited');
    if(!r.ok)throw new Error('HTTP '+r.status);
    return r.json();
  })
  .then(function(j){
    if(!j)return;
    var items=Array.isArray(j.data)?j.data:[];
    var meta=j.meta||{};
    state.hasMore=meta.has_more!=null?!!meta.has_more:(items.length>=state.perPage);

    if(state.page===1)state.items=items;
    else state.items=state.items.concat(items);

    if(state.items.length===0){
      showOnly('empty');
    }else{
      renderItems();
      showOnly('content');
    }
    $('load-more-wrap').hidden=!state.hasMore;

    var cnt=$('offer-count');
    if(state.items.length){cnt.textContent=state.items.length;cnt.hidden=false;}
    else{cnt.hidden=true;}
  })
  .catch(function(e){
    console.warn('[S013] loadOffers',e);
    if(state.items.length===0)showOnly('error');
  })
  .then(function(){
    state.loading=false;
  });
}

var chips=$('filters').querySelectorAll('.chip');
Array.prototype.forEach.call(chips,function(btn){
  btn.addEventListener('click',function(){
    Array.prototype.forEach.call(chips,function(x){x.classList.toggle('active',x===btn);});
    state.status=btn.dataset.status||'';
    state.page=1;
    state.items=[];
    $('load-more-wrap').hidden=true;
    loadOffers();
  });
});

$('load-more-btn').addEventListener('click',function(){
  if(!state.hasMore||state.loading)return;
  state.page+=1;
  loadOffers();
});

window.addEventListener('offline',function(){$('offline-banner').className='offline-banner on';});
window.addEventListener('online',function(){$('offline-banner').className='offline-banner';loadOffers();});

if(navigator.onLine===false){$('offline-banner').className='offline-banner on';}

window.loadOffers=loadOffers;
loadOffers();
})();
</script>
</body>
</html>
