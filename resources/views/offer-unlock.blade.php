<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>{{ __('unlockTitle') }} — {{ __('brand') }}</title>
<style>
*{box-sizing:border-box;margin:0;padding:0}
body{font-family:system-ui,-apple-system,sans-serif;background:#F4F6F8;color:#192431;line-height:1.5;min-height:100vh;padding-bottom:100px}
header{background:#003366;color:#fff;height:64px;padding:0 16px;display:flex;align-items:center;gap:12px;position:sticky;top:0;z-index:30}
header a{color:#fff;text-decoration:none;font-size:20px;padding:8px;border-radius:6px;line-height:1}
header .title{font-size:16px;font-weight:600;flex:1}
main{max-width:560px;margin:0 auto;padding:16px}
.hero{text-align:center;padding:32px 16px}
.hero .icon{width:88px;height:88px;border-radius:50%;background:#e3f2fd;color:#0d47a1;display:flex;align-items:center;justify-content:center;font-size:44px;margin:0 auto 20px}
.hero h1{font-size:22px;font-weight:700;color:#003366;margin-bottom:10px}
.hero p{color:#586675;font-size:14px;line-height:1.5;max-width:400px;margin:0 auto}
.card{background:#fff;border-radius:12px;padding:20px;margin-bottom:16px;box-shadow:0 1px 3px rgba(0,0,0,.08)}
.card h2{font-size:15px;font-weight:600;color:#003366;margin-bottom:12px}
.info-row{display:flex;justify-content:space-between;padding:10px 0;border-bottom:1px solid #eef1f4;font-size:14px}
.info-row:last-child{border-bottom:none}
.info-row .lbl{color:#586675}
.info-row .val{font-weight:600;text-align:right;max-width:60%;word-break:break-word}
.price-big{font-size:26px;font-weight:700;color:#1b5e20;text-align:center;margin:16px 0 6px}
.price-big .cur{font-size:16px;font-weight:600;margin-right:4px}
.price-sub{text-align:center;font-size:12px;color:#8a95a3;margin-bottom:8px}
.notice-warn{background:#fff8e1;border:1px solid #ffe082;color:#8a6d00;padding:14px 16px;border-radius:10px;font-size:13px;line-height:1.5;margin-bottom:16px;display:none}
.notice-warn.on{display:block}
.state{padding:60px 20px;text-align:center;color:#586675}
.state h3{color:#003366;font-size:18px;margin:0 0 8px}
.state p{margin:0 0 16px}
.actions{position:fixed;bottom:0;left:0;right:0;background:#fff;border-top:1px solid #eef1f4;padding:12px 16px;display:flex;gap:10px;max-width:560px;margin:0 auto;z-index:20;box-shadow:0 -1px 3px rgba(0,0,0,.04)}
.btn{flex:1;padding:14px 20px;border-radius:10px;font-size:15px;font-weight:600;cursor:pointer;border:none;font-family:inherit;text-decoration:none;text-align:center;display:block;transition:background .15s}
.btn.primary{background:#003366;color:#fff}
.btn.primary:hover:not(:disabled){background:#002a52}
.btn.primary:disabled{opacity:.5;cursor:not-allowed}
.btn.sec{background:#eef1f4;color:#192431}
.btn.sec:hover{background:#e0e4e8}
.btn.success{background:#1b5e20;color:#fff}
.btn.success:hover:not(:disabled){background:#154a19}
.spinner{display:inline-block;width:20px;height:20px;border:3px solid #e0e0e0;border-top-color:#003366;border-radius:50%;animation:spin .8s linear infinite}
@keyframes spin{to{transform:rotate(360deg)}}
.offline-banner{background:#fff8e1;border:1px solid #ffe082;color:#8a6d00;padding:10px 14px;border-radius:8px;font-size:13px;margin-bottom:16px;display:none}
.offline-banner.on{display:block}
.toast{position:fixed;bottom:120px;left:50%;transform:translateX(-50%);background:#192431;color:#fff;padding:12px 20px;border-radius:8px;font-size:14px;z-index:40;opacity:0;pointer-events:none;transition:opacity .2s;max-width:90vw;text-align:center}
.toast.on{opacity:1}
.unlock-badge{display:inline-block;padding:4px 12px;border-radius:999px;background:#e8f5e9;color:#1b5e20;font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.5px}
</style>
</head>
<body>
<header>
<a href="#" id="back-btn" title="{{ __('back') }}">&#8592;</a>
<span class="title">{{ __('unlockTitle') }}</span>
</header>

<main>
<div class="hero">
<div class="icon">&#128275;</div>
<h1>{{ __('unlockHeroTitle') }}</h1>
<p>{{ __('unlockHeroBody') }}</p>
</div>

<div class="notice-warn" id="api-warn">
<strong>{{ __('featurePendingTitle') }}</strong><br>
{{ __('featurePendingBody') }}
</div>

<div class="offline-banner" id="offline-banner">{{ __('offlineBody') }}</div>

<div id="state-loading" class="state" style="padding:60px 20px">
<div class="spinner"></div>
<p style="margin-top:12px">{{ __('loading') }}...</p>
</div>

<div id="state-error" class="state" hidden>
<h3>{{ __('loadErrorTitle') }}</h3>
<p>{{ __('loadErrorBody') }}</p>
<button type="button" class="btn sec" onclick="loadUnlock()" style="display:inline-block;max-width:180px;margin-top:12px">{{ __('retry') }}</button>
</div>

<div id="content" hidden>
<div class="card">
<h2>{{ __('unlockCost') }}</h2>
<div class="price-big"><span class="cur" id="unlock-currency">ETB</span><span id="unlock-price">—</span></div>
<div class="price-sub" id="unlock-cost-sub">{{ __('unlockCostSub') }}</div>
</div>

<div class="card">
<h2>{{ __('unlockDetails') }}</h2>
<div id="unlock-rows"></div>
</div>

<div class="card">
<h2>{{ __('unlockWhatYouGet') }}</h2>
<div class="info-row"><span class="lbl">{{ __('unlockBenefit1') }}</span><span class="val">&#10003;</span></div>
<div class="info-row"><span class="lbl">{{ __('unlockBenefit2') }}</span><span class="val">&#10003;</span></div>
<div class="info-row"><span class="lbl">{{ __('unlockBenefit3') }}</span><span class="val">&#10003;</span></div>
</div>
</div>
</main>

<div class="actions" id="actions" hidden>
<a href="#" id="cancel-btn" class="btn sec">{{ __('cancel') }}</a>
<button type="button" class="btn success" id="unlock-btn">{{ __('unlockNow') }}</button>
</div>

<div class="toast" id="toast"></div>

<script>
(function(){
'use strict';
var csrf=(document.querySelector('meta[name="csrf-token"]')||{}).content||'';
var LS_TOKEN='felagi_token';
var needId='';
var unlockInfo=null;

function $(id){return document.getElementById(id);}
function getToken(){return localStorage.getItem(LS_TOKEN);}
function esc(s){return String(s==null?'':s).replace(/[&<>"']/g,function(c){return {'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c];});}

function getNeedId(){
  var parts=window.location.pathname.split('/').filter(Boolean);
  return parts.length>=2?parts[1]:'';
}

function toast(msg,ms){ms=ms||2800;var el=$('toast');el.textContent=msg;el.className='toast on';setTimeout(function(){el.className='toast';},ms);}

function showOnly(name){
  ['state-loading','state-error','content'].forEach(function(id){
    var el=$(id); if(el)el.hidden=(id!=='state-'+name);
  });
  var a=$('actions');
  if(a)a.hidden=(name!=='content');
}

function fmtDate(iso){
  if(!iso)return '—';
  var d=new Date(iso);
  if(isNaN(d))return '—';
  return d.toLocaleDateString();
}

function renderUnlock(){
  if(!unlockInfo){
    $('unlock-price').textContent='—';
    return;
  }
  $('unlock-currency').textContent=unlockInfo.currency||'ETB';
  $('unlock-price').textContent=unlockInfo.price!=null?Number(unlockInfo.price).toFixed(2):'—';

  var rows='';
  if(unlockInfo.expires_at)rows+='<div class="info-row"><span class="lbl">{{ __('expiresAt') }}</span><span class="val">'+esc(fmtDate(unlockInfo.expires_at))+'</span></div>';
  if(unlockInfo.unlocked_until)rows+='<div class="info-row"><span class="lbl">{{ __('unlockedUntil') }}</span><span class="val">'+esc(fmtDate(unlockInfo.unlocked_until))+'</span></div>';
  if(unlockInfo.description)rows+='<div class="info-row"><span class="lbl">{{ __('description') }}</span><span class="val">'+esc(unlockInfo.description)+'</span></div>';
  if(!rows)rows='<div class="info-row"><span class="lbl">{{ __('noAdditionalDetails') }}</span></div>';
  $('unlock-rows').innerHTML=rows;
}

function loadUnlock(){
  needId=getNeedId();
  if(!needId){showOnly('error');return;}
  var token=getToken();
  if(!token){window.location.href='/';return;}

  var backHref='/needs/'+encodeURIComponent(needId);
  $('back-btn').href=backHref;
  $('cancel-btn').href=backHref;

  showOnly('loading');

  // Best-effort: try to fetch need to show details
  fetch('/api/v1/needs/'+encodeURIComponent(needId),{
    headers:{'Accept':'application/json','Authorization':'Bearer '+token}
  })
  .then(function(r){
    if(r.status===401)throw new Error('auth');
    if(r.status===404)throw new Error('notfound');
    if(!r.ok)throw new Error('HTTP '+r.status);
    return r.json();
  })
  .then(function(j){
    var need=(j&&j.data)?j.data:j;

    // No unlock API exists yet — show placeholder with pending notice
    unlockInfo=need.unlock_info||null;
    renderUnlock();
    $('api-warn').className='notice-warn on';
    showOnly('content');
  })
  .catch(function(e){
    var m=String(e.message||e);
    if(m==='auth'){localStorage.removeItem(LS_TOKEN);window.location.href='/';return;}
    showOnly('error');
  });
}

function doUnlock(){
  toast('{{ __('featurePendingBody') }}', 4000);
}

$('unlock-btn').addEventListener('click',doUnlock);

window.addEventListener('offline',function(){$('offline-banner').className='offline-banner on';});
window.addEventListener('online',function(){$('offline-banner').className='offline-banner';loadUnlock();});

if(navigator.onLine===false){$('offline-banner').className='offline-banner on';}

window.loadUnlock=loadUnlock;
loadUnlock();
})();
</script>
</body>
</html>
