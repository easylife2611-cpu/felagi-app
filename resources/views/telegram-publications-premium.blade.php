<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>{{ __('screenS022') }} — {{ __('brand') }}</title>
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
.intro strong{display:block;margin-bottom:5px;font-weight:900;letter-spacing:-.01em}
.notice-warn{display:none;background:linear-gradient(135deg,var(--warn-100),#fffbf0);border:1px solid rgba(168,101,0,.18);color:var(--warn-600);padding:14px 16px;border-radius:var(--r-md);font-size:13px;line-height:1.6;margin-bottom:16px;font-weight:600}
.notice-warn.on{display:block}
.notice-warn strong{display:block;margin-bottom:4px;font-weight:900;letter-spacing:-.01em}
.cd{background:var(--surface);border:1px solid var(--line-100);border-radius:var(--r-lg);padding:20px;margin-bottom:12px;box-shadow:var(--sh-xs);position:relative;overflow:hidden;transition:all .25s var(--ease-out)}
.cd:hover{box-shadow:var(--sh-sm)}
.cd::before{content:'';position:absolute;top:0;left:0;right:0;height:3px;background:linear-gradient(90deg,var(--orange-500),var(--navy-700));opacity:0;transition:opacity .3s var(--ease)}
.cd:hover::before{opacity:1}
.cd .top{display:flex;justify-content:space-between;align-items:flex-start;gap:12px;margin-bottom:14px}
.cd .ch{font-size:15px;font-weight:900;color:var(--ink-900);display:flex;align-items:center;gap:8px;letter-spacing:-.01em;font-family:var(--f-en)}
.cd .ch svg{width:22px;height:22px;stroke:currentColor;fill:none;stroke-width:2;stroke-linecap:round;stroke-linejoin:round;color:#229ED9}
.bg{display:inline-flex;align-items:center;gap:5px;padding:5px 11px;border-radius:var(--r-pill);font-size:10px;font-weight:800;letter-spacing:.05em;font-family:var(--f-en);text-transform:uppercase;flex:0 0 auto}
.bg::before{content:'●';font-size:6px}
.bg.sent{background:var(--success-100);color:var(--success-600)}
.bg.pending,.bg.stop_requested{background:var(--warn-100);color:var(--warn-600)}
.bg.failed{background:var(--danger-100);color:var(--danger-600)}
.bg.stopped{background:var(--line-100);color:var(--ink-500)}
.info-row{display:flex;justify-content:space-between;gap:12px;padding:10px 0;border-bottom:1px solid var(--line-100);font-size:13px}
.info-row:last-child{border-bottom:none}
.info-row .lbl{color:var(--ink-300);font-weight:800;font-size:11px;text-transform:uppercase;letter-spacing:.05em;font-family:var(--f-en);flex:0 0 auto}
.info-row .val{font-weight:800;color:var(--ink-900);text-align:right;word-break:break-word;max-width:65%;font-family:var(--f-en)}
.info-row .val.err{color:var(--danger-600)}
.cd .acts{display:flex;gap:8px;margin-top:14px;padding-top:14px;border-top:1px solid var(--line-100)}
.bt{display:inline-flex;align-items:center;justify-content:center;gap:9px;height:48px;padding:0 22px;border-radius:var(--r-sm);font-weight:900;font-size:14px;line-height:1;letter-spacing:-.015em;transition:all .2s var(--ease);font-family:var(--f-am);text-align:center;flex:1}
.bt:active{transform:scale(.975)}
.bt-n{background:linear-gradient(135deg,var(--navy-800),var(--navy-700));color:#fff}
.bt-n:hover:not(:disabled){transform:translateY(-1px);box-shadow:var(--sh-navy)}
.bt-danger{background:#fff;color:var(--danger-600);border:1.5px solid rgba(192,57,43,.25)}
.bt-danger:hover:not(:disabled){background:var(--danger-100)}
.bt-danger:disabled{opacity:.5;cursor:not-allowed}
.bt-ghost{background:var(--canvas-2);color:var(--ink-500);border:1.5px solid var(--line-200)}
.bt-ghost:disabled{opacity:.7;cursor:not-allowed}
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
    <h1 class="t" id="page-title" tabindex="-1" role="heading" aria-level="1">{{ __('screenS022') }}</h1>
  </div>
</div>
<div class="sb">
  <div class="intro">
    <strong>{{ __('telegramIntroTitle') }}</strong>
    {{ __('telegramIntroBody') }}
  </div>

  <div class="notice-warn" id="api-warn" role="alert">
    <strong>{{ __('featurePendingTitle') }}</strong>
    {{ __('featurePendingBody') }}
  </div>

  <div class="offline-banner" id="offline-banner" role="status" aria-live="polite">{{ __('offlineBody') }}</div>

  <div id="state-loading" class="state" style="padding:80px 24px">
    <div class="spinner"></div>
    <p style="margin-top:14px">{{ __('loading') }}...</p>
  </div>

  <div id="state-empty" class="state" hidden>
    <div class="ic">💬</div>
    <h3>{{ __('noPublicationsTitle') }}</h3>
    <p>{{ __('noPublicationsBody') }}</p>
    <a href="#" id="need-btn-empty" class="bt bt-n" style="display:inline-flex;max-width:220px;margin:0 auto">{{ __('backToNeed') }}</a>
  </div>

  <div id="state-error" class="state" hidden>
    <div class="ic">⚠️</div>
    <h3>{{ __('loadErrorTitle') }}</h3>
    <p>{{ __('loadErrorBody') }}</p>
    <button type="button" class="bt bt-n" onclick="loadPublications()">{{ __('retry') }}</button>
  </div>

  <div id="content" hidden>
    <div id="publications-list"></div>
  </div>
</div>
</div>
<div class="toast" id="toast" role="status" aria-live="polite"></div>
<script>
(function(){
'use strict';
var csrf=(document.querySelector('meta[name="csrf-token"]')||{}).content||'';
var LS_TOKEN='felagi_token';
var needId='';
var publications=[];
function $(id){return document.getElementById(id);}
function getToken(){return 'session';}
function esc(s){return String(s==null?'':s).replace(/[&<>"']/g,function(c){return {'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c];});}
function getNeedId(){var parts=window.location.pathname.split('/').filter(Boolean);return parts.length>=2?parts[1]:'';}
function toast(msg,ms){ms=ms||2600;var el=$('toast');el.textContent=msg;el.className='toast on';setTimeout(function(){el.className='toast';},ms);}
function showOnly(name){
  ['state-loading','state-empty','state-error','content'].forEach(function(id){var el=$(id);if(el)el.hidden=(id!==('state-'+name));});
}
function fmtDate(iso){if(!iso)return '—';var d=new Date(iso);if(isNaN(d))return '—';return d.toLocaleDateString()+' '+d.toLocaleTimeString([],{hour:'2-digit',minute:'2-digit'});}
function statusLabel(s){return ({'SENT':'{{ __('pubStatusSent') }}','PENDING':'{{ __('pubStatusPending') }}','FAILED':'{{ __('pubStatusFailed') }}','STOPPED':'{{ __('pubStatusStopped') }}','STOP_REQUESTED':'{{ __('pubStatusStopping') }}'})[s]||s||'';}
function isStoppable(p){var s=String(p.status||'').toUpperCase();return s==='SENT'||s==='PENDING';}
function renderPublications(){
  var wrap=$('publications-list');
  wrap.innerHTML=publications.map(function(p){
    var s=String(p.status||'').toLowerCase();
    var channel=(p.channel||'telegram').toUpperCase();
    var badge='<span class="bg '+esc(s)+'">'+esc(statusLabel(p.status))+'</span>';
    var stoppable=isStoppable(p);
    var rows='';
    rows+='<div class="info-row"><span class="lbl">{{ __('publishedAt') }}</span><span class="val">'+esc(fmtDate(p.sent_at||p.published_at||p.created_at))+'</span></div>';
    if(p.telegram_message_id)rows+='<div class="info-row"><span class="lbl">{{ __('messageId') }}</span><span class="val">'+esc(p.telegram_message_id)+'</span></div>';
    if(p.destination&&p.destination.name)rows+='<div class="info-row"><span class="lbl">{{ __('destination') }}</span><span class="val">'+esc(p.destination.name)+'</span></div>';
    if(p.error_message||p.failure_reason)rows+='<div class="info-row"><span class="lbl">{{ __('error') }}</span><span class="val err">'+esc(p.error_message||p.failure_reason)+'</span></div>';
    var stopBtn=stoppable
      ? '<button type="button" class="bt bt-danger" data-action="stop" data-id="'+esc(p.id)+'">{{ __('stopPublication') }}</button>'
      : '<button type="button" class="bt bt-ghost" disabled>{{ __('cannotStop') }}</button>';
    return '<div class="cd">'+
      '<div class="top">'+
        '<div class="ch"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M22 2 11 13M22 2l-7 20-4-9-9-4 20-7z"/></svg>'+esc(channel)+'</div>'+
        badge+
      '</div>'+
      rows+
      '<div class="acts">'+stopBtn+'</div>'+
    '</div>';
  }).join('');
  Array.prototype.forEach.call(wrap.querySelectorAll('[data-action="stop"]'),function(btn){
    btn.addEventListener('click',function(){stopPublication(btn.dataset.id);});
  });
}
function stopPublication(id){
  if(!confirm('{{ __('confirmStop') }}'))return;
  var btn=document.querySelector('[data-id="'+id+'"]');
  if(btn){btn.disabled=true;btn.textContent='{{ __('stopping') }}...';}
  fetch('/api/v1/needs/'+encodeURIComponent(needId)+'/telegram-publication/stop',{
    method:'POST',
    headers:{'Accept':'application/json','Content-Type':'application/json','X-CSRF-TOKEN':csrf},
    body:JSON.stringify({publication_id:id})
  })
  .then(function(r){return r.json().then(function(j){return {s:r.status,b:j};});})
  .then(function(res){
    if(res.s===200||res.s===201){toast('{{ __('publicationStopped') }}');setTimeout(loadPublications,600);return;}
    if(res.s===409){toast('{{ __('stateConflict') }}',3000);setTimeout(loadPublications,800);return;}
    if(res.s===403){toast('{{ __('notAllowed') }}');return;}
    if(res.s===404){toast('{{ __('notFound') }}');return;}
    if(res.s===401){localStorage.removeItem(LS_TOKEN);window.location.href='/';return;}
    toast('{{ __('actionFailed') }}');
  })
  .catch(function(){toast('{{ __('actionFailed') }}');})
  .then(function(){if(btn){btn.disabled=false;btn.textContent='{{ __('stopPublication') }}';}});
}
function loadPublications(){
  needId=getNeedId();
  if(!needId){showOnly('error');return;}
  var backHref='/needs/'+encodeURIComponent(needId);
  $('back-btn').href=backHref;
  $('need-btn-empty').href=backHref;
  showOnly('loading');
  fetch('/api/v1/needs/'+encodeURIComponent(needId)+'/telegram-publications',{credentials:'same-origin',headers:{'Accept':'application/json'}})
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
    if(!publications.length){showOnly('empty');return;}
    renderPublications();showOnly('content');
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
