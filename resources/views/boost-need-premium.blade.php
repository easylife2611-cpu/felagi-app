<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>{{ __('screenS019') }} — {{ __('brand') }}</title>
<meta name="theme-color" content="#003366">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Ethiopic:wght@400;500;600;700;800;900&family=Inter:wght@400;600;700;800;900&display=swap" rel="stylesheet">
<style>
:root{--navy-950:#001a33;--navy-900:#002b52;--navy-800:#003366;--navy-700:#0b4d83;--navy-600:#1a6bb0;--orange-700:#b35c00;--orange-600:#e8851d;--orange-500:#FF9933;--orange-400:#ffb366;--orange-100:#fff2e0;--ink-900:#0d1a2b;--ink-500:#3c4d61;--ink-300:#5f7185;--ink-200:#8fa0b3;--line-100:#eef2f6;--line-200:#e3e9ef;--line-300:#d5dde6;--surface:#fff;--canvas:#f4f7fa;--canvas-2:#eef3f8;--success-600:#0e6b34;--success-500:#147a3d;--success-100:#e8f5ee;--warn-600:#a86500;--warn-100:#fff7e6;--danger-600:#a32e21;--danger-500:#c0392b;--danger-100:#fdecea;--info-600:#0056d6;--info-100:#e7f1ff;--r-xs:8px;--r-sm:12px;--r-md:16px;--r-lg:22px;--r-pill:999px;--sh-xs:0 1px 2px rgba(0,26,51,.04),0 1px 3px rgba(0,26,51,.06);--sh-sm:0 2px 6px rgba(0,26,51,.05),0 4px 12px rgba(0,26,51,.06);--sh-md:0 4px 12px rgba(0,26,51,.06),0 12px 28px rgba(0,26,51,.08);--sh-navy:0 12px 32px rgba(0,51,102,.28);--sh-orange:0 8px 20px rgba(255,153,51,.32);--f-am:'Noto Sans Ethiopic','Inter',system-ui,sans-serif;--f-en:'Inter','Noto Sans Ethiopic',system-ui,sans-serif;--ease:cubic-bezier(.16,.84,.44,1);--ease-out:cubic-bezier(.22,1,.36,1)}
*{box-sizing:border-box;margin:0;padding:0}
html,body{background:var(--canvas);color:var(--ink-900);font-family:var(--f-am);line-height:1.55;-webkit-font-smoothing:antialiased}
body{padding:0 0 110px;min-height:100vh}
a{color:inherit;text-decoration:none}
button{font-family:inherit;cursor:pointer;border:0;background:transparent;color:inherit}
:focus-visible{outline:3px solid var(--orange-500);outline-offset:3px;border-radius:8px}
.sr-only{position:absolute;width:1px;height:1px;padding:0;margin:-1px;overflow:hidden;clip:rect(0,0,0,0);white-space:nowrap;border:0}
.sc{max-width:640px;margin:0 auto;min-height:100vh;display:flex;flex-direction:column}
.sh{flex:0 0 auto;color:#fff;padding:18px 22px 22px;position:relative;overflow:hidden;z-index:1}
.mesh{background:radial-gradient(ellipse at top right,rgba(255,153,51,.18),transparent 55%),radial-gradient(ellipse at bottom left,rgba(26,107,176,.35),transparent 60%),linear-gradient(140deg,var(--navy-950),var(--navy-800))}
.sh .tr{display:flex;align-items:center;gap:12px;position:relative;z-index:1}
.ib{width:40px;height:40px;border-radius:50%;display:inline-flex;align-items:center;justify-content:center;color:#fff;background:rgba(255,255,255,.10);border:1px solid rgba(255,255,255,.08);transition:all .2s var(--ease);flex:0 0 auto}
.ib:hover{background:rgba(255,255,255,.18)}
.ib svg{width:20px;height:20px;stroke:currentColor;fill:none;stroke-width:2;stroke-linecap:round;stroke-linejoin:round}
.sh .t{margin-top:12px;font-size:22px;font-weight:900;letter-spacing:-.03em;line-height:1.2;position:relative;z-index:1}
.sb{flex:1 1 auto;padding:20px 18px 40px;position:relative;z-index:1}
.intro{background:linear-gradient(135deg,var(--info-100),#f0f7ff);border:1px solid rgba(13,110,253,.18);border-radius:var(--r-md);padding:14px 16px;font-size:13px;line-height:1.6;color:var(--navy-800);margin-bottom:16px;font-weight:600}
.intro strong{display:block;margin-bottom:5px;font-weight:900;letter-spacing:-.01em}
.notice-warn{display:none;background:linear-gradient(135deg,var(--warn-100),#fffbf0);border:1px solid rgba(168,101,0,.18);color:var(--warn-600);padding:14px 16px;border-radius:var(--r-md);font-size:13px;line-height:1.6;margin-bottom:16px;font-weight:600}
.notice-warn.on{display:block}
.notice-warn strong{display:block;margin-bottom:4px;font-weight:900;letter-spacing:-.01em}
.pkg{background:var(--surface);border:2px solid var(--line-100);border-radius:var(--r-lg);padding:20px;margin-bottom:12px;box-shadow:var(--sh-xs);cursor:pointer;transition:all .25s var(--ease-out);position:relative;overflow:hidden}
.pkg:hover{transform:translateY(-2px);box-shadow:var(--sh-sm)}
.pkg.selected{border-color:var(--navy-800);background:linear-gradient(135deg,#f6faff,var(--surface));box-shadow:var(--sh-sm)}
.pkg.selected::before{content:'';position:absolute;top:0;left:0;right:0;height:3px;background:linear-gradient(90deg,var(--orange-500),var(--navy-700))}
.pkg .top{display:flex;justify-content:space-between;align-items:flex-start;gap:12px;margin-bottom:12px}
.pkg .name{font-size:17px;font-weight:900;color:var(--ink-900);letter-spacing:-.02em;line-height:1.3}
.pkg .price{font-size:24px;font-weight:900;color:var(--success-600);text-align:right;font-family:var(--f-en);letter-spacing:-.03em;display:flex;align-items:baseline;gap:4px;line-height:1}
.pkg .price .cur{font-size:12px;font-weight:800;color:var(--ink-300);letter-spacing:.06em}
.pkg .meta{font-size:13px;color:var(--ink-500);line-height:1.6}
.pkg .meta .row{display:flex;gap:6px;align-items:center;margin-top:5px;font-weight:600}
.pkg .check{position:absolute;top:18px;right:18px;width:26px;height:26px;border:2px solid var(--line-300);border-radius:50%;background:var(--surface);display:flex;align-items:center;justify-content:center;color:#fff;font-size:14px;font-weight:900;transition:all .2s var(--ease)}
.pkg.selected .check{background:linear-gradient(135deg,var(--navy-800),var(--navy-700));border-color:var(--navy-800);box-shadow:0 2px 8px rgba(0,51,102,.24)}
.actions{position:fixed;bottom:0;left:0;right:0;background:rgba(255,255,255,.98);backdrop-filter:blur(12px);border-top:1px solid var(--line-200);padding:14px 18px;display:flex;gap:10px;max-width:640px;margin:0 auto;z-index:20;box-shadow:0 -4px 16px rgba(0,26,51,.06)}
.bt{display:inline-flex;align-items:center;justify-content:center;gap:9px;height:52px;padding:0 24px;border-radius:var(--r-sm);font-weight:900;font-size:15px;line-height:1;letter-spacing:-.015em;transition:all .2s var(--ease);font-family:var(--f-am);text-align:center;flex:1}
.bt:active{transform:scale(.975)}
.bt-primary{background:linear-gradient(135deg,var(--orange-500),var(--orange-600));color:#fff;box-shadow:var(--sh-orange)}
.bt-primary:hover:not(:disabled){transform:translateY(-1px);box-shadow:0 12px 28px rgba(255,153,51,.42)}
.bt-primary:disabled{opacity:.5;cursor:not-allowed}
.bt-ghost{background:var(--surface);color:var(--navy-800);border:1.5px solid var(--line-300);flex:0 0 auto}
.bt-ghost:hover{background:var(--canvas-2);border-color:var(--navy-700)}
.state{padding:60px 24px;text-align:center;color:var(--ink-300)}
.state .ic{font-size:48px;margin-bottom:14px;opacity:.5}
.state h3{font-size:18px;font-weight:900;color:var(--navy-800);margin-bottom:8px}
.state p{font-size:13.5px;line-height:1.6;color:var(--ink-500);max-width:320px;margin:0 auto 16px}
.spinner{display:inline-block;width:26px;height:26px;border:3px solid var(--line-200);border-top-color:var(--navy-800);border-radius:50%;animation:spin .8s linear infinite}
.spinner-sm{display:inline-block;width:16px;height:16px;border:2px solid rgba(255,255,255,.4);border-top-color:#fff;border-radius:50%;animation:spin .8s linear infinite}
@keyframes spin{to{transform:rotate(360deg)}}
.offline-banner{display:none;background:var(--warn-100);color:var(--warn-600);padding:10px 16px;border-radius:var(--r-sm);font-size:12.5px;font-weight:800;text-align:center;margin-bottom:14px}
.offline-banner.on{display:block}
.toast{position:fixed;bottom:120px;left:50%;transform:translateX(-50%) translateY(20px);background:var(--navy-900);color:#fff;padding:12px 20px;border-radius:var(--r-pill);font-size:13px;font-weight:700;opacity:0;pointer-events:none;transition:all .3s var(--ease-out);z-index:50;box-shadow:var(--sh-md);max-width:90vw;text-align:center}
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
    <h1 class="t" id="page-title" tabindex="-1" role="heading" aria-level="1">{{ __('boostTitle') }}</h1>
  </div>
</div>
<div class="sb">
  <div class="intro">
    <strong>{{ __('boostIntroTitle') }}</strong>
    {{ __('boostIntroBody') }}
  </div>

  <div class="notice-warn" id="api-warn">
    <strong>{{ __('featurePendingTitle') }}</strong>
    {{ __('featurePendingBody') }}
  </div>

  <div class="offline-banner" id="offline-banner" role="status" aria-live="polite">{{ __('offlineBody') }}</div>

  <div id="state-loading" class="state" style="padding:80px 24px">
    <div class="spinner"></div>
    <p style="margin-top:14px">{{ __('loading') }}...</p>
  </div>

  <div id="state-error" class="state" hidden>
    <div class="ic">⚠️</div>
    <h3>{{ __('loadErrorTitle') }}</h3>
    <p>{{ __('loadErrorBody') }}</p>
    <button type="button" class="bt bt-ghost" onclick="loadPackages()" style="display:inline-flex;max-width:200px;margin:0 auto">{{ __('retry') }}</button>
  </div>

  <div id="content" hidden>
    <div id="packages-list"></div>
  </div>
</div>

<div class="actions" id="actions" hidden>
  <a href="#" id="cancel-btn" class="bt bt-ghost">{{ __('cancel') }}</a>
  <button type="button" class="bt bt-primary" id="boost-btn" disabled>{{ __('boostNow') }}</button>
</div>
</div>
<div class="toast" id="toast" role="status" aria-live="polite"></div>
<script>
(function(){
'use strict';
var csrf=(document.querySelector('meta[name="csrf-token"]')||{}).content||'';
var LS_TOKEN='felagi_token';
var needId='';
var packages=[];
var selected={};
function $(id){return document.getElementById(id);}
function getToken(){return 'session';}
function esc(s){return String(s==null?'':s).replace(/[&<>"']/g,function(c){return {'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c];});}
function getNeedId(){var parts=window.location.pathname.split('/').filter(Boolean);return parts.length>=2?parts[1]:'';}
function toast(msg,ms){ms=ms||2600;var el=$('toast');el.textContent=msg;el.className='toast on';setTimeout(function(){el.className='toast';},ms);}
function showOnly(name){
  ['state-loading','state-error','content'].forEach(function(id){var el=$(id);if(el)el.hidden=(id!==('state-'+name));});
  var a=$('actions');if(a)a.hidden=(name!=='content');
}
function fmtPrice(p){
  var cur=p.currency||'ETB';
  var price=p.price!=null?Number(p.price).toFixed(2):'—';
  return {cur:cur,price:price};
}
function renderPackages(){
  var wrap=$('packages-list');
  if(!packages.length){
    wrap.innerHTML='<div class="state"><div class="ic">📦</div><h3>{{ __('noPackagesTitle') }}</h3><p>{{ __('noPackagesBody') }}</p></div>';
    return;
  }
  wrap.innerHTML=packages.map(function(p){
    var isSelected=!!selected[p.id];
    var pr=fmtPrice(p);
    var duration=p.duration_days?p.duration_days+' {{ __('days') }}':'';
    var features='';
    if(p.description)features='<div class="row">'+esc(p.description)+'</div>';
    return '<div class="pkg'+(isSelected?' selected':'')+'" data-id="'+esc(p.id)+'" role="radio" aria-checked="'+(isSelected?'true':'false')+'" tabindex="0">'+
      '<div class="check">'+(isSelected?'✓':'')+'</div>'+
      '<div class="top">'+
        '<div class="name">'+esc(p.name||'Package')+'</div>'+
        '<div class="price"><span class="cur">'+esc(pr.cur)+'</span>'+esc(pr.price)+'</div>'+
      '</div>'+
      '<div class="meta">'+
        (duration?'<div class="row">🕐 '+esc(duration)+'</div>':'')+
        features+
      '</div>'+
    '</div>';
  }).join('');
  Array.prototype.forEach.call(wrap.querySelectorAll('.pkg'),function(el){
    el.addEventListener('click',function(){
      var id=el.dataset.id;
      selected={};selected[id]=true;
      renderPackages();updateButton();
    });
    el.addEventListener('keydown',function(e){
      if(e.key===' '||e.key==='Enter'){e.preventDefault();el.click();}
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
  $('back-btn').href='/needs/'+encodeURIComponent(needId);
  $('cancel-btn').href='/needs/'+encodeURIComponent(needId);
  showOnly('loading');
  fetch('/api/v1/boost-packages',{credentials:'same-origin',headers:{'Accept':'application/json'}})
  .then(function(r){
    if(r.status===404)throw new Error('notfound');
    if(r.status===401)throw new Error('auth');
    if(!r.ok)throw new Error('HTTP '+r.status);
    return r.json();
  })
  .then(function(j){
    var data=(j&&j.data)?j.data:(Array.isArray(j)?j:[]);
    packages=Array.isArray(data)?data:[];
    renderPackages();updateButton();showOnly('content');
  })
  .catch(function(e){
    var m=String(e.message||e);
    if(m==='auth'){localStorage.removeItem(LS_TOKEN);window.location.href='/';return;}
    $('api-warn').className='notice-warn on';
    showOnly('error');
  });
}
function submitBoost(){
  var ids=Object.keys(selected);
  if(!ids.length){toast('{{ __('selectPackage') }}');return;}
  if(!needId){toast('{{ __('boostFailed') }}');return;}
  var btn=$('boost-btn');
  if(btn.disabled)return;
  var origText=btn.textContent;
  btn.disabled=true;
  btn.innerHTML='<span class="spinner-sm"></span>{{ __('processing') }}';
  fetch('/api/v1/needs/'+encodeURIComponent(needId)+'/boosts',{
    method:'POST',
    credentials:'same-origin',
    headers:{'Accept':'application/json','Content-Type':'application/json','X-CSRF-TOKEN':csrf},
    body:JSON.stringify({package_id:ids[0]})
  })
  .then(function(r){return r.json().then(function(j){return {ok:r.ok,status:r.status,json:j};});})
  .then(function(res){
    if(!res.ok){
      var err=(res.json&&res.json.error)||{};
      var code=err.code||'';
      var msg=err.message||'';
      if(res.status===409&&code==='BOOST_ACTIVE'){toast('{{ __('boostActive') }}');}
      else if(res.status===503){toast('{{ __('featurePendingBody') }}');}
      else if(res.status===401){localStorage.removeItem(LS_TOKEN);window.location.href='/';return;}
      else{toast(msg||'{{ __('boostFailed') }}');}
      btn.disabled=false;btn.textContent=origText;
      return;
    }
    var data=(res.json&&res.json.data)||{};
    var pay=data.payment||{};
    var url=pay.checkout_url;
    toast('{{ __('boostPending') }}');
    if(url){setTimeout(function(){window.location.href=url;},600);}
    else{setTimeout(function(){window.location.href='/needs/'+encodeURIComponent(needId);},1000);}
  })
  .catch(function(){
    toast('{{ __('boostFailed') }}');
    btn.disabled=false;btn.textContent=origText;
  });
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
