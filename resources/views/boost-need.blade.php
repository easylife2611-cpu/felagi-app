<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>{{ __('boostTitle') }} — {{ __('brand') }}</title>
<style>
*{box-sizing:border-box;margin:0;padding:0}
body{font-family:system-ui,-apple-system,sans-serif;background:#F4F6F8;color:#192431;line-height:1.5;min-height:100vh;padding-bottom:100px}
header{background:#003366;color:#fff;height:64px;padding:0 16px;display:flex;align-items:center;gap:12px;position:sticky;top:0;z-index:30}
header a{color:#fff;text-decoration:none;font-size:20px;padding:8px;border-radius:6px;line-height:1}
header .title{font-size:16px;font-weight:600;flex:1}
main{max-width:640px;margin:0 auto;padding:16px}
.intro{background:#e3f2fd;border:1px solid #bbdefb;color:#0d47a1;padding:14px 16px;border-radius:10px;font-size:13px;line-height:1.5;margin-bottom:16px}
.intro strong{display:block;margin-bottom:4px}
.notice-warn{background:#fff8e1;border:1px solid #ffe082;color:#8a6d00;padding:14px 16px;border-radius:10px;font-size:13px;line-height:1.5;margin-bottom:16px;display:none}
.notice-warn.on{display:block}
.state{padding:60px 20px;text-align:center;color:#586675}
.state h3{color:#003366;font-size:18px;margin:0 0 8px}
.state p{margin:0 0 16px}
.package{background:#fff;border-radius:12px;padding:18px;margin-bottom:12px;box-shadow:0 1px 3px rgba(0,0,0,.08);cursor:pointer;transition:border-color .15s,box-shadow .15s;border:2px solid transparent;position:relative}
.package:hover{box-shadow:0 4px 12px rgba(0,0,0,.1)}
.package.selected{border-color:#003366;background:#f6f8fa}
.package .top{display:flex;justify-content:space-between;align-items:flex-start;gap:10px;margin-bottom:10px}
.package .name{font-size:17px;font-weight:700;color:#192431}
.package .price{font-size:22px;font-weight:700;color:#1b5e20;text-align:right}
.package .price .cur{font-size:13px;font-weight:600;margin-right:2px}
.package .meta{font-size:13px;color:#586675;line-height:1.5}
.package .meta .row{display:flex;gap:6px;align-items:center;margin-top:3px}
.package .check{position:absolute;top:14px;right:14px;width:24px;height:24px;border:2px solid #d0d7de;border-radius:50%;background:#fff;display:flex;align-items:center;justify-content:center;color:#fff;font-size:14px;transition:all .15s}
.package.selected .check{background:#003366;border-color:#003366}
.actions{position:fixed;bottom:0;left:0;right:0;background:#fff;border-top:1px solid #eef1f4;padding:12px 16px;display:flex;gap:10px;max-width:640px;margin:0 auto;z-index:20;box-shadow:0 -1px 3px rgba(0,0,0,.04)}
.btn{flex:1;padding:14px 20px;border-radius:10px;font-size:15px;font-weight:600;cursor:pointer;border:none;font-family:inherit;text-decoration:none;text-align:center;display:block;transition:background .15s}
.btn.primary{background:#003366;color:#fff}
.btn.primary:hover:not(:disabled){background:#002a52}
.btn.primary:disabled{opacity:.5;cursor:not-allowed}
.btn.sec{background:#eef1f4;color:#192431}
.btn.sec:hover{background:#e0e4e8}
.spinner{display:inline-block;width:20px;height:20px;border:3px solid #e0e0e0;border-top-color:#003366;border-radius:50%;animation:spin .8s linear infinite}
@keyframes spin{to{transform:rotate(360deg)}}
.offline-banner{background:#fff8e1;border:1px solid #ffe082;color:#8a6d00;padding:10px 14px;border-radius:8px;font-size:13px;margin-bottom:16px;display:none}
.offline-banner.on{display:block}
.toast{position:fixed;bottom:120px;left:50%;transform:translateX(-50%);background:#192431;color:#fff;padding:12px 20px;border-radius:8px;font-size:14px;z-index:40;opacity:0;pointer-events:none;transition:opacity .2s;max-width:90vw;text-align:center}
.toast.on{opacity:1}
</style>
</head>
<body>
<header>
<a href="#" id="back-btn" title="{{ __('back') }}">&#8592;</a>
<span class="title">{{ __('boostTitle') }}</span>
</header>

<main>
<div class="intro">
<strong>{{ __('boostIntroTitle') }}</strong>
{{ __('boostIntroBody') }}
</div>

<div class="notice-warn" id="api-warn">
<strong>{{ __('featurePendingTitle') }}</strong><br>
{{ __('featurePendingBody') }}
</div>

<div class="offline-banner" id="offline-banner">{{ __('offlineBody') }}</div>

<div id="state-loading" class="state" style="padding:80px 20px">
<div class="spinner"></div>
<p style="margin-top:12px">{{ __('loading') }}...</p>
</div>

<div id="state-error" class="state" hidden>
<h3>{{ __('loadErrorTitle') }}</h3>
<p>{{ __('loadErrorBody') }}</p>
<button type="button" class="btn sec" onclick="loadPackages()" style="display:inline-block;max-width:180px;margin-top:12px">{{ __('retry') }}</button>
</div>

<div id="content" hidden>
<div id="packages-list"></div>
</div>
</main>

<div class="actions" id="actions" hidden>
<a href="#" id="cancel-btn" class="btn sec">{{ __('cancel') }}</a>
<button type="button" class="btn primary" id="boost-btn" disabled>{{ __('boostNow') }}</button>
</div>

<div class="toast" id="toast"></div>

<script>
(function(){
'use strict';
var csrf=(document.querySelector('meta[name="csrf-token"]')||{}).content||'';
var LS_TOKEN='felagi_token';
var needId='';
var packages=[];
var selected={};

function $(id){return document.getElementById(id);}
function getToken(){return localStorage.getItem(LS_TOKEN);}
function esc(s){return String(s==null?'':s).replace(/[&<>"']/g,function(c){return {'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c];});}

function getNeedId(){
  var parts=window.location.pathname.split('/').filter(Boolean);
  return parts.length>=2?parts[1]:'';
}

function toast(msg,ms){ms=ms||2600;var el=$('toast');el.textContent=msg;el.className='toast on';setTimeout(function(){el.className='toast';},ms);}

function showOnly(name){
  ['state-loading','state-error','content'].forEach(function(id){
    var el=$(id); if(el)el.hidden=(id!=='state-'+name);
  });
  var a=$('actions');
  if(a)a.hidden=(name!=='content');
}

function fmtPrice(p){
  var cur=p.currency||'ETB';
  var price=p.price!=null?Number(p.price).toFixed(2):'—';
  return {cur:cur,price:price};
}

function renderPackages(){
  var wrap=$('packages-list');
  if(!packages.length){
    wrap.innerHTML='<div class="state"><h3>{{ __('noPackagesTitle') }}</h3><p>{{ __('noPackagesBody') }}</p></div>';
    return;
  }
  wrap.innerHTML=packages.map(function(p){
    var isSelected=!!selected[p.id];
    var pr=fmtPrice(p);
    var duration=p.duration_days?p.duration_days+' {{ __('days') }}':'';
    var features='';
    if(p.description)features='<div class="row">'+esc(p.description)+'</div>';
    return '<div class="package'+(isSelected?' selected':'')+'" data-id="'+esc(p.id)+'">'+
      '<div class="check">'+(isSelected?'&#10003;':'')+'</div>'+
      '<div class="top">'+
        '<div class="name">'+esc(p.name||'Package')+'</div>'+
        '<div class="price"><span class="cur">'+esc(pr.cur)+'</span>'+esc(pr.price)+'</div>'+
      '</div>'+
      '<div class="meta">'+
        (duration?'<div class="row">&#9201; '+esc(duration)+'</div>':'')+
        features+
      '</div>'+
    '</div>';
  }).join('');

  Array.prototype.forEach.call(wrap.querySelectorAll('.package'),function(el){
    el.addEventListener('click',function(){
      var id=el.dataset.id;
      selected={};
      selected[id]=true;
      renderPackages();
      updateButton();
    });
  });
}

function updateButton(){
  var has=Object.keys(selected).length>0;
  $('boost-btn').disabled=!has;
}

function loadPackages(){
  needId=getNeedId();
  if(!needId){showOnly('error');return;}
  var token=getToken();
  if(!token){window.location.href='/';return;}
  $('back-btn').href='/needs/'+encodeURIComponent(needId);
  $('cancel-btn').href='/needs/'+encodeURIComponent(needId);

  showOnly('loading');

  fetch('/api/v1/boost-packages',{
    headers:{'Accept':'application/json','Authorization':'Bearer '+token}
  })
  .then(function(r){
    if(r.status===404)throw new Error('notfound');
    if(r.status===401)throw new Error('auth');
    if(!r.ok)throw new Error('HTTP '+r.status);
    return r.json();
  })
  .then(function(j){
    var data=(j&&j.data)?j.data:(Array.isArray(j)?j:[]);
    packages=Array.isArray(data)?data:[];
    renderPackages();
    updateButton();
    showOnly('content');
  })
  .catch(function(e){
    var m=String(e.message||e);
    if(m==='auth'){localStorage.removeItem(LS_TOKEN);window.location.href='/';return;}
    // Backend pending — show graceful message
    $('api-warn').className='notice-warn on';
    showOnly('error');
  });
}

function submitBoost(){
  var ids=Object.keys(selected);
  if(!ids.length){toast('{{ __('selectPackage') }}');return;}
  toast('{{ __('featurePendingBody') }}', 3500);
}

$('boost-btn').addEventListener('click',submitBoost);

window.addEventListener('offline',function(){$('offline-banner').className='offline-banner on';});
window.addEventListener('online',function(){$('offline-banner').className='offline-banner';loadPackages();});

if(navigator.onLine===false){$('offline-banner').className='offline-banner on';}

window.loadPackages=loadPackages;
loadPackages();
})();
</script>
</body>
</html>
