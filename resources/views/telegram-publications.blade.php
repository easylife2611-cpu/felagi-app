<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>{{ __('screenS022') }} — {{ __('brand') }}</title>
<style>
*{box-sizing:border-box;margin:0;padding:0}
body{font-family:system-ui,-apple-system,sans-serif;background:#F4F6F8;color:#192431;line-height:1.5;min-height:100vh;padding-bottom:80px}
:focus-visible{outline:3px solid #1b5e20;outline-offset:2px;border-radius:6px}
.sr-only{position:absolute;width:1px;height:1px;padding:0;margin:-1px;overflow:hidden;clip:rect(0,0,0,0);white-space:nowrap;border:0}
#page-title:focus{outline:none}
header{background:#003366;color:#fff;height:64px;padding:0 16px;display:flex;align-items:center;gap:12px;position:sticky;top:0;z-index:30}
header a{color:#fff;text-decoration:none;font-size:20px;padding:8px;border-radius:6px;line-height:1}
header .title{font-size:16px;font-weight:600;flex:1;overflow:hidden;text-overflow:ellipsis;white-space:nowrap}
main{max-width:720px;margin:0 auto;padding:16px}
.intro{background:#e3f2fd;border:1px solid #bbdefb;color:#0d47a1;padding:14px 16px;border-radius:10px;font-size:13px;line-height:1.5;margin-bottom:16px}
.intro strong{display:block;margin-bottom:4px}
.notice-warn{background:#fff8e1;border:1px solid #ffe082;color:#8a6d00;padding:14px 16px;border-radius:10px;font-size:13px;line-height:1.5;margin-bottom:16px;display:none}
.notice-warn.on{display:block}
.state{padding:60px 20px;text-align:center;color:#586675}
.state h3{color:#003366;font-size:18px;margin:0 0 8px}
.state p{margin:0 0 16px}
.state .icon-big{font-size:48px;margin-bottom:12px;opacity:.4}
.card{background:#fff;border-radius:12px;padding:18px;margin-bottom:12px;box-shadow:0 1px 3px rgba(0,0,0,.08)}
.card .top{display:flex;justify-content:space-between;align-items:flex-start;gap:10px;margin-bottom:10px}
.card .channel{font-size:15px;font-weight:700;color:#192431;display:flex;align-items:center;gap:6px}
.badge{display:inline-block;padding:3px 10px;border-radius:999px;font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.5px;flex:0 0 auto}
.badge.sent{background:#e8f5e9;color:#1b5e20}
.badge.pending{background:#fff3e0;color:#e65100}
.badge.failed{background:#ffebee;color:#b71c1c}
.badge.stopped{background:#f5f5f5;color:#616161}
.info-row{display:flex;justify-content:space-between;padding:8px 0;border-bottom:1px solid #eef1f4;font-size:13px}
.info-row:last-child{border-bottom:none}
.info-row .lbl{color:#586675}
.info-row .val{font-weight:600;text-align:right;word-break:break-word;max-width:60%}
.card .actions{display:flex;gap:8px;margin-top:12px;padding-top:12px;border-top:1px solid #eef1f4}
.card .actions .btn{flex:1;padding:10px 14px;font-size:13px}
.btn{padding:12px 20px;border-radius:8px;font-size:15px;font-weight:600;cursor:pointer;border:none;font-family:inherit;text-decoration:none;text-align:center;display:block;transition:background .15s}
.btn.primary{background:#003366;color:#fff}
.btn.primary:hover:not(:disabled){background:#002a52}
.btn.primary:disabled{opacity:.5;cursor:not-allowed}
.btn.sec{background:#eef1f4;color:#192431}
.btn.sec:hover{background:#e0e4e8}
.btn.danger{background:#fff;color:#c62828;border:1px solid #ffcdd2}
.btn.danger:hover:not(:disabled){background:#ffebee}
.btn.danger:disabled{opacity:.5;cursor:not-allowed}
.spinner{display:inline-block;width:20px;height:20px;border:3px solid #e0e0e0;border-top-color:#003366;border-radius:50%;animation:spin .8s linear infinite}
@keyframes spin{to{transform:rotate(360deg)}}
.offline-banner{background:#fff8e1;border:1px solid #ffe082;color:#8a6d00;padding:10px 14px;border-radius:8px;font-size:13px;margin-bottom:16px;display:none}
.offline-banner.on{display:block}
.toast{position:fixed;bottom:100px;left:50%;transform:translateX(-50%);background:#192431;color:#fff;padding:12px 20px;border-radius:8px;font-size:14px;z-index:40;opacity:0;pointer-events:none;transition:opacity .2s;max-width:90vw;text-align:center}
.toast.on{opacity:1}
</style>
</head>
<body>
<header>
<a href="#" id="back-btn" title="{{ __('back') }}">&#8592;</a>
<span class="title">{{ __('screenS022') }}</span>
</header>

<main role="main" aria-labelledby="page-title">
<h2 id="page-title" tabindex="-1" class="sr-only">{{ __('screenS022') }}</h2>
<div class="intro">
<strong>{{ __('telegramIntroTitle') }}</strong>
{{ __('telegramIntroBody') }}
</div>

<div class="notice-warn" id="api-warn" role="alert">
<strong>{{ __('featurePendingTitle') }}</strong><br>
{{ __('featurePendingBody') }}
</div>

<div class="offline-banner" id="offline-banner">{{ __('offlineBody') }}</div>

<div id="state-loading" class="state" style="padding:80px 20px">
<div class="spinner"></div>
<p style="margin-top:12px">{{ __('loading') }}...</p>
</div>

<div id="state-empty" class="state" hidden>
<div class="icon-big">&#128172;</div>
<h3>{{ __('noPublicationsTitle') }}</h3>
<p>{{ __('noPublicationsBody') }}</p>
<a href="#" id="need-btn-empty" class="btn sec" style="display:inline-block;max-width:220px;margin-top:12px">{{ __('backToNeed') }}</a>
</div>

<div id="state-error" class="state" hidden>
<h3>{{ __('loadErrorTitle') }}</h3>
<p>{{ __('loadErrorBody') }}</p>
<button type="button" class="btn sec" onclick="loadPublications()" style="display:inline-block;max-width:180px;margin-top:12px">{{ __('retry') }}</button>
</div>

<div id="content" hidden>
<div id="publications-list"></div>
</div>
</main>

<div class="toast" id="toast" role="status" aria-live="polite"></div>

<script>
(function(){
'use strict';
var csrf=(document.querySelector('meta[name="csrf-token"]')||{}).content||'';
var LS_TOKEN='felagi_token';
var needId='';
var publications=[];

function $(id){return document.getElementById(id);}
function getToken(){return 'session'; /* L305d */}
function esc(s){return String(s==null?'':s).replace(/[&<>"']/g,function(c){return {'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c];});}

function getNeedId(){
  var parts=window.location.pathname.split('/').filter(Boolean);
  return parts.length>=2?parts[1]:'';
}

function toast(msg,ms){ms=ms||2600;var el=$('toast');el.textContent=msg;el.className='toast on';setTimeout(function(){el.className='toast';},ms);}

function showOnly(name){
  ['state-loading','state-empty','state-error','content'].forEach(function(id){
    var el=$(id); if(el)el.hidden=(id!==((name==='content')?'content':'state-'+name));
  });
}

function fmtDate(iso){
  if(!iso)return '—';
  var d=new Date(iso);
  if(isNaN(d))return '—';
  return d.toLocaleDateString()+' '+d.toLocaleTimeString([],{hour:'2-digit',minute:'2-digit'});
}

function statusLabel(s){
  return ({
    'SENT':'{{ __('pubStatusSent') }}',
    'PENDING':'{{ __('pubStatusPending') }}',
    'FAILED':'{{ __('pubStatusFailed') }}',
    'STOPPED':'{{ __('pubStatusStopped') }}',
    'STOP_REQUESTED':'{{ __('pubStatusStopping') }}'
  })[s]||s||'';
}

function isStoppable(p){
  var s=String(p.status||'').toUpperCase();
  return s==='SENT'||s==='PENDING';
}

function renderPublications(){
  var wrap=$('publications-list');
  wrap.innerHTML=publications.map(function(p){
    var s=String(p.status||'').toLowerCase();
    var channel=(p.channel||'telegram').toUpperCase();
    var badge='<span class="badge '+esc(s)+'">'+esc(statusLabel(p.status))+'</span>';
    var stoppable=isStoppable(p);

    var rows='';
    rows+='<div class="info-row"><span class="lbl">{{ __('publishedAt') }}</span><span class="val">'+esc(fmtDate(p.sent_at||p.published_at||p.created_at))+'</span></div>';
    if(p.telegram_message_id)rows+='<div class="info-row"><span class="lbl">{{ __('messageId') }}</span><span class="val">'+esc(p.telegram_message_id)+'</span></div>';
    if(p.destination&&p.destination.name)rows+='<div class="info-row"><span class="lbl">{{ __('destination') }}</span><span class="val">'+esc(p.destination.name)+'</span></div>';
    if(p.error_message||p.failure_reason)rows+='<div class="info-row"><span class="lbl">{{ __('error') }}</span><span class="val" style="color:#b71c1c">'+esc(p.error_message||p.failure_reason)+'</span></div>';

    var stopBtn=stoppable
      ? '<button type="button" class="btn danger" data-action="stop" data-id="'+esc(p.id)+'">{{ __('stopPublication') }}</button>'
      : '<button type="button" class="btn sec" disabled>{{ __('cannotStop') }}</button>';

    return '<div class="card">'+
      '<div class="top">'+
        '<div class="channel">&#128172; '+esc(channel)+'</div>'+
        badge+
      '</div>'+
      rows+
      '<div class="actions">'+stopBtn+'</div>'+
    '</div>';
  }).join('');

  Array.prototype.forEach.call(wrap.querySelectorAll('[data-action="stop"]'),function(btn){
    btn.addEventListener('click',function(){stopPublication(btn.dataset.id);});
  });
}

function stopPublication(id){
  if(!confirm('{{ __('confirmStop') }}'))return;
  var token=getToken();
  /* L305d */

  var btn=document.querySelector('[data-id="'+id+'"]');
  if(btn){btn.disabled=true;btn.textContent='{{ __('stopping') }}...';}

  fetch('/api/v1/needs/'+encodeURIComponent(needId)+'/telegram-publication/stop',{
    method:'POST',
    headers:{
      'Accept':'application/json',
      'Content-Type':'application/json',
      'X-CSRF-TOKEN':csrf
    },
    body:JSON.stringify({publication_id:id})
  })
  .then(function(r){return r.json().then(function(j){return {s:r.status,b:j};});})
  .then(function(res){
    if(res.s===200||res.s===201){
      toast('{{ __('publicationStopped') }}');
      setTimeout(loadPublications,600);
      return;
    }
    if(res.s===409){
      toast('{{ __('stateConflict') }}',3000);
      setTimeout(loadPublications,800);
      return;
    }
    if(res.s===403){toast('{{ __('notAllowed') }}');return;}
    if(res.s===404){toast('{{ __('notFound') }}');return;}
    if(res.s===401){localStorage.removeItem(LS_TOKEN);window.location.href='/';return;}
    toast('{{ __('actionFailed') }}');
  })
  .catch(function(){toast('{{ __('actionFailed') }}');})
  .then(function(){
    if(btn){btn.disabled=false;btn.textContent='{{ __('stopPublication') }}';}
  });
}

function loadPublications(){
  needId=getNeedId();
  if(!needId){showOnly('error');return;}
  var token=getToken();
  /* L305d */

  var backHref='/needs/'+encodeURIComponent(needId);
  $('back-btn').href=backHref;
  $('need-btn-empty').href=backHref;

  showOnly('loading');

  fetch('/api/v1/needs/'+encodeURIComponent(needId)+'/telegram-publications',{
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
    publications=Array.isArray(data)?data:[];
    if(!publications.length){
      showOnly('empty');
      return;
    }
    renderPublications();
    showOnly('content');
  })
  .catch(function(e){
    var m=String(e.message||e);
    if(m==='auth'){localStorage.removeItem(LS_TOKEN);window.location.href='/';return;}
    $('api-warn').className='notice-warn on';
    showOnly('error');
  });
}

window.loadPublications=loadPublications;

window.addEventListener('offline',function(){$('offline-banner').className='offline-banner on';});
window.addEventListener('online',function(){$('offline-banner').className='offline-banner';loadPublications();});

if(navigator.onLine===false){$('offline-banner').className='offline-banner on';}

var h=document.getElementById('page-title');
if(h){try{h.focus();}catch(e){}}

loadPublications();
})();
</script>
</body>
</html>
