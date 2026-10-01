<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>{{ __('navNotifications') }} — {{ __('brand') }}</title>
<style>
*{box-sizing:border-box;margin:0;padding:0}
body{font-family:system-ui,-apple-system,sans-serif;background:#F4F6F8;color:#192431;line-height:1.5;min-height:100vh;padding-bottom:80px}
header{background:#003366;color:#fff;height:64px;padding:0 16px;display:flex;align-items:center;gap:12px;position:sticky;top:0;z-index:30}
header a{color:#fff;text-decoration:none;font-size:20px;padding:8px;border-radius:6px;line-height:1}
header a:hover{background:rgba(255,255,255,.12)}
header .title{font-size:16px;font-weight:600;flex:1}
header .count{background:#ff5252;color:#fff;padding:4px 10px;border-radius:999px;font-size:12px;font-weight:700}
.filters{display:flex;gap:8px;padding:12px 16px;max-width:720px;margin:0 auto;overflow-x:auto;scrollbar-width:none}
.filters::-webkit-scrollbar{display:none}
.chip{flex:0 0 auto;padding:8px 16px;border:1px solid #d0d7de;border-radius:999px;background:#fff;cursor:pointer;font-size:14px;font-family:inherit;color:#192431;white-space:nowrap}
.chip.active{background:#003366;color:#fff;border-color:#003366}
main{max-width:720px;margin:0 auto;padding:0 16px}
.notif{background:#fff;border-radius:10px;padding:14px;margin-bottom:10px;box-shadow:0 1px 3px rgba(0,0,0,.08);display:flex;gap:12px;text-decoration:none;color:inherit;transition:background .15s;cursor:pointer;position:relative}
.notif:hover{background:#fafbfc}
.notif.unread{background:#e8eef4;border-left:4px solid #003366}
.notif .icon{width:38px;height:38px;border-radius:50%;background:#003366;color:#fff;display:flex;align-items:center;justify-content:center;font-size:18px;flex:0 0 auto}
.notif.accepted .icon{background:#1b5e20}
.notif.rejected .icon{background:#616161}
.notif.offer .icon{background:#003366}
.notif.need .icon{background:#0d47a1}
.notif .body{flex:1;min-width:0}
.notif .title{font-weight:600;font-size:14px;color:#192431;margin-bottom:2px;display:-webkit-box;-webkit-line-clamp:1;-webkit-box-orient:vertical;overflow:hidden}
.notif .msg{font-size:13px;color:#586675;line-height:1.4;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden}
.notif .time{font-size:11px;color:#586675;margin-top:4px}
.notif .dot{width:8px;height:8px;background:#ff5252;border-radius:50%;flex:0 0 auto;margin-top:6px}
.state{padding:60px 20px;text-align:center;color:#586675}
.state h3{color:#003366;font-size:18px;margin:0 0 8px}
.state p{margin:0 0 16px}
.state .icon-big{font-size:48px;margin-bottom:12px;opacity:.4}
.btn{padding:12px 20px;border-radius:8px;font-size:15px;font-weight:600;cursor:pointer;border:none;font-family:inherit;text-decoration:none;text-align:center;display:inline-block;background:#003366;color:#fff;transition:background .15s}
.btn:hover:not(:disabled){background:#002a52}
.btn.sec{background:#eef1f4;color:#192431}
.btn.sec:hover{background:#e0e4e8}
.skeleton-card{background:#fff;border-radius:10px;padding:14px;margin-bottom:10px;box-shadow:0 1px 3px rgba(0,0,0,.08);display:flex;gap:12px}
.sk-avatar{width:38px;height:38px;border-radius:50%;background:#eef1f4;flex:0 0 auto}
.sk-lines{flex:1}
.sk-line{height:12px;background:#eef1f4;border-radius:4px;margin-bottom:8px}
.sk-line.w40{width:40%}.sk-line.w90{width:90%}.sk-line.w70{width:70%}
.load-more-wrap{text-align:center;padding:24px 0}
.bn{position:fixed;bottom:0;left:0;right:0;height:64px;background:#fff;border-top:1px solid #eef1f4;display:flex;z-index:20;box-shadow:0 -1px 3px rgba(0,0,0,.04)}
.bn a{flex:1;display:flex;flex-direction:column;align-items:center;justify-content:center;gap:2px;text-decoration:none;color:#586675;font-size:11px;position:relative}
.bn a:hover{color:#003366}
.bn a.active{color:#003366;font-weight:600}
.bn .ic{font-size:20px;line-height:1}
.bn .badge-dot{position:absolute;top:8px;right:calc(50% - 14px);width:8px;height:8px;background:#ff5252;border-radius:50%;display:none}
.bn .badge-dot.on{display:block}
@media(min-width:768px){.bn{display:none}body{padding-bottom:24px}}
.offline-banner{background:#fff8e1;border:1px solid #ffe082;color:#8a6d00;padding:10px 14px;border-radius:8px;font-size:13px;margin-bottom:12px;display:none;max-width:720px;margin:12px auto 0}
.offline-banner.on{display:block}
.toast{position:fixed;bottom:100px;left:50%;transform:translateX(-50%);background:#192431;color:#fff;padding:12px 20px;border-radius:8px;font-size:14px;z-index:40;opacity:0;pointer-events:none;transition:opacity .2s;max-width:90vw}
.toast.on{opacity:1}
</style>
</head>
<body>
<header>
<a href="/browse" title="{{ __('back') }}">&#8592;</a>
<span class="title">{{ __('navNotifications') }}</span>
<span class="count" id="unread-count" hidden>0</span>
</header>

<div class="offline-banner" id="offline-banner">{{ __('offlineBody') }}</div>

<div class="filters" id="filters">
<button type="button" class="chip active" data-unread="">{{ __('filterAll') }}</button>
<button type="button" class="chip" data-unread="1">{{ __('filterUnread') }}</button>
</div>

<main>
<div id="state-loading">
<div class="skeleton-card"><div class="sk-avatar"></div><div class="sk-lines"><div class="sk-line w40"></div><div class="sk-line w90"></div></div></div>
<div class="skeleton-card"><div class="sk-avatar"></div><div class="sk-lines"><div class="sk-line w40"></div><div class="sk-line w90"></div></div></div>
<div class="skeleton-card"><div class="sk-avatar"></div><div class="sk-lines"><div class="sk-line w40"></div><div class="sk-line w90"></div></div></div>
</div>

<div id="state-list" hidden></div>

<div id="state-empty" class="state" hidden>
<div class="icon-big">&#128276;</div>
<h3>{{ __('noNotificationsTitle') }}</h3>
<p>{{ __('noNotificationsBody') }}</p>
</div>

<div id="state-error" class="state" hidden>
<h3>{{ __('loadErrorTitle') }}</h3>
<p>{{ __('loadErrorBody') }}</p>
<button type="button" class="btn" onclick="loadNotifications()">{{ __('retry') }}</button>
</div>

<div class="load-more-wrap" id="load-more-wrap" hidden>
<button type="button" class="btn sec" id="load-more-btn">{{ __('loadMore') }}</button>
</div>
</main>

<nav class="bn" role="navigation">
<a href="/browse"><span class="ic">&#128269;</span><span>{{ __('navBrowse') }}</span></a>
<a href="/my/needs"><span class="ic">&#128203;</span><span>{{ __('navMyNeeds') }}</span></a>
<a href="/my/offers"><span class="ic">&#127991;</span><span>{{ __('navMyOffers') }}</span></a>
<a href="/notifications" class="active" aria-current="page">
<span class="ic">&#128276;<span class="badge-dot" id="nav-badge"></span></span>
<span>{{ __('navNotifications') }}</span>
</a>
<a href="/profile"><span class="ic">&#128100;</span><span>{{ __('navProfile') }}</span></a>
</nav>

<div class="toast" id="toast"></div>

<script>
(function(){
'use strict';
var csrf=(document.querySelector('meta[name="csrf-token"]')||{}).content||'';
var LS_TOKEN='felagi_token';
var state={unreadOnly:false,page:1,perPage:20,hasMore:false,items:[],loading:false};

function $(id){return document.getElementById(id);}
function getToken(){return localStorage.getItem(LS_TOKEN);}
function esc(s){return String(s==null?'':s).replace(/[&<>"']/g,function(c){return {'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c];});}

function toast(msg,ms){ms=ms||2200;var el=$('toast');el.textContent=msg;el.className='toast on';setTimeout(function(){el.className='toast';},ms);}

function showOnly(name){
  ['state-loading','state-list','state-empty','state-error'].forEach(function(id){
    var el=$(id); if(el)el.hidden=(id!=='state-'+name);
  });
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
  if(type.indexOf('accept')!==-1)return {cls:'accepted',ic:'&#10003;'};
  if(type.indexOf('reject')!==-1)return {cls:'rejected',ic:'&#10007;'};
  if(entity==='offer'||type.indexOf('offer')!==-1)return {cls:'offer',ic:'&#127991;'};
  if(entity==='need'||type.indexOf('need')!==-1)return {cls:'need',ic:'&#128203;'};
  return {cls:'',ic:'&#128276;'};
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
    return '<'+tag+' class="'+cls+'"'+hrefAttr+' data-id="'+esc(n.id)+'" data-unread="'+(unread?'1':'0')+'">'+
      '<div class="icon">'+ico.ic+'</div>'+
      '<div class="body">'+
        '<div class="title">'+esc(n.title||'')+'</div>'+
        '<div class="msg">'+esc((n.body||'').slice(0,140))+'</div>'+
        '<div class="time">'+esc(fmtTime(n.created_at))+'</div>'+
      '</div>'+
      dot+
    '</'+tag+'>';
  }).join('');

  // Wire click → mark read (then navigate if link)
  Array.prototype.forEach.call(wrap.querySelectorAll('.notif'),function(el){
    el.addEventListener('click',function(e){
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
  var navBadge=$('nav-badge');
  if(count>0){
    el.textContent=count;
    el.hidden=false;
    if(navBadge)navBadge.className='badge-dot on';
  }else{
    el.hidden=true;
    if(navBadge)navBadge.className='badge-dot';
  }
}

function decrementUnread(){
  var el=$('unread-count');
  var cur=parseInt(el.textContent||'0',10);
  if(cur>0)updateUnread(cur-1);
}

function markRead(id,cb){
  var token=getToken();
  if(!token)return;
  fetch('/api/v1/notifications/'+encodeURIComponent(id)+'/read',{
    method:'POST',
    headers:{
      'Accept':'application/json',
      'Authorization':'Bearer '+token,
      'X-CSRF-TOKEN':csrf
    }
  })
  .then(function(r){return r.ok?r.json():null;})
  .then(function(){if(cb)cb();})
  .catch(function(){if(cb)cb();});
}

function buildQuery(){
  var p=new URLSearchParams();
  if(state.unreadOnly)p.set('unread','1');
  p.set('page',state.page);
  p.set('per_page',state.perPage);
  return p.toString();
}

function loadNotifications(){
  if(state.loading)return;
  state.loading=true;

  var token=getToken();
  if(!token){window.location.href='/';return;}

  if(state.page===1&&state.items.length===0)showOnly('loading');

  fetch('/api/v1/notifications?'+buildQuery(),{
    headers:{'Accept':'application/json','Authorization':'Bearer '+token}
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
      showOnly('list');
    }
    $('load-more-wrap').hidden=!state.hasMore;

    if(meta.unread_count!=null)updateUnread(parseInt(meta.unread_count,10)||0);
  })
  .catch(function(e){
    console.warn('[S018] loadNotifications',e);
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
    state.unreadOnly=btn.dataset.unread==='1';
    state.page=1;
    state.items=[];
    $('load-more-wrap').hidden=true;
    loadNotifications();
  });
});

$('load-more-btn').addEventListener('click',function(){
  if(!state.hasMore||state.loading)return;
  state.page+=1;
  loadNotifications();
});

window.addEventListener('offline',function(){$('offline-banner').className='offline-banner on';});
window.addEventListener('online',function(){$('offline-banner').className='offline-banner';loadNotifications();});

if(navigator.onLine===false){$('offline-banner').className='offline-banner on';}

window.loadNotifications=loadNotifications;
loadNotifications();
})();
</script>
</body>
</html>
