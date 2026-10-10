<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>{{ __('screenS010') }} — {{ __('brand') }}</title>
<meta name="theme-color" content="#003366">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Ethiopic:wght@400;500;600;700;800;900&family=Inter:wght@400;600;700;800;900&display=swap" rel="stylesheet">
<style>
:root{--navy-950:#001a33;--navy-900:#002b52;--navy-800:#003366;--navy-700:#0b4d83;--navy-600:#1a6bb0;--orange-700:#b35c00;--orange-600:#e8851d;--orange-500:#FF9933;--orange-400:#ffb366;--orange-100:#fff2e0;--ink-900:#0d1a2b;--ink-500:#3c4d61;--ink-300:#5f7185;--ink-200:#8fa0b3;--line-100:#eef2f6;--line-200:#e3e9ef;--line-300:#d5dde6;--surface:#fff;--canvas:#f4f7fa;--canvas-2:#eef3f8;--success-600:#0e6b34;--success-500:#147a3d;--success-100:#e8f5ee;--warn-600:#a86500;--warn-100:#fff7e6;--danger-600:#a32e21;--danger-100:#fdecea;--info-600:#0056d6;--info-100:#e7f1ff;--r-xs:8px;--r-sm:12px;--r-md:16px;--r-lg:22px;--r-xl:28px;--r-pill:999px;--sh-xs:0 1px 2px rgba(0,26,51,.04),0 1px 3px rgba(0,26,51,.06);--sh-sm:0 2px 6px rgba(0,26,51,.05),0 4px 12px rgba(0,26,51,.06);--sh-md:0 4px 12px rgba(0,26,51,.06),0 12px 28px rgba(0,26,51,.08);--sh-navy:0 12px 32px rgba(0,51,102,.28);--sh-orange:0 8px 20px rgba(255,153,51,.32);--f-am:'Noto Sans Ethiopic','Inter',system-ui,sans-serif;--f-en:'Inter','Noto Sans Ethiopic',system-ui,sans-serif;--ease:cubic-bezier(.16,.84,.44,1);--ease-out:cubic-bezier(.22,1,.36,1)}
*{box-sizing:border-box;margin:0;padding:0}
html,body{background:var(--canvas);color:var(--ink-900);font-family:var(--f-am);line-height:1.55;-webkit-font-smoothing:antialiased}
body{padding:0 0 80px;min-height:100vh}
a{color:inherit;text-decoration:none}
button{font-family:inherit;cursor:pointer;border:0;background:transparent;color:inherit}
:focus-visible{outline:3px solid var(--orange-500);outline-offset:3px;border-radius:8px}
.sr-only{position:absolute;width:1px;height:1px;padding:0;margin:-1px;overflow:hidden;clip:rect(0,0,0,0);white-space:nowrap;border:0}
.sc{max-width:960px;margin:0 auto;min-height:100vh;display:flex;flex-direction:column}
.sh{flex:0 0 auto;color:#fff;padding:18px 22px 22px;position:relative;overflow:hidden;z-index:1}
.mesh{background:radial-gradient(ellipse at top right,rgba(255,153,51,.18),transparent 55%),radial-gradient(ellipse at bottom left,rgba(26,107,176,.35),transparent 60%),linear-gradient(140deg,var(--navy-950),var(--navy-800))}
.sh .tr{display:flex;align-items:center;justify-content:space-between;gap:12px;position:relative;z-index:1}
.ib{width:40px;height:40px;border-radius:50%;display:inline-flex;align-items:center;justify-content:center;color:#fff;background:rgba(255,255,255,.10);border:1px solid rgba(255,255,255,.08);transition:all .2s var(--ease)}
.ib:hover{background:rgba(255,255,255,.18)}
.ib svg{width:20px;height:20px;stroke:currentColor;fill:none;stroke-width:2;stroke-linecap:round;stroke-linejoin:round}
.sh .t{margin-top:14px;font-size:22px;font-weight:900;letter-spacing:-.03em;line-height:1.2;position:relative;z-index:1}
.sh .st{margin-top:4px;font-size:12.5px;opacity:.75;font-weight:500;position:relative;z-index:1}
.sb{flex:1 1 auto;padding:20px 18px 40px;position:relative;z-index:1}
.need-banner{background:linear-gradient(135deg,var(--canvas-2),var(--surface));border:1px solid var(--line-200);border-radius:var(--r-md);padding:14px 16px;margin-bottom:14px}
.need-banner .lbl{font-size:10.5px;color:var(--ink-300);text-transform:uppercase;letter-spacing:.08em;font-weight:800;margin-bottom:5px;font-family:var(--f-en)}
.need-banner .title{font-size:15px;font-weight:900;color:var(--ink-900);line-height:1.3;letter-spacing:-.02em}
.need-banner .meta{font-size:11.5px;color:var(--ink-300);margin-top:4px;font-weight:600}
.compare-bar{background:linear-gradient(135deg,var(--info-100),#f0f7ff);border:1px solid rgba(13,110,253,.18);border-radius:var(--r-md);padding:14px 16px;margin-bottom:14px;display:flex;justify-content:space-between;align-items:center;gap:12px;flex-wrap:wrap}
.compare-bar .info{font-size:12.5px;color:var(--navy-800);font-weight:800}
.compare-bar .btn-compare{padding:10px 18px;background:linear-gradient(135deg,var(--navy-800),var(--navy-700));color:#fff;border-radius:var(--r-sm);font-size:13px;font-weight:900;border:0;cursor:pointer;font-family:var(--f-am);box-shadow:var(--sh-navy);transition:all .2s var(--ease)}
.compare-bar .btn-compare:hover:not(:disabled){transform:translateY(-1px)}
.compare-bar .btn-compare:disabled{opacity:.5;cursor:not-allowed}
.feed{display:grid;grid-template-columns:1fr;gap:14px}
@media(min-width:640px){.feed{grid-template-columns:1fr 1fr}}
.lst{background:var(--surface);border:1px solid var(--line-100);border-radius:var(--r-lg);padding:18px;box-shadow:var(--sh-xs);position:relative;overflow:hidden;transition:all .3s var(--ease-out);cursor:pointer;display:flex;flex-direction:column;gap:10px}
.lst::before{content:'';position:absolute;top:0;left:0;right:0;height:3px;background:linear-gradient(90deg,var(--orange-500),var(--navy-700));opacity:0;transition:opacity .3s var(--ease)}
.lst:hover{transform:translateY(-3px);box-shadow:var(--sh-md)}
.lst:hover::before{opacity:1}
.lst .top{display:flex;justify-content:space-between;align-items:flex-start;gap:10px}
.provider{display:flex;align-items:center;gap:10px;flex:1;min-width:0}
.av{width:44px;height:44px;border-radius:50%;background:linear-gradient(135deg,var(--navy-800),var(--navy-600));color:#fff;display:flex;align-items:center;justify-content:center;font-weight:900;font-size:15px;flex:0 0 auto;overflow:hidden;box-shadow:0 2px 8px rgba(0,51,102,.16)}
.av.orange{background:linear-gradient(135deg,var(--orange-500),var(--orange-600))}
.av img{width:100%;height:100%;object-fit:cover}
.provider .name{font-weight:900;font-size:14px;color:var(--ink-900);letter-spacing:-.01em;overflow:hidden;text-overflow:ellipsis;white-space:nowrap}
.provider .meta{font-size:11px;color:var(--ink-300);font-weight:600;margin-top:2px}
.rating{color:var(--orange-600);font-weight:800}
.bg{display:inline-flex;align-items:center;gap:5px;padding:5px 11px;border-radius:var(--r-pill);font-size:10px;font-weight:800;letter-spacing:.05em;font-family:var(--f-en);flex:0 0 auto}
.bg::before{content:'●';font-size:6px}
.bg.pending{background:var(--warn-100);color:var(--warn-600)}
.bg.accepted{background:var(--success-100);color:var(--success-600)}
.bg.rejected,.bg.withdrawn{background:var(--line-100);color:var(--ink-500)}
.offer-price{font-size:22px;font-weight:900;color:var(--success-600);letter-spacing:-.03em;font-family:var(--f-en);display:flex;align-items:baseline;gap:4px}
.offer-price .cur{font-size:12px;color:var(--ink-300);font-weight:800;letter-spacing:.06em}
.offer-msg{font-size:13px;color:var(--ink-500);line-height:1.6;display:-webkit-box;-webkit-line-clamp:3;-webkit-box-orient:vertical;overflow:hidden}
.offer-meta{display:flex;flex-direction:column;gap:4px;font-size:11.5px;color:var(--ink-300);font-weight:600}
.offer-meta .row{display:flex;gap:6px;align-items:center}
.lst .footer{margin-top:auto;padding-top:10px;border-top:1px solid var(--line-100);display:flex;justify-content:space-between;align-items:center;font-size:11px;color:var(--ink-300);font-weight:700}
.view-link{color:var(--navy-800);font-weight:900;font-size:12px}
.lst:hover .view-link{color:var(--orange-600)}
.state{padding:60px 24px;text-align:center;color:var(--ink-300)}
.state .ic{font-size:44px;margin-bottom:14px;opacity:.5}
.state h3{font-size:18px;font-weight:900;color:var(--navy-800);margin-bottom:8px}
.state p{font-size:13.5px;line-height:1.6;color:var(--ink-500);max-width:300px;margin:0 auto 16px}
.bt{display:inline-flex;align-items:center;justify-content:center;gap:9px;height:46px;padding:0 22px;border-radius:var(--r-sm);font-weight:900;font-size:14px;line-height:1;letter-spacing:-.015em;transition:all .2s var(--ease);font-family:var(--f-am);text-align:center}
.bt-n{background:linear-gradient(135deg,var(--navy-800),var(--navy-700));color:#fff}
.bt-n:hover{transform:translateY(-1px);box-shadow:var(--sh-navy)}
.bt-g{background:var(--surface);color:var(--navy-800);border:1.5px solid var(--line-300)}
.bt-g:hover{background:var(--canvas-2);border-color:var(--navy-700)}
.skeleton-card{background:var(--surface);border:1px solid var(--line-100);border-radius:var(--r-lg);padding:18px;margin-bottom:14px}
.sk-line{height:12px;background:var(--line-100);border-radius:4px;margin-bottom:10px}
.sk-line.w40{width:40%}.sk-line.w70{width:70%}.sk-line.w90{width:90%}.sk-line.w100{width:100%}
.offline-banner{display:none;background:var(--warn-100);color:var(--warn-600);padding:10px 16px;border-radius:var(--r-sm);font-size:12.5px;font-weight:800;text-align:center;margin-bottom:14px}
.offline-banner.on{display:block}
.toast{position:fixed;bottom:100px;left:50%;transform:translateX(-50%) translateY(20px);background:var(--navy-900);color:#fff;padding:12px 20px;border-radius:var(--r-pill);font-size:13px;font-weight:700;opacity:0;pointer-events:none;transition:all .3s var(--ease-out);z-index:50;box-shadow:var(--sh-md);max-width:90vw;text-align:center}
.toast.on{opacity:1;transform:translateX(-50%) translateY(0)}
@media(min-width:600px){.sb{padding:28px 24px 48px}.lst{padding:20px}}
</style>
</head>
<body>
<div class="sc">
<div class="sh mesh">
  <div class="tr">
    <a href="#" id="back-btn" class="ib" title="{{ __('back') }}" aria-label="{{ __('back') }}">
      <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M19 12H5M12 19l-7-7 7-7"/></svg>
    </a>
    <span class="bg info" id="offer-count" hidden>0</span>
  </div>
  <h1 class="t" id="page-title" tabindex="-1" role="heading" aria-level="1">{{ __('receivedOffersTitle') }}</h1>
  <div class="st" id="offer-count-sub" hidden>0 {{ __('receivedOffersTitle') }}</div>
</div>
<div class="sb">
  <div class="offline-banner" id="offline-banner" role="status" aria-live="polite">{{ __('offlineBody') }}</div>

  <div id="state-loading">
    <div class="skeleton-card"><div class="sk-line w40"></div><div class="sk-line w90"></div><div class="sk-line w70"></div></div>
    <div class="skeleton-card"><div class="sk-line w40"></div><div class="sk-line w90"></div><div class="sk-line w70"></div></div>
  </div>

  <div id="state-denied" class="state" hidden>
    <div class="ic">🔒</div>
    <h3>{{ __('accessDenied') }}</h3>
    <p>{{ __('accessDeniedOffers') }}</p>
    <a href="/my/needs" class="bt bt-n">{{ __('backToMyNeeds') }}</a>
  </div>

  <div id="state-error" class="state" hidden>
    <div class="ic">⚠️</div>
    <h3>{{ __('loadErrorTitle') }}</h3>
    <p>{{ __('loadErrorBody') }}</p>
    <button type="button" class="bt bt-n" onclick="loadOffers()">{{ __('retry') }}</button>
  </div>

  <div id="state-empty" class="state" hidden>
    <div class="ic">📭</div>
    <h3>{{ __('noOffersTitle') }}</h3>
    <p>{{ __('noOffersBody') }}</p>
    <a href="#" id="back-need-btn" class="bt bt-g">{{ __('backToNeed') }}</a>
  </div>

  <div id="content" hidden>
    <div class="need-banner" id="need-banner" hidden>
      <div class="lbl">{{ __('needDetails') }}</div>
      <div class="title" id="need-title"></div>
      <div class="meta" id="need-meta"></div>
    </div>
    <div class="compare-bar" id="compare-bar" hidden>
      <span class="info" id="compare-info">{{ __('compareHint') }}</span>
      <button type="button" class="btn-compare" id="btn-compare">{{ __('compareOffers') }}</button>
    </div>
    <div id="state-list" class="feed"></div>
  </div>
</div>
</div>
<div class="toast" id="toast" role="status" aria-live="polite"></div>
<script>
function felagiLocalizedName(obj){if(!obj)return '';var am=document.documentElement.lang==='am';return am?(obj.name_am||obj.name_en||obj.slug||''):(obj.name_en||obj.name_am||obj.slug||'');}
(function(){
'use strict';
var csrf=(document.querySelector('meta[name="csrf-token"]')||{}).content||'';
var LS_TOKEN='felagi_token';
var needId='';
var offers=[];
var MIN_COMPARE=2;
function $(id){return document.getElementById(id);}
function getToken(){return 'session';}
function esc(s){return String(s==null?'':s).replace(/[&<>"']/g,function(c){return {'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c];});}
function getNeedId(){var parts=window.location.pathname.split('/').filter(Boolean);return parts.length>=2?parts[1]:'';}
function toast(msg,ms){ms=ms||2500;var el=$('toast');el.textContent=msg;el.className='toast on';setTimeout(function(){el.className='toast';},ms);}
function showOnly(name){['state-loading','state-denied','state-error','state-empty','content'].forEach(function(id){var el=$(id);if(el)el.hidden=(id!==((name==='content')?'content':'state-'+name));});}
function fmtPrice(it){var cur=it.currency||'ETB';var price=it.offered_price!=null?Number(it.offered_price).toFixed(2):'-';return {cur:cur,price:price};}
function fmtDate(iso){if(!iso)return '';var d=new Date(iso);if(isNaN(d))return '';return d.toLocaleDateString()+' '+d.toLocaleTimeString([],{hour:'2-digit',minute:'2-digit'});}
function statusLabel(s){return ({'PENDING':'{{ __('offerStatusPending') }}','ACCEPTED':'{{ __('offerStatusAccepted') }}','REJECTED':'{{ __('offerStatusRejected') }}','WITHDRAWN':'{{ __('offerStatusWithdrawn') }}'})[s]||s||'';}
function renderNeedBanner(need){
  if(!need||!need.id){$('need-banner').hidden=true;return;}
  $('need-banner').hidden=false;
  $('need-title').textContent=need.title||'';
  var meta=[];var cat=need.category||{};var catName=felagiLocalizedName(cat);
  if(catName)meta.push(catName);
  if(need.status)meta.push(need.status);
  if(need.location_text)meta.push('📍 '+esc(need.location_text));
  $('need-meta').innerHTML=meta.join(' · ');
  $('back-btn').href='/needs/'+encodeURIComponent(needId);
  var bb=$('back-need-btn');if(bb)bb.href='/needs/'+encodeURIComponent(needId);
}
function renderOfferCard(off){
  var prov=off.provider||{};
  var initial=String(prov.full_name||'?').trim().charAt(0).toUpperCase();
  var avatar=prov.profile_photo_url?'<img src="'+esc(prov.profile_photo_url)+'" alt="">':esc(initial);
  var rating=prov.rating_score?'<span class="rating">★ '+Number(prov.rating_score).toFixed(1)+'</span>':'';
  var ratingCount=prov.rating_count?' ('+prov.rating_count+')':'';
  var pr=fmtPrice(off);
  var status=String(off.status||'PENDING').toLowerCase();
  var statusBadge='<span class="bg '+esc(status)+'">'+esc(statusLabel(off.status))+'</span>';
  var delivery=off.delivery_time_text?'🕐 '+esc(off.delivery_time_text):'';
  var availability=off.availability_text?'📅 '+esc(off.availability_text):'';
  var created=off.created_at?fmtDate(off.created_at):'';
  var oid=encodeURIComponent(off.id);
  return '<a class="lst" href="/offers/'+oid+'" role="article" tabindex="0">'+
    '<div class="top">'+
      '<div class="provider">'+
        '<div class="av">'+avatar+'</div>'+
        '<div style="min-width:0">'+
          '<div class="name">'+esc(prov.full_name||'{{ __('anonymous') }}')+'</div>'+
          '<div class="meta">'+rating+ratingCount+'</div>'+
        '</div>'+
      '</div>'+statusBadge+
    '</div>'+
    '<div class="offer-price"><span class="cur">'+esc(pr.cur)+'</span>'+esc(pr.price)+'</div>'+
    '<div class="offer-msg">'+esc(off.proposal_message||'')+'</div>'+
    ((delivery||availability)?'<div class="offer-meta">'+(delivery?'<div class="row">'+delivery+'</div>':'')+(availability?'<div class="row">'+availability+'</div>':'')+'</div>':'')+
    '<div class="footer"><span>'+esc(created)+'</span><span class="view-link">{{ __('viewDetails') }} →</span></div>'+
  '</a>';
}
function renderList(){var wrap=$('state-list');if(!offers.length){wrap.innerHTML='';return;}wrap.innerHTML=offers.map(renderOfferCard).join('');}
function updateCompareBar(){
  var bar=$('compare-bar');if(!bar)return;
  var pendingCount=offers.filter(function(o){return o.status==='PENDING';}).length;
  if(offers.length>=MIN_COMPARE&&pendingCount>=MIN_COMPARE){bar.hidden=false;$('compare-info').textContent='{{ __('compareHint') }} ('+pendingCount+')';}
  else{bar.hidden=true;}
}
function updateCount(){
  var el=$('offer-count');var sub=$('offer-count-sub');
  if(!el)return;
  if(offers.length){el.textContent=offers.length;el.hidden=false;if(sub){sub.textContent=offers.length+' {{ __('receivedOffersTitle') }}';sub.hidden=false;}}
  else{el.hidden=true;if(sub)sub.hidden=true;}
}
function loadOffers(){
  needId=getNeedId();
  if(!needId){showOnly('error');return;}
  showOnly('loading');
  var h={'Accept':'application/json'};
  fetch('/api/v1/needs/'+encodeURIComponent(needId)+'/offers',{headers:h})
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
      offers=Array.isArray(data)?data:[];
      fetch('/api/v1/needs/'+encodeURIComponent(needId),{headers:{'Accept':'application/json'}})
        .then(function(r){return r.ok?r.json():null;})
        .then(function(j2){var need=(j2&&j2.data)?j2.data:j2;renderNeedBanner(need);})
        .catch(function(){$('back-btn').href='/needs/'+encodeURIComponent(needId);});
      if(!offers.length){showOnly('empty');updateCount();return;}
      renderList();updateCompareBar();updateCount();showOnly('content');
    })
    .catch(function(e){
      var m=String(e.message||e);
      if(m==='auth'){localStorage.removeItem(LS_TOKEN);window.location.href='/';return;}
      if(m==='denied'){showOnly('denied');return;}
      showOnly('error');
    });
}
window.loadOffers=loadOffers;
var btnCompare=$('btn-compare');
if(btnCompare){btnCompare.addEventListener('click',function(){window.location.href='/needs/'+encodeURIComponent(needId)+'/compare';});}
window.addEventListener('offline',function(){$('offline-banner').className='offline-banner on';});
window.addEventListener('online',function(){$('offline-banner').className='offline-banner';});
if(navigator.onLine===false){$('offline-banner').className='offline-banner on';}
loadOffers();
})();
</script>
</body>
</html>
