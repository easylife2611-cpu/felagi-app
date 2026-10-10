<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>{{ __('screenS013') }} — {{ __('brand') }}</title>
<meta name="theme-color" content="#003366">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Ethiopic:wght@400;500;600;700;800;900&family=Inter:wght@400;600;700;800;900&display=swap" rel="stylesheet">
<style>
:root{--navy-950:#001a33;--navy-900:#002b52;--navy-800:#003366;--navy-700:#0b4d83;--navy-600:#1a6bb0;--orange-700:#b35c00;--orange-600:#e8851d;--orange-500:#FF9933;--orange-400:#ffb366;--orange-100:#fff2e0;--ink-900:#0d1a2b;--ink-500:#3c4d61;--ink-300:#5f7185;--ink-200:#8fa0b3;--line-100:#eef2f6;--line-200:#e3e9ef;--line-300:#d5dde6;--surface:#fff;--canvas:#f4f7fa;--canvas-2:#eef3f8;--success-600:#0e6b34;--success-500:#147a3d;--success-100:#e8f5ee;--warn-600:#a86500;--warn-100:#fff7e6;--danger-600:#a32e21;--danger-500:#c0392b;--danger-100:#fdecea;--info-600:#0056d6;--info-100:#e7f1ff;--r-xs:8px;--r-sm:12px;--r-md:16px;--r-lg:22px;--r-pill:999px;--sh-xs:0 1px 2px rgba(0,26,51,.04),0 1px 3px rgba(0,26,51,.06);--sh-sm:0 2px 6px rgba(0,26,51,.05),0 4px 12px rgba(0,26,51,.06);--sh-md:0 4px 12px rgba(0,26,51,.06),0 12px 28px rgba(0,26,51,.08);--sh-navy:0 12px 32px rgba(0,51,102,.28);--sh-orange:0 8px 20px rgba(255,153,51,.32);--f-am:'Noto Sans Ethiopic','Inter',system-ui,sans-serif;--f-en:'Inter','Noto Sans Ethiopic',system-ui,sans-serif;--ease:cubic-bezier(.16,.84,.44,1);--ease-out:cubic-bezier(.22,1,.36,1)}
*{box-sizing:border-box;margin:0;padding:0}
html,body{background:var(--canvas);color:var(--ink-900);font-family:var(--f-am);line-height:1.55;-webkit-font-smoothing:antialiased}
body{padding:0 0 100px;min-height:100vh}
a{color:inherit;text-decoration:none}
button{font-family:inherit;cursor:pointer;border:0;background:transparent;color:inherit}
:focus-visible{outline:3px solid var(--orange-500);outline-offset:3px;border-radius:8px}
.sr-only{position:absolute;width:1px;height:1px;padding:0;margin:-1px;overflow:hidden;clip:rect(0,0,0,0);white-space:nowrap;border:0}
.sc{max-width:960px;margin:0 auto;min-height:100vh;display:flex;flex-direction:column}
.sh{flex:0 0 auto;color:#fff;padding:18px 22px 22px;position:relative;overflow:hidden;z-index:1}
.mesh{background:radial-gradient(ellipse at top right,rgba(255,153,51,.18),transparent 55%),radial-gradient(ellipse at bottom left,rgba(26,107,176,.35),transparent 60%),linear-gradient(140deg,var(--navy-950),var(--navy-800))}
.sh .tr{display:flex;align-items:center;justify-content:space-between;gap:12px;position:relative;z-index:1}
.sh .br{font-size:17px;font-weight:900;letter-spacing:-.03em;display:flex;align-items:center;gap:9px}
.sh .ha{display:flex;gap:6px;align-items:center}
.ib{width:40px;height:40px;border-radius:50%;display:inline-flex;align-items:center;justify-content:center;color:#fff;background:rgba(255,255,255,.10);border:1px solid rgba(255,255,255,.08);transition:all .2s var(--ease);position:relative}
.ib:hover{background:rgba(255,255,255,.18)}
.ib svg{width:20px;height:20px;stroke:currentColor;fill:none;stroke-width:2;stroke-linecap:round;stroke-linejoin:round}
.sh .t{margin-top:14px;font-size:22px;font-weight:900;letter-spacing:-.03em;line-height:1.2;position:relative;z-index:1}
.sh .st{margin-top:4px;font-size:12.5px;opacity:.75;font-weight:500;position:relative;z-index:1}
.sb{flex:1 1 auto;padding:20px 18px 40px;position:relative;z-index:1}
.chips{display:flex;gap:8px;flex-wrap:nowrap;overflow-x:auto;padding-bottom:8px;margin-bottom:18px;scrollbar-width:none}
.chips::-webkit-scrollbar{display:none}
.chip{padding:10px 16px;border-radius:var(--r-pill);border:1.5px solid var(--line-300);background:var(--surface);font-size:12.5px;font-weight:800;color:var(--ink-500);transition:all .2s var(--ease);white-space:nowrap;letter-spacing:-.005em}
.chip:hover{border-color:var(--navy-700);color:var(--navy-800)}
.chip.active{background:linear-gradient(135deg,var(--navy-800),var(--navy-700));color:#fff;border-color:var(--navy-800);box-shadow:0 4px 10px rgba(0,51,102,.24)}
.feed{display:grid;grid-template-columns:1fr;gap:14px}
@media(min-width:640px){.feed{grid-template-columns:1fr 1fr}}
@media(min-width:1024px){.feed{grid-template-columns:1fr 1fr 1fr}}
.lst{background:var(--surface);border:1px solid var(--line-100);border-radius:var(--r-lg);padding:18px;box-shadow:var(--sh-xs);position:relative;overflow:hidden;transition:all .3s var(--ease-out);cursor:pointer;display:flex;flex-direction:column;gap:10px}
.lst::before{content:'';position:absolute;top:0;left:0;right:0;height:3px;background:linear-gradient(90deg,var(--orange-500),var(--navy-700));opacity:0;transition:opacity .3s var(--ease)}
.lst:hover{transform:translateY(-3px);box-shadow:var(--sh-md)}
.lst:hover::before{opacity:1}
.lst .top{display:flex;justify-content:space-between;align-items:flex-start;gap:10px}
.lst .need-title{font-size:12px;font-weight:900;color:var(--navy-800);background:var(--canvas-2);padding:5px 11px;border-radius:var(--r-pill);border:1px solid var(--line-200);max-width:65%;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;letter-spacing:-.005em}
.bg{display:inline-flex;align-items:center;gap:5px;padding:5px 11px;border-radius:var(--r-pill);font-size:10px;font-weight:800;letter-spacing:.05em;font-family:var(--f-en);flex:0 0 auto}
.bg::before{content:'●';font-size:6px}
.bg.pending{background:var(--warn-100);color:var(--warn-600)}
.bg.accepted{background:var(--success-100);color:var(--success-600)}
.bg.rejected,.bg.withdrawn{background:var(--line-100);color:var(--ink-500)}
.lst .price{font-size:22px;font-weight:900;color:var(--success-600);letter-spacing:-.03em;font-family:var(--f-en);display:flex;align-items:baseline;gap:4px}
.lst .price .cur{font-size:12px;color:var(--ink-300);font-weight:800;letter-spacing:.06em}
.lst .msg{font-size:13px;color:var(--ink-500);line-height:1.6;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden}
.lst .footer{margin-top:auto;padding-top:10px;border-top:1px solid var(--line-100);display:flex;justify-content:space-between;align-items:center;font-size:11px;color:var(--ink-300);font-weight:700}
.lst .footer .status-tag{color:var(--navy-800);font-weight:900;font-size:11.5px}
.state{padding:60px 24px;text-align:center;color:var(--ink-300)}
.state .ic{font-size:44px;margin-bottom:14px;opacity:.5}
.state h3{font-size:18px;font-weight:900;color:var(--navy-800);margin-bottom:8px}
.state p{font-size:13.5px;line-height:1.6;color:var(--ink-500);max-width:300px;margin:0 auto 16px}
.bt{display:inline-flex;align-items:center;justify-content:center;gap:9px;height:48px;padding:0 22px;border-radius:var(--r-sm);font-weight:900;font-size:14px;line-height:1;letter-spacing:-.015em;transition:all .2s var(--ease);font-family:var(--f-am);text-align:center}
.bt-n{background:linear-gradient(135deg,var(--navy-800),var(--navy-700));color:#fff}
.bt-n:hover{transform:translateY(-1px);box-shadow:var(--sh-navy)}
.bt-g{background:var(--surface);color:var(--navy-800);border:1.5px solid var(--line-300)}
.bt-g:hover{background:var(--canvas-2);border-color:var(--navy-700)}
.skeleton-card{background:var(--surface);border:1px solid var(--line-100);border-radius:var(--r-lg);padding:18px}
.sk-line{height:12px;background:var(--line-100);border-radius:4px;margin-bottom:10px}
.sk-line.w40{width:40%}.sk-line.w70{width:70%}.sk-line.w90{width:90%}.sk-line.w100{width:100%}
.load-more-wrap{text-align:center;padding:16px 0}
.offline-banner{display:none;background:var(--warn-100);color:var(--warn-600);padding:10px 16px;border-radius:var(--r-sm);font-size:12.5px;font-weight:800;text-align:center;margin-bottom:14px}
.offline-banner.on{display:block}
@media(min-width:600px){.sb{padding:28px 24px 48px}.sh{padding:20px 28px 26px}}
</style>
</head>
<body>
<div class="sc">
<div class="sh mesh">
  <div class="tr">
    <div class="br">
      <svg width="26" height="26" viewBox="0 0 32 32" fill="none" aria-hidden="true"><circle cx="16" cy="16" r="15" fill="url(#felagiGrad2)"/><path d="M9 10 L9 22 M9 12 L18 12 M9 17 L16 17 M18 12 L18 22" stroke="#ffffff" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"/><circle cx="23" cy="10" r="3" fill="#FF9933"/><path d="M20 20 L25 25" stroke="#ffffff" stroke-width="2.2" stroke-linecap="round" opacity="0.6"/><defs><linearGradient id="felagiGrad2" x1="0" y1="0" x2="32" y2="32"><stop offset="0" stop-color="#00264d"/><stop offset="1" stop-color="#0b4d83"/></linearGradient></defs></svg>
      <span>{{ __('brand') }}</span>
    </div>
    <div class="ha">
      <a href="/notifications" class="ib" aria-label="{{ __('navNotifications') }}" title="{{ __('navNotifications') }}">
        <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M18 8a6 6 0 0 0-12 0c0 7-3 9-3 9h18s-3-2-3-9M13.7 21a2 2 0 0 1-3.4 0"/></svg>
      </a>
    </div>
  </div>
  <h1 class="t" id="page-title" tabindex="-1" role="heading" aria-level="1">{{ __('myOffersTitle') }}</h1>
  <div class="st" id="offer-count-sub" hidden>0</div>
</div>
<div class="sb">
  <div class="offline-banner" id="offline-banner" role="status" aria-live="polite">{{ __('offlineBody') }}</div>

  <div class="chips" id="filters" role="tablist" aria-label="filters">
    <button type="button" class="chip active" data-status="" role="tab" aria-selected="true">{{ __('filterAll') }}</button>
    <button type="button" class="chip" data-status="PENDING" role="tab" aria-selected="false">{{ __('offerStatusPending') }}</button>
    <button type="button" class="chip" data-status="ACCEPTED" role="tab" aria-selected="false">{{ __('offerStatusAccepted') }}</button>
    <button type="button" class="chip" data-status="REJECTED" role="tab" aria-selected="false">{{ __('offerStatusRejected') }}</button>
    <button type="button" class="chip" data-status="WITHDRAWN" role="tab" aria-selected="false">{{ __('offerStatusWithdrawn') }}</button>
  </div>

  <div id="state-loading" class="feed">
    <div class="skeleton-card"><div class="sk-line w40"></div><div class="sk-line w90"></div><div class="sk-line w70"></div></div>
    <div class="skeleton-card"><div class="sk-line w40"></div><div class="sk-line w90"></div><div class="sk-line w70"></div></div>
    <div class="skeleton-card"><div class="sk-line w40"></div><div class="sk-line w90"></div><div class="sk-line w70"></div></div>
  </div>

  <div id="state-content" class="feed" hidden aria-busy="false" role="feed"></div>

  <div id="state-empty" class="state" hidden>
    <div class="ic">📋</div>
    <h3>{{ __('noMyOffersTitle') }}</h3>
    <p>{{ __('noMyOffersBody') }}</p>
    <a href="/browse" class="bt bt-n">{{ __('browseNeeds') }}</a>
  </div>

  <div id="state-error" class="state" hidden>
    <div class="ic">⚠️</div>
    <h3>{{ __('loadErrorTitle') }}</h3>
    <p>{{ __('loadErrorBody') }}</p>
    <button type="button" class="bt bt-n" onclick="loadOffers()">{{ __('retry') }}</button>
  </div>

  <div class="load-more-wrap" id="load-more-wrap" hidden>
    <button type="button" class="bt bt-g" id="load-more-btn">{{ __('loadMore') }}</button>
  </div>
</div>
@include('partials.bottom-nav')
</div>
<script>
(function(){
'use strict';
var LS_TOKEN='felagi_token';
var state={status:'',page:1,perPage:20,hasMore:false,items:[],loading:false};
function $(id){return document.getElementById(id);}
function getToken(){return 'session';}
function esc(s){return String(s==null?'':s).replace(/[&<>"']/g,function(c){return {'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c];});}
function showOnly(name){
  ['state-loading','state-content','state-empty','state-error'].forEach(function(id){
    var el=$(id);if(el)el.hidden=(id!==('state-'+name));
  });
}
function fmtPrice(it){
  var cur=it.currency||'ETB';
  var p=it.offered_price!=null?Number(it.offered_price).toFixed(2):'-';
  return {cur:cur,price:p};
}
function fmtDate(iso){if(!iso)return '';var d=new Date(iso);if(isNaN(d))return '';return d.toLocaleDateString();}
function statusLabel(s){return ({'PENDING':'{{ __('offerStatusPending') }}','ACCEPTED':'{{ __('offerStatusAccepted') }}','REJECTED':'{{ __('offerStatusRejected') }}','WITHDRAWN':'{{ __('offerStatusWithdrawn') }}'})[s]||s||'';}
function renderItems(){
  var wrap=$('state-content');
  if(!state.items.length){wrap.innerHTML='';return;}
  wrap.innerHTML=state.items.map(function(it){
    var need=it.need||{};
    var pr=fmtPrice(it);
    var status=String(it.status||'PENDING').toLowerCase();
    var badge='<span class="bg '+esc(status)+'">'+esc(statusLabel(it.status))+'</span>';
    var needTitle=esc(need.title||'—');
    var msg=esc((it.proposal_message||'').slice(0,100));
    var created=it.created_at?fmtDate(it.created_at):'';
    var needStatus=need.status?esc(need.status):'';
    return '<a class="lst" href="/offers/'+encodeURIComponent(it.id)+'" role="article" tabindex="0">'+
      '<div class="top"><span class="need-title">'+needTitle+'</span>'+badge+'</div>'+
      '<div class="price"><span class="cur">'+esc(pr.cur)+'</span>'+esc(pr.price)+'</div>'+
      '<div class="msg">'+msg+'</div>'+
      '<div class="footer"><span>'+esc(created)+'</span><span class="status-tag">'+needStatus+'</span></div>'+
    '</a>';
  }).join('');
}
function buildQuery(){var p=new URLSearchParams();if(state.status)p.set('status',state.status);p.set('page',state.page);p.set('per_page',state.perPage);return p.toString();}
function loadOffers(){
  if(state.loading)return;state.loading=true;
  if(state.page===1&&state.items.length===0)showOnly('loading');
  var feed=$('state-content');if(feed)feed.setAttribute('aria-busy','true');
  fetch('/api/v1/my/offers?'+buildQuery(),{credentials:'same-origin',headers:{'Accept':'application/json'}})
  .then(function(r){
    if(r.status===401){localStorage.removeItem(LS_TOKEN);localStorage.removeItem('felagi_user');window.location.href='/';return null;}
    if(r.status===429)throw new Error('rate-limited');
    if(!r.ok)throw new Error('HTTP '+r.status);
    return r.json();
  })
  .then(function(j){
    if(!j)return;
    var items=Array.isArray(j.data)?j.data:[];var meta=j.meta||{};
    state.hasMore=meta.has_more!=null?!!meta.has_more:(items.length>=state.perPage);
    if(state.page===1)state.items=items;else state.items=state.items.concat(items);
    if(state.items.length===0){showOnly('empty');}
    else{renderItems();showOnly('content');}
    $('load-more-wrap').hidden=!state.hasMore;
    var sub=$('offer-count-sub');
    if(state.items.length){if(sub){sub.textContent=state.items.length+' ';sub.hidden=false;}}
    else{if(sub)sub.hidden=true;}
  })
  .catch(function(e){console.warn('[S013] loadOffers',e);if(state.items.length===0)showOnly('error');})
  .then(function(){state.loading=false;if(feed)feed.setAttribute('aria-busy','false');});
}
var chips=$('filters').querySelectorAll('.chip');
Array.prototype.forEach.call(chips,function(btn){
  btn.addEventListener('click',function(){
    Array.prototype.forEach.call(chips,function(x){x.classList.toggle('active',x===btn);x.setAttribute('aria-selected',x===btn?'true':'false');});
    state.status=btn.dataset.status||'';state.page=1;state.items=[];$('load-more-wrap').hidden=true;loadOffers();
  });
});
$('load-more-btn').addEventListener('click',function(){if(!state.hasMore||state.loading)return;state.page+=1;loadOffers();});
window.addEventListener('offline',function(){$('offline-banner').className='offline-banner on';});
window.addEventListener('online',function(){$('offline-banner').className='offline-banner';loadOffers();});
if(navigator.onLine===false){$('offline-banner').className='offline-banner on';}
window.loadOffers=loadOffers;
loadOffers();
})();
</script>
</body>
</html>
