<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>{{ __('screenS018') }} — {{ __('brand') }}</title>
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
.sc{max-width:720px;margin:0 auto;min-height:100vh;display:flex;flex-direction:column}
.sh{flex:0 0 auto;color:#fff;padding:18px 22px 22px;position:relative;overflow:hidden;z-index:1}
.mesh{background:radial-gradient(ellipse at top right,rgba(255,153,51,.18),transparent 55%),radial-gradient(ellipse at bottom left,rgba(26,107,176,.35),transparent 60%),linear-gradient(140deg,var(--navy-950),var(--navy-800))}
.sh .tr{display:flex;align-items:center;justify-content:space-between;gap:12px;position:relative;z-index:1}
.sh .br{font-size:17px;font-weight:900;letter-spacing:-.03em;display:flex;align-items:center;gap:9px}
.ib{width:40px;height:40px;border-radius:50%;display:inline-flex;align-items:center;justify-content:center;color:#fff;background:rgba(255,255,255,.10);border:1px solid rgba(255,255,255,.08);transition:all .2s var(--ease)}
.ib:hover{background:rgba(255,255,255,.18)}
.ib svg{width:20px;height:20px;stroke:currentColor;fill:none;stroke-width:2;stroke-linecap:round;stroke-linejoin:round}
.sh .t{margin-top:14px;font-size:22px;font-weight:900;letter-spacing:-.03em;line-height:1.2;position:relative;z-index:1;display:flex;align-items:center;gap:10px}
.sh .count{background:linear-gradient(135deg,var(--orange-500),var(--orange-600));color:#fff;padding:5px 12px;border-radius:var(--r-pill);font-size:12px;font-weight:900;font-family:var(--f-en);letter-spacing:.02em;box-shadow:0 4px 10px rgba(255,153,51,.32)}
.sb{flex:1 1 auto;padding:18px 18px 40px;position:relative;z-index:1}
.chips{display:flex;gap:8px;flex-wrap:nowrap;overflow-x:auto;padding-bottom:8px;margin-bottom:14px;scrollbar-width:none}
.chips::-webkit-scrollbar{display:none}
.chip{padding:10px 16px;border-radius:var(--r-pill);border:1.5px solid var(--line-300);background:var(--surface);font-size:12.5px;font-weight:800;color:var(--ink-500);transition:all .2s var(--ease);white-space:nowrap;letter-spacing:-.005em}
.chip:hover{border-color:var(--navy-700);color:var(--navy-800)}
.chip.active{background:linear-gradient(135deg,var(--navy-800),var(--navy-700));color:#fff;border-color:var(--navy-800);box-shadow:0 4px 10px rgba(0,51,102,.24)}
.notif{background:var(--surface);border:1px solid var(--line-100);border-radius:var(--r-lg);padding:14px 16px;margin-bottom:10px;box-shadow:var(--sh-xs);display:flex;gap:12px;cursor:pointer;position:relative;transition:all .25s var(--ease-out);overflow:hidden}
.notif:hover{transform:translateY(-2px);box-shadow:var(--sh-sm)}
.notif.unread{background:linear-gradient(135deg,#f6faff,var(--surface));border-left:4px solid var(--navy-800)}
.notif .icon{width:42px;height:42px;border-radius:50%;background:linear-gradient(135deg,var(--navy-800),var(--navy-600));color:#fff;display:flex;align-items:center;justify-content:center;font-size:18px;flex:0 0 auto;box-shadow:0 2px 8px rgba(0,51,102,.16)}
.notif.accepted .icon{background:linear-gradient(135deg,var(--success-500),var(--success-600))}
.notif.rejected .icon{background:linear-gradient(135deg,#5f7185,#3c4d61)}
.notif.offer .icon{background:linear-gradient(135deg,var(--orange-500),var(--orange-600))}
.notif.need .icon{background:linear-gradient(135deg,var(--navy-700),var(--navy-600))}
.notif .body{flex:1;min-width:0}
.notif .title{font-weight:900;font-size:14px;color:var(--ink-900);margin-bottom:3px;display:-webkit-box;-webkit-line-clamp:1;-webkit-box-orient:vertical;overflow:hidden;letter-spacing:-.01em}
.notif .msg{font-size:12.5px;color:var(--ink-500);line-height:1.5;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden}
.notif .time{font-size:10.5px;color:var(--ink-300);margin-top:5px;font-weight:700;font-family:var(--f-en);letter-spacing:.02em}
.notif .dot{width:10px;height:10px;background:linear-gradient(135deg,var(--orange-500),var(--orange-600));border-radius:50%;flex:0 0 auto;margin-top:6px;box-shadow:0 0 0 4px rgba(255,153,51,.18)}
.state{padding:60px 24px;text-align:center;color:var(--ink-300)}
.state .ic{font-size:48px;margin-bottom:14px;opacity:.5}
.state h3{font-size:18px;font-weight:900;color:var(--navy-800);margin-bottom:8px}
.state p{font-size:13.5px;line-height:1.6;color:var(--ink-500);max-width:300px;margin:0 auto 16px}
.bt{display:inline-flex;align-items:center;justify-content:center;gap:9px;height:48px;padding:0 22px;border-radius:var(--r-sm);font-weight:900;font-size:14px;line-height:1;letter-spacing:-.015em;transition:all .2s var(--ease);font-family:var(--f-am);text-align:center}
.bt-n{background:linear-gradient(135deg,var(--navy-800),var(--navy-700));color:#fff}
.bt-n:hover{transform:translateY(-1px);box-shadow:var(--sh-navy)}
.bt-g{background:var(--surface);color:var(--navy-800);border:1.5px solid var(--line-300)}
.bt-g:hover{background:var(--canvas-2);border-color:var(--navy-700)}
.skeleton-card{background:var(--surface);border:1px solid var(--line-100);border-radius:var(--r-lg);padding:14px 16px;margin-bottom:10px;display:flex;gap:12px;box-shadow:var(--sh-xs)}
.sk-av{width:42px;height:42px;border-radius:50%;background:var(--line-100);flex:0 0 auto}
.sk-lines{flex:1}
.sk-line{height:12px;background:var(--line-100);border-radius:4px;margin-bottom:8px}
.sk-line.w40{width:40%}.sk-line.w70{width:70%}.sk-line.w90{width:90%}
.load-more-wrap{text-align:center;padding:16px 0}
.offline-banner{display:none;background:var(--warn-100);color:var(--warn-600);padding:10px 16px;border-radius:var(--r-sm);font-size:12.5px;font-weight:800;text-align:center;margin-bottom:12px}
.offline-banner.on{display:block}
.toast{position:fixed;bottom:100px;left:50%;transform:translateX(-50%) translateY(20px);background:var(--navy-900);color:#fff;padding:12px 20px;border-radius:var(--r-pill);font-size:13px;font-weight:700;opacity:0;pointer-events:none;transition:all .3s var(--ease-out);z-index:50;box-shadow:var(--sh-md);max-width:90vw;text-align:center}
.toast.on{opacity:1;transform:translateX(-50%) translateY(0)}
@media(min-width:600px){.sb{padding:24px 24px 48px}.sh{padding:20px 28px 26px}}
</style>
</head>
<body>
<div class="sc">
<div class="sh mesh">
  <div class="tr">
    <a href="/browse" class="ib" title="{{ __('back') }}" aria-label="{{ __('back') }}">
      <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M19 12H5M12 19l-7-7 7-7"/></svg>
    </a>
    <div class="br">
      <svg width="26" height="26" viewBox="0 0 32 32" fill="none" aria-hidden="true"><circle cx="16" cy="16" r="15" fill="url(#felagiGrad3)"/><path d="M9 10 L9 22 M9 12 L18 12 M9 17 L16 17 M18 12 L18 22" stroke="#ffffff" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"/><circle cx="23" cy="10" r="3" fill="#FF9933"/><path d="M20 20 L25 25" stroke="#ffffff" stroke-width="2.2" stroke-linecap="round" opacity="0.6"/><defs><linearGradient id="felagiGrad3" x1="0" y1="0" x2="32" y2="32"><stop offset="0" stop-color="#00264d"/><stop offset="1" stop-color="#0b4d83"/></linearGradient></defs></svg>
      <span>{{ __('brand') }}</span>
    </div>
  </div>
  <h1 class="t" id="page-title" tabindex="-1" role="heading" aria-level="1">
    <span>{{ __('navNotifications') }}</span>
    <span class="count" id="unread-count" hidden>0</span>
  </h1>
</div>
<div class="sb">
  <div class="offline-banner" id="offline-banner" role="status" aria-live="polite">{{ __('offlineBody') }}</div>

  <div class="chips" id="filters" role="tablist" aria-label="filters">
    <button type="button" class="chip active" data-unread="" role="tab" aria-selected="true">{{ __('filterAll') }}</button>
    <button type="button" class="chip" data-unread="1" role="tab" aria-selected="false">{{ __('filterUnread') }}</button>
  </div>

  <div id="state-loading">
    <div class="skeleton-card"><div class="sk-av"></div><div class="sk-lines"><div class="sk-line w40"></div><div class="sk-line w90"></div></div></div>
    <div class="skeleton-card"><div class="sk-av"></div><div class="sk-lines"><div class="sk-line w40"></div><div class="sk-line w90"></div></div></div>
    <div class="skeleton-card"><div class="sk-av"></div><div class="sk-lines"><div class="sk-line w40"></div><div class="sk-line w90"></div></div></div>
  </div>

  <div id="state-list" hidden aria-busy="false" role="feed"></div>

  <div id="state-empty" class="state" hidden>
    <div class="ic">🔔</div>
    <h3>{{ __('noNotificationsTitle') }}</h3>
    <p>{{ __('noNotificationsBody') }}</p>
  </div>

  <div id="state-error" class="state" hidden>
    <div class="ic">⚠️</div>
    <h3>{{ __('loadErrorTitle') }}</h3>
    <p>{{ __('loadErrorBody') }}</p>
    <button type="button" class="bt bt-n" onclick="loadNotifications()">{{ __('retry') }}</button>
  </div>

  <div class="load-more-wrap" id="load-more-wrap" hidden>
    <button type="button" class="bt bt-g" id="load-more-btn">{{ __('loadMore') }}</button>
  </div>
</div>
@include('partials.bottom-nav')
</div>
<div class="toast" id="toast" role="status" aria-live="polite"></div>
<script>
(function(){
'use strict';
var csrf=(document.querySelector('meta[name="csrf-token"]')||{}).content||'';
var LS_TOKEN='felagi_token';
var state={unreadOnly:false,page:1,perPage:20,hasMore:false,items:[],loading:false};
function $(id){return document.getElementById(id);}
function getToken(){return 'session';}
function esc(s){return String(s==null?'':s).replace(/[&<>"']/g,function(c){return {'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c];});}
function toast(msg,ms){ms=ms||2200;var el=$('toast');el.textContent=msg;el.className='toast on';setTimeout(function(){el.className='toast';},ms);}
function showOnly(name){
  ['state-loading','state-list','state-empty','state-error'].forEach(function(id){var el=$(id);if(el)el.hidden=(id!==('state-'+name));});
}
function fmtTime(iso){
  if(!iso)return '';
  var d=new Date(iso);
  if(isNaN(d))return '';
  var diff=Math.floor((Date.now()-d.getTime())/1000);
  if(diff<60)return '{{ __('justNow') }}';
  if(diff<3600)return Math.floor(diff/60)+'m';
  if(diff<86400)return Math.floor(diff/3600)+'h';
  if(diff<604800)return Math.floor(diff/86400)+'d';
  return d.toLocaleDateString();
}
function iconFor(n){
  var type=String(n.type||'').toLowerCase();
  var entity=String(n.entity_type||'').toLowerCase();
  if(type.indexOf('accept')!==-1)return {cls:'accepted',ic:'✓'};
  if(type.indexOf('reject')!==-1)return {cls:'rejected',ic:'✗'};
  if(entity==='offer'||type.indexOf('offer')!==-1)return {cls:'offer',ic:'🏷'};
  if(entity==='need'||type.indexOf('need')!==-1)return {cls:'need',ic:'📋'};
  return {cls:'',ic:'🔔'};
}
function linkFor(n){
  var entity=String(n.entity_type||'').toLowerCase();
  if(!n.entity_id)return null;
  if(entity==='offer')return '/offers/'+encodeURIComponent(n.entity_id);
  if(entity==='need')return '/needs/'+encodeURIComponent(n.entity_id);
  if(entity==='comparison')return '/comparisons/'+encodeURIComponent(n.entity_id);
  return null;
}
function renderItems(){
  var wrap=$('state-list');
  if(!state.items.length){wrap.innerHTML='';return;}
  wrap.innerHTML=state.items.map(function(n){
    var ico=iconFor(n);
    var href=linkFor(n);
    var tag=href?'a':'div';
    var hrefAttr=href?(' href="'+esc(href)+'"'):'';
    var unread=n.read_at?false:true;
    var cls='notif '+(ico.cls||'')+(unread?' unread':'');
    var dot=unread?'<span class="dot"></span>':'';
    return '<'+tag+' class="'+cls+'"'+hrefAttr+' data-id="'+esc(n.id)+'" data-unread="'+(unread?'1':'0')+'" role="article" tabindex="0">'+
      '<div class="icon">'+ico.ic+'</div>'+
      '<div class="body">'+
        '<div class="title">'+esc(n.title||'')+'</div>'+
        '<div class="msg">'+esc((n.body||'').slice(0,140))+'</div>'+
        '<div class="time">'+esc(fmtTime(n.created_at))+'</div>'+
      '</div>'+
      dot+
    '</'+tag+'>';
  }).join('');
  Array.prototype.forEach.call(wrap.querySelectorAll('.notif'),function(el){
    el.addEventListener('click',function(){
      var id=el.dataset.id;
      var wasUnread=el.dataset.unread==='1';
      if(wasUnread){
        markRead(id,function(){
          el.classList.remove('unread');
          el.dataset.unread='0';
          var dot=el.querySelector('.dot');
          if(dot)dot.remove();
          decrementUnread();
        });
      }
    });
  });
}
function updateUnread(count){
  var el=$('unread-count');
  if(count>0){el.textContent=count;el.hidden=false;}
  else{el.hidden=true;}
}
function decrementUnread(){
  var el=$('unread-count');
  var cur=parseInt(el.textContent||'0',10);
  if(cur>0)updateUnread(cur-1);
}
function markRead(id,cb){
  fetch('/api/v1/notifications/'+encodeURIComponent(id)+'/read',{
    method:'POST',
    headers:{'Accept':'application/json','X-CSRF-TOKEN':csrf}
  })
  .then(function(r){return r.ok?r.json():null;})
  .then(function(){if(cb)cb();})
  .catch(function(){if(cb)cb();});
}
function buildQuery(){var p=new URLSearchParams();if(state.unreadOnly)p.set('unread','1');p.set('page',state.page);p.set('per_page',state.perPage);return p.toString();}
function loadNotifications(){
  if(state.loading)return;state.loading=true;
  if(state.page===1&&state.items.length===0)showOnly('loading');
  var feed=$('state-list');if(feed)feed.setAttribute('aria-busy','true');
  fetch('/api/v1/notifications?'+buildQuery(),{credentials:'same-origin',headers:{'Accept':'application/json'}})
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
    else{renderItems();showOnly('list');}
    $('load-more-wrap').hidden=!state.hasMore;
    if(meta.unread_count!=null)updateUnread(parseInt(meta.unread_count,10)||0);
  })
  .catch(function(e){console.warn('[S018] loadNotifications',e);if(state.items.length===0)showOnly('error');})
  .then(function(){state.loading=false;if(feed)feed.setAttribute('aria-busy','false');});
}
var chips=$('filters').querySelectorAll('.chip');
Array.prototype.forEach.call(chips,function(btn){
  btn.addEventListener('click',function(){
    Array.prototype.forEach.call(chips,function(x){x.classList.toggle('active',x===btn);x.setAttribute('aria-selected',x===btn?'true':'false');});
    state.unreadOnly=btn.dataset.unread==='1';
    state.page=1;state.items=[];$('load-more-wrap').hidden=true;loadNotifications();
  });
});
$('load-more-btn').addEventListener('click',function(){if(!state.hasMore||state.loading)return;state.page+=1;loadNotifications();});
window.addEventListener('offline',function(){$('offline-banner').className='offline-banner on';});
window.addEventListener('online',function(){$('offline-banner').className='offline-banner';loadNotifications();});
if(navigator.onLine===false){$('offline-banner').className='offline-banner on';}
window.loadNotifications=loadNotifications;
loadNotifications();
})();
</script>
</body>
</html>
