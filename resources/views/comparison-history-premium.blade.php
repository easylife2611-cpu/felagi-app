<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>{{ __('screenS016') }} — {{ __('brand') }}</title>
<meta name="theme-color" content="#003366">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Ethiopic:wght@400;500;600;700;800;900&family=Inter:wght@400;600;700;800;900&display=swap" rel="stylesheet">
<style>
:root{--navy-950:#001a33;--navy-900:#002b52;--navy-800:#003366;--navy-700:#0b4d83;--navy-600:#1a6bb0;--orange-700:#b35c00;--orange-600:#e8851d;--orange-500:#FF9933;--orange-400:#ffb366;--orange-100:#fff2e0;--ink-900:#0d1a2b;--ink-500:#3c4d61;--ink-300:#5f7185;--ink-200:#8fa0b3;--line-100:#eef2f6;--line-200:#e3e9ef;--line-300:#d5dde6;--surface:#fff;--canvas:#f4f7fa;--canvas-2:#eef3f8;--success-600:#0e6b34;--success-500:#147a3d;--success-100:#e8f5ee;--warn-600:#a86500;--warn-100:#fff7e6;--danger-600:#a32e21;--danger-500:#c0392b;--danger-100:#fdecea;--info-600:#0056d6;--info-100:#e7f1ff;--r-xs:8px;--r-sm:12px;--r-md:16px;--r-lg:22px;--r-pill:999px;--sh-xs:0 1px 2px rgba(0,26,51,.04),0 1px 3px rgba(0,26,51,.06);--sh-sm:0 2px 6px rgba(0,26,51,.05),0 4px 12px rgba(0,26,51,.06);--sh-md:0 4px 12px rgba(0,26,51,.06),0 12px 28px rgba(0,26,51,.08);--sh-navy:0 12px 32px rgba(0,51,102,.28);--sh-orange:0 8px 20px rgba(255,153,51,.32);--f-am:'Noto Sans Ethiopic','Inter',system-ui,sans-serif;--f-en:'Inter','Noto Sans Ethiopic',system-ui,sans-serif;--ease:cubic-bezier(.16,.84,.44,1);--ease-out:cubic-bezier(.22,1,.36,1)}
*{box-sizing:border-box;margin:0;padding:0}
html,body{background:var(--canvas);color:var(--ink-900);font-family:var(--f-am);line-height:1.55;-webkit-font-smoothing:antialiased}
body{padding:0 0 40px;min-height:100vh}
a{color:inherit;text-decoration:none}
button{font-family:inherit;cursor:pointer;border:0;background:transparent;color:inherit}
:focus-visible{outline:3px solid var(--orange-500);outline-offset:3px;border-radius:8px}
.sr-only{position:absolute;width:1px;height:1px;padding:0;margin:-1px;overflow:hidden;clip:rect(0,0,0,0);white-space:nowrap;border:0}
.sc{max-width:720px;margin:0 auto;min-height:100vh;display:flex;flex-direction:column}
.sh{flex:0 0 auto;color:#fff;padding:18px 22px 22px;position:relative;overflow:hidden;z-index:1}
.mesh{background:radial-gradient(ellipse at top right,rgba(255,153,51,.18),transparent 55%),radial-gradient(ellipse at bottom left,rgba(26,107,176,.35),transparent 60%),linear-gradient(140deg,var(--navy-950),var(--navy-800))}
.sh .tr{display:flex;align-items:center;gap:12px;position:relative;z-index:1}
.ib{width:40px;height:40px;border-radius:50%;display:inline-flex;align-items:center;justify-content:center;color:#fff;background:rgba(255,255,255,.10);border:1px solid rgba(255,255,255,.08);transition:all .2s var(--ease);flex:0 0 auto}
.ib:hover{background:rgba(255,255,255,.18)}
.ib svg{width:20px;height:20px;stroke:currentColor;fill:none;stroke-width:2;stroke-linecap:round;stroke-linejoin:round}
.sh .t{margin-top:12px;font-size:22px;font-weight:900;letter-spacing:-.03em;line-height:1.2;position:relative;z-index:1}
.sb{flex:1 1 auto;padding:20px 18px 40px;position:relative;z-index:1}
.intro{background:linear-gradient(135deg,var(--info-100),#f0f7ff);border:1px solid rgba(13,110,253,.18);border-radius:var(--r-md);padding:14px 16px;font-size:13px;line-height:1.6;color:var(--navy-800);margin-bottom:16px;font-weight:600}
.lst{background:var(--surface);border:1px solid var(--line-100);border-radius:var(--r-lg);padding:16px;margin-bottom:12px;box-shadow:var(--sh-xs);display:flex;gap:14px;transition:all .25s var(--ease-out);position:relative;overflow:hidden;cursor:pointer}
.lst:hover{transform:translateY(-2px);box-shadow:var(--sh-md)}
.lst::before{content:'';position:absolute;top:0;left:0;right:0;height:3px;background:linear-gradient(90deg,var(--orange-500),var(--navy-700));opacity:0;transition:opacity .3s var(--ease)}
.lst:hover::before{opacity:1}
.lst .ver{width:60px;height:60px;border-radius:var(--r-md);background:linear-gradient(135deg,var(--navy-800),var(--navy-700));color:#fff;display:flex;flex-direction:column;align-items:center;justify-content:center;flex:0 0 auto;box-shadow:0 4px 12px rgba(0,51,102,.24)}
.lst .ver .num{font-size:20px;font-weight:900;line-height:1;font-family:var(--f-en);letter-spacing:-.02em}
.lst .ver .lbl{font-size:8.5px;text-transform:uppercase;letter-spacing:.08em;margin-top:3px;opacity:.85;font-family:var(--f-en);font-weight:800}
.lst .body{flex:1;min-width:0}
.lst .body .badge-row{margin-bottom:8px}
.lst .body .meta{font-size:12px;color:var(--ink-300);display:flex;flex-wrap:wrap;gap:12px;font-weight:700;font-family:var(--f-en)}
.lst .body .meta span{display:inline-flex;align-items:center;gap:5px}
.lst .arrow{color:var(--ink-200);font-size:24px;align-self:center;transition:all .2s var(--ease);font-family:var(--f-en)}
.lst:hover .arrow{color:var(--orange-500);transform:translateX(4px)}
.bg{display:inline-flex;align-items:center;gap:5px;padding:5px 11px;border-radius:var(--r-pill);font-size:10px;font-weight:800;letter-spacing:.05em;font-family:var(--f-en);text-transform:uppercase}
.bg::before{content:'●';font-size:6px}
.bg.pending{background:var(--warn-100);color:var(--warn-600)}
.bg.processing{background:var(--info-100);color:var(--info-600)}
.bg.completed{background:var(--success-100);color:var(--success-600)}
.bg.failed{background:var(--danger-100);color:var(--danger-600)}
.bg.cancelled{background:var(--line-100);color:var(--ink-500)}
.actions-bottom{margin-top:20px;display:flex;gap:10px;flex-wrap:wrap}
.bt{display:inline-flex;align-items:center;justify-content:center;gap:9px;height:48px;padding:0 22px;border-radius:var(--r-sm);font-weight:900;font-size:14px;line-height:1;letter-spacing:-.015em;transition:all .2s var(--ease);font-family:var(--f-am);text-align:center;flex:1;min-width:140px}
.bt-n{background:linear-gradient(135deg,var(--navy-800),var(--navy-700));color:#fff}
.bt-n:hover{transform:translateY(-1px);box-shadow:var(--sh-navy)}
.bt-g{background:var(--surface);color:var(--navy-800);border:1.5px solid var(--line-300)}
.bt-g:hover{background:var(--canvas-2);border-color:var(--navy-700)}
.state{padding:60px 24px;text-align:center;color:var(--ink-300)}
.state .ic{font-size:48px;margin-bottom:14px;opacity:.5}
.state h3{font-size:18px;font-weight:900;color:var(--navy-800);margin-bottom:8px}
.state p{font-size:13.5px;line-height:1.6;color:var(--ink-500);max-width:320px;margin:0 auto 16px}
.spinner{display:inline-block;width:26px;height:26px;border:3px solid var(--line-200);border-top-color:var(--navy-800);border-radius:50%;animation:spin .8s linear infinite}
@keyframes spin{to{transform:rotate(360deg)}}
.offline-banner{display:none;background:var(--warn-100);color:var(--warn-600);padding:10px 16px;border-radius:var(--r-sm);font-size:12.5px;font-weight:800;text-align:center;margin-bottom:14px}
.offline-banner.on{display:block}
.toast{position:fixed;bottom:100px;left:50%;transform:translateX(-50%) translateY(20px);background:var(--navy-900);color:#fff;padding:12px 20px;border-radius:var(--r-pill);font-size:13px;font-weight:700;opacity:0;pointer-events:none;transition:all .3s var(--ease-out);z-index:50;box-shadow:var(--sh-md);max-width:90vw;text-align:center}
.toast.on{opacity:1;transform:translateX(-50%) translateY(0)}
@media(min-width:600px){.sb{padding:28px 24px 48px}.sh{padding:20px 28px 26px}}
</style>
</head>
<body>
<div class="sc">
<div class="sh mesh">
  <div class="tr">
    <a href="#" id="back-btn" class="ib" title="{{ __('back') }}" aria-label="{{ __('back') }}">
      <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M19 12H5M12 19l-7-7 7-7"/></svg>
    </a>
    <h1 class="t" id="page-title" tabindex="-1" role="heading" aria-level="1">{{ __('comparisonHistoryTitle') }}</h1>
  </div>
</div>
<div class="sb">
  <div class="offline-banner" id="offline-banner" role="status" aria-live="polite">{{ __('offlineBody') }}</div>
  <div class="intro">{{ __('comparisonHistoryIntro') }}</div>

  <div id="state-loading" class="state" style="padding:60px 24px">
    <div class="spinner"></div>
  </div>

  <div id="state-empty" class="state" hidden>
    <div class="ic">📊</div>
    <h3>{{ __('noComparisonsTitle') }}</h3>
    <p>{{ __('noComparisonsBody') }}</p>
    <a href="#" id="need-btn-empty" class="bt bt-g" style="display:inline-flex;max-width:220px;margin:0 auto">{{ __('backToNeed') }}</a>
  </div>

  <div id="state-error" class="state" hidden>
    <div class="ic">⚠️</div>
    <h3>{{ __('loadErrorTitle') }}</h3>
    <p>{{ __('loadErrorBody') }}</p>
    <button type="button" class="bt bt-n" onclick="loadHistory()">{{ __('retry') }}</button>
  </div>

  <div id="list" hidden aria-busy="false" role="feed"></div>

  <div class="actions-bottom" id="bottom-actions" hidden>
    <a href="#" id="need-btn" class="bt bt-g">{{ __('backToNeed') }}</a>
    <a href="#" id="new-comp-btn" class="bt bt-n">{{ __('newComparison') }}</a>
  </div>
</div>
</div>
<div class="toast" id="toast" role="status" aria-live="polite"></div>
<script>
(function(){
'use strict';
var LS_TOKEN='felagi_token';
var needId='';
var comparisons=[];
function $(id){return document.getElementById(id);}
function getToken(){return 'session';}
function esc(s){return String(s==null?'':s).replace(/[&<>"']/g,function(c){return {'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c];});}
function getNeedId(){var parts=window.location.pathname.split('/').filter(Boolean);return parts.length>=2?parts[1]:'';}
function toast(msg,ms){ms=ms||2400;var el=$('toast');el.textContent=msg;el.className='toast on';setTimeout(function(){el.className='toast';},ms);}
function showOnly(name){
  ['state-loading','state-empty','state-error','list'].forEach(function(id){var el=$(id);if(el)el.hidden=(id!==('state-'+name));});
  var ba=$('bottom-actions');if(ba)ba.hidden=(name!=='list');
}
function fmtDate(iso){if(!iso)return '—';var d=new Date(iso);if(isNaN(d))return '—';return d.toLocaleDateString()+' '+d.toLocaleTimeString([],{hour:'2-digit',minute:'2-digit'});}
function statusLabel(s){return ({'PENDING':'{{ __('compStatusPending') }}','PROCESSING':'{{ __('compStatusProcessing') }}','COMPLETED':'{{ __('compStatusCompleted') }}','FAILED':'{{ __('compStatusFailed') }}','CANCELLED':'{{ __('compStatusCancelled') }}'})[s]||s||'';}
function renderList(){
  if(!comparisons.length){showOnly('empty');return;}
  $('list').innerHTML=comparisons.map(function(c){
    var s=String(c.status||'').toLowerCase();
    var badge='<span class="bg '+esc(s)+'">'+esc(statusLabel(c.status))+'</span>';
    var offerCount=c.included_offer_count!=null?(c.included_offer_count+' {{ __('offers') }}'):'';
    var requested=fmtDate(c.requested_at);
    return '<a class="lst" href="/comparisons/'+encodeURIComponent(c.id)+'" role="article" tabindex="0">'+
      '<div class="ver"><div class="num">#'+(c.version_number||1)+'</div><div class="lbl">{{ __('version') }}</div></div>'+
      '<div class="body">'+
        '<div class="badge-row">'+badge+'</div>'+
        '<div class="meta"><span>📅 '+esc(requested)+'</span>'+(offerCount?'<span>📋 '+esc(offerCount)+'</span>':'')+'</div>'+
      '</div>'+
      '<div class="arrow">›</div>'+
    '</a>';
  }).join('');
  showOnly('list');
}
function loadHistory(){
  needId=getNeedId();
  if(!needId){showOnly('error');return;}
  var backHref='/needs/'+encodeURIComponent(needId);
  $('back-btn').href=backHref;
  $('need-btn').href=backHref;
  $('need-btn-empty').href=backHref;
  $('new-comp-btn').href='/needs/'+encodeURIComponent(needId)+'/compare';
  showOnly('loading');
  fetch('/api/v1/needs/'+encodeURIComponent(needId)+'/comparisons',{credentials:'same-origin',headers:{'Accept':'application/json'}})
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
    if(!comparisons.length){showOnly('empty');return;}
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
