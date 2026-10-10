<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>{{ __('screenS017') }} — {{ __('brand') }}</title>
<meta name="theme-color" content="#003366">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Ethiopic:wght@400;500;600;700;800;900&family=Inter:wght@400;600;700;800;900&display=swap" rel="stylesheet">
<style>
:root{--navy-950:#001a33;--navy-900:#002b52;--navy-800:#003366;--navy-700:#0b4d83;--navy-600:#1a6bb0;--orange-700:#b35c00;--orange-600:#e8851d;--orange-500:#FF9933;--orange-400:#ffb366;--orange-100:#fff2e0;--ink-900:#0d1a2b;--ink-500:#3c4d61;--ink-300:#5f7185;--ink-200:#8fa0b3;--line-100:#eef2f6;--line-200:#e3e9ef;--line-300:#d5dde6;--surface:#fff;--canvas:#f4f7fa;--canvas-2:#eef3f8;--success-600:#0e6b34;--success-500:#147a3d;--success-100:#e8f5ee;--warn-600:#a86500;--warn-100:#fff7e6;--danger-600:#a32e21;--danger-500:#c0392b;--danger-100:#fdecea;--info-600:#0056d6;--info-100:#e7f1ff;--r-xs:8px;--r-sm:12px;--r-md:16px;--r-lg:22px;--r-pill:999px;--sh-xs:0 1px 2px rgba(0,26,51,.04),0 1px 3px rgba(0,26,51,.06);--sh-sm:0 2px 6px rgba(0,26,51,.05),0 4px 12px rgba(0,26,51,.06);--sh-md:0 4px 12px rgba(0,26,51,.06),0 12px 28px rgba(0,26,51,.08);--sh-navy:0 12px 32px rgba(0,51,102,.28);--sh-orange:0 8px 20px rgba(255,153,51,.32);--f-am:'Noto Sans Ethiopic','Inter',system-ui,sans-serif;--f-en:'Inter','Noto Sans Ethiopic',system-ui,sans-serif;--ease:cubic-bezier(.16,.84,.44,1);--ease-out:cubic-bezier(.22,1,.36,1)}
*{box-sizing:border-box;margin:0;padding:0}
html,body{height:100%}
html{background:var(--canvas)}
body{font-family:var(--f-am);color:var(--ink-900);line-height:1.55;display:flex;flex-direction:column;-webkit-font-smoothing:antialiased;background:var(--canvas)}
a{color:inherit;text-decoration:none}
button{font-family:inherit;cursor:pointer;border:0;background:transparent;color:inherit}
textarea{font-family:inherit;font-size:inherit}
:focus-visible{outline:3px solid var(--orange-500);outline-offset:3px;border-radius:8px}
.sr-only{position:absolute;width:1px;height:1px;padding:0;margin:-1px;overflow:hidden;clip:rect(0,0,0,0);white-space:nowrap;border:0}
.sh{background:radial-gradient(ellipse at top right,rgba(255,153,51,.18),transparent 55%),radial-gradient(ellipse at bottom left,rgba(26,107,176,.35),transparent 60%),linear-gradient(140deg,var(--navy-950),var(--navy-800));color:#fff;padding:16px 18px;display:flex;align-items:center;gap:12px;position:sticky;top:0;z-index:30;flex:0 0 auto;box-shadow:0 4px 16px rgba(0,26,51,.18)}
.sh .ib{width:40px;height:40px;border-radius:50%;display:inline-flex;align-items:center;justify-content:center;color:#fff;background:rgba(255,255,255,.10);border:1px solid rgba(255,255,255,.08);transition:all .2s var(--ease);flex:0 0 auto}
.sh .ib:hover{background:rgba(255,255,255,.18)}
.sh .ib svg{width:20px;height:20px;stroke:currentColor;fill:none;stroke-width:2;stroke-linecap:round;stroke-linejoin:round}
.sh .meta{flex:1;min-width:0}
.sh .t{font-size:15px;font-weight:900;letter-spacing:-.02em;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;line-height:1.3}
.sh .sub{font-size:11px;opacity:.72;font-weight:600;font-family:var(--f-en);margin-top:2px}
.sb{flex:1;overflow-y:auto;padding:18px 16px;max-width:720px;width:100%;margin:0 auto;display:flex;flex-direction:column}
.sb::-webkit-scrollbar{width:6px}
.sb::-webkit-scrollbar-thumb{background:var(--line-200);border-radius:3px}
.day-sep{text-align:center;font-size:10.5px;color:var(--ink-300);margin:18px 0 14px;font-weight:800;letter-spacing:.08em;text-transform:uppercase;font-family:var(--f-en);position:relative}
.day-sep::before,.day-sep::after{content:'';position:absolute;top:50%;width:calc(50% - 60px);height:1px;background:var(--line-200)}
.day-sep::before{left:0}
.day-sep::after{right:0}
.msg{display:flex;gap:10px;margin-bottom:14px;align-items:flex-end;animation:fadeUp .35s var(--ease-out) backwards}
@keyframes fadeUp{from{opacity:0;transform:translateY(8px)}to{opacity:1;transform:translateY(0)}}
.msg.mine{flex-direction:row-reverse}
.av{width:34px;height:34px;border-radius:50%;background:linear-gradient(135deg,var(--navy-800),var(--navy-600));color:#fff;display:flex;align-items:center;justify-content:center;font-size:12.5px;font-weight:900;flex:0 0 auto;overflow:hidden;box-shadow:0 2px 6px rgba(0,51,102,.16)}
.av.orange{background:linear-gradient(135deg,var(--orange-500),var(--orange-600))}
.av img{width:100%;height:100%;object-fit:cover}
.bubble{max-width:76%;padding:11px 15px;border-radius:18px 18px 18px 6px;background:var(--surface);box-shadow:var(--sh-xs);word-wrap:break-word;font-size:14px;line-height:1.6;border:1px solid var(--line-100);font-weight:500}
.msg.mine .bubble{background:linear-gradient(135deg,var(--navy-800),var(--navy-700));color:#fff;border-color:transparent;border-radius:18px 18px 6px 18px;box-shadow:var(--sh-navy)}
.bubble .sender{font-size:11px;font-weight:900;margin-bottom:5px;display:block;color:var(--navy-800);letter-spacing:-.005em}
.msg.mine .bubble .sender{color:rgba(255,255,255,.85)}
.bubble .meta{font-size:9.5px;opacity:.65;margin-top:5px;display:block;font-family:var(--f-en);font-weight:700;letter-spacing:.02em}
.bubble .meta.right{text-align:right}
.state{padding:60px 24px;text-align:center;color:var(--ink-300);flex:1;display:flex;flex-direction:column;justify-content:center;align-items:center}
.state .ic{font-size:44px;margin-bottom:14px;opacity:.5}
.state h3{font-size:18px;font-weight:900;color:var(--navy-800);margin-bottom:8px}
.state p{font-size:13.5px;line-height:1.6;color:var(--ink-500);max-width:300px;margin:0 auto 16px}
.composer{background:rgba(255,255,255,.98);backdrop-filter:blur(12px);border-top:1px solid var(--line-200);padding:12px 16px;display:flex;gap:10px;align-items:flex-end;max-width:720px;width:100%;margin:0 auto;flex:0 0 auto;box-shadow:0 -4px 16px rgba(0,26,51,.06)}
.composer textarea{flex:1;padding:12px 16px;border:1.5px solid var(--line-300);border-radius:22px;font-size:14.5px;resize:none;max-height:130px;min-height:46px;line-height:1.5;background:var(--canvas-2);font-weight:500;transition:all .2s var(--ease)}
.composer textarea:focus{outline:none;border-color:var(--navy-800);background:#fff;box-shadow:0 0 0 4px rgba(0,51,102,.08)}
.composer textarea::placeholder{color:var(--ink-200)}
.composer button{background:linear-gradient(135deg,var(--orange-500),var(--orange-600));color:#fff;border:0;width:46px;height:46px;border-radius:50%;cursor:pointer;font-size:18px;display:flex;align-items:center;justify-content:center;flex:0 0 auto;transition:all .2s var(--ease);box-shadow:var(--sh-orange)}
.composer button:hover:not(:disabled){transform:scale(1.06) rotate(-5deg)}
.composer button:disabled{opacity:.5;cursor:not-allowed;transform:none}
.bt{display:inline-flex;align-items:center;justify-content:center;gap:9px;height:48px;padding:0 22px;border-radius:var(--r-sm);font-weight:900;font-size:14px;line-height:1;transition:all .2s var(--ease);font-family:var(--f-am)}
.bt-n{background:linear-gradient(135deg,var(--navy-800),var(--navy-700));color:#fff}
.bt-n:hover{transform:translateY(-1px);box-shadow:var(--sh-navy)}
.skeleton-msg{display:flex;gap:10px;margin-bottom:14px}
.sk-bubble{height:46px;background:var(--line-100);border-radius:18px;width:62%}
.sk-bubble.right{margin-left:auto;background:linear-gradient(135deg,rgba(0,51,102,.08),rgba(0,51,102,.04))}
.spinner{display:inline-block;width:16px;height:16px;border:2px solid rgba(255,255,255,.4);border-top-color:#fff;border-radius:50%;animation:spin .8s linear infinite}
@keyframes spin{to{transform:rotate(360deg)}}
.offline-banner{display:none;background:var(--warn-100);color:var(--warn-600);padding:10px 16px;font-size:12.5px;font-weight:800;text-align:center;flex:0 0 auto}
.offline-banner.on{display:block}
.toast{position:fixed;bottom:100px;left:50%;transform:translateX(-50%) translateY(20px);background:var(--navy-900);color:#fff;padding:12px 20px;border-radius:var(--r-pill);font-size:13px;font-weight:700;opacity:0;pointer-events:none;transition:all .3s var(--ease-out);z-index:50;box-shadow:var(--sh-md);max-width:90vw;text-align:center}
.toast.on{opacity:1;transform:translateX(-50%) translateY(0)}
</style>
</head>
<body>
<div class="sh">
  <a href="#" id="back-btn" class="ib" title="{{ __('back') }}" aria-label="{{ __('back') }}">
    <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M19 12H5M12 19l-7-7 7-7"/></svg>
  </a>
  <div class="meta">
    <div class="t" id="page-title" tabindex="-1" role="heading" aria-level="1">{{ __('messagesTitle') }}</div>
    <div class="sub" id="page-sub"></div>
  </div>
</div>

<div class="offline-banner" id="offline-banner" role="status" aria-live="polite">{{ __('offlineBody') }}</div>

<div id="state-loading" style="flex:1;padding:18px 16px;max-width:720px;margin:0 auto;width:100%">
  <div class="skeleton-msg"><div class="sk-bubble"></div></div>
  <div class="skeleton-msg"><div class="sk-bubble right"></div></div>
  <div class="skeleton-msg"><div class="sk-bubble"></div></div>
  <div class="skeleton-msg"><div class="sk-bubble right"></div></div>
</div>

<div id="state-denied" class="state" hidden>
  <div class="ic">🔒</div>
  <h3>{{ __('accessDenied') }}</h3>
  <p>{{ __('accessDeniedMessages') }}</p>
  <a href="/browse" class="bt bt-n">{{ __('backToBrowse') }}</a>
</div>

<div id="state-error" class="state" hidden>
  <div class="ic">⚠️</div>
  <h3>{{ __('loadErrorTitle') }}</h3>
  <p>{{ __('loadErrorBody') }}</p>
  <button type="button" class="bt bt-n" onclick="loadMessages()">{{ __('retry') }}</button>
</div>

<main id="state-list" class="sb" hidden role="main" aria-labelledby="page-title">
  <div id="messages-container"></div>
  <div id="empty-hint" class="state" hidden style="padding:40px 20px">
    <div class="ic">💬</div>
    <p style="color:var(--ink-500);font-size:14px">{{ __('noMessagesYet') }}</p>
  </div>
</main>

<div class="composer" id="composer" hidden>
  <textarea id="content" placeholder="{{ __('typeMessage') }}" maxlength="5000" rows="1" aria-label="{{ __('typeMessage') }}"></textarea>
  <button type="button" id="send-btn" title="{{ __('send') }}" aria-label="{{ __('send') }}">➤</button>
</div>

<div class="toast" id="toast" role="status" aria-live="polite"></div>
<script>
(function(){
'use strict';
var csrf=(document.querySelector('meta[name="csrf-token"]')||{}).content||'';
var LS_TOKEN='felagi_token';
var offerId='';
var meId=null;
var messages=[];
var lastFetchedId=null;
var pollTimer=null;
var DRAFT_KEY='';
function $(id){return document.getElementById(id);}
function getToken(){return 'session';}
function esc(s){return String(s==null?'':s).replace(/[&<>"']/g,function(c){return {'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c];});}
function getOfferId(){var parts=window.location.pathname.split('/').filter(Boolean);return parts.length>=2?parts[1]:'';}
function toast(msg,ms){ms=ms||2200;var el=$('toast');el.textContent=msg;el.className='toast on';setTimeout(function(){el.className='toast';},ms);}
function showOnly(name){
  ['state-loading','state-denied','state-error','state-list'].forEach(function(id){
    var el=$(id);if(el)el.hidden=(id!==('state-'+name));
  });
  var composer=$('composer');if(composer)composer.hidden=(name!=='list');
}
function fmtTime(iso){
  if(!iso)return '';
  var d=new Date(iso);
  if(isNaN(d))return '';
  return d.toLocaleTimeString([],{hour:'2-digit',minute:'2-digit'});
}
function fmtDay(iso){
  if(!iso)return '';
  var d=new Date(iso);
  if(isNaN(d))return '';
  var today=new Date();
  var yesterday=new Date();yesterday.setDate(yesterday.getDate()-1);
  if(d.toDateString()===today.toDateString())return '{{ __('today') }}';
  if(d.toDateString()===yesterday.toDateString())return '{{ __('yesterday') }}';
  return d.toLocaleDateString();
}
function renderMessages(){
  var wrap=$('messages-container');
  var empty=$('empty-hint');
  if(!messages.length){wrap.innerHTML='';empty.hidden=false;return;}
  empty.hidden=true;
  var html='';
  var lastDay='';
  messages.forEach(function(m){
    var day=fmtDay(m.created_at);
    if(day&&day!==lastDay){html+='<div class="day-sep">'+esc(day)+'</div>';lastDay=day;}
    var mine=m.sender_id===meId;
    var sender=m.sender||{};
    var initial=String(sender.full_name||'?').trim().charAt(0).toUpperCase();
    var avatar=sender.profile_photo_url?'<img src="'+esc(sender.profile_photo_url)+'" alt="">':esc(initial);
    var avCls='av'+(mine?'':' orange');
    var senderName=!mine&&sender.full_name?('<span class="sender">'+esc(sender.full_name)+'</span>'):'';
    html+='<div class="msg '+(mine?'mine':'')+'">'+
      '<div class="'+avCls+'">'+avatar+'</div>'+
      '<div class="bubble">'+senderName+esc(m.content||'')+
        '<span class="meta'+(mine?' right':'')+'">'+esc(fmtTime(m.created_at))+'</span>'+
      '</div>'+
    '</div>';
  });
  wrap.innerHTML=html;
  var main=document.querySelector('main.sb')||document.querySelector('main');
  if(main)main.scrollTop=main.scrollHeight;
}
function loadMessages(silent){
  if(!silent)showOnly('loading');
  var h={'Accept':'application/json'};
  fetch('/api/v1/offers/'+encodeURIComponent(offerId)+'/messages?per_page=100',{headers:h})
    .then(function(r){
      if(r.status===404)throw new Error('notfound');
      if(r.status===401)throw new Error('auth');
      if(r.status===403)throw new Error('denied');
      if(r.status===429)throw new Error('rate-limited');
      if(!r.ok)throw new Error('HTTP '+r.status);
      return r.json();
    })
    .then(function(j){
      var items=Array.isArray(j.data)?j.data:[];
      items.reverse();
      messages=items;
      renderMessages();
      if(!silent)showOnly('list');
    })
    .catch(function(e){
      var m=String(e.message||e);
      if(m==='auth'){localStorage.removeItem(LS_TOKEN);window.location.href='/';return;}
      if(m==='denied'){showOnly('denied');return;}
      if(!silent)showOnly('error');
    });
}
function loadOfferMeta(){
  fetch('/api/v1/offers/'+encodeURIComponent(offerId),{credentials:'same-origin',headers:{'Accept':'application/json'}})
  .then(function(r){return r.ok?r.json():null;})
  .then(function(j){
    var off=(j&&j.data)?j.data:j;
    if(!off)return;
    var need=off.need||{};
    $('page-title').textContent=need.title||'{{ __('messagesTitle') }}';
    $('page-sub').textContent=(off.currency||'ETB')+' '+(off.offered_price!=null?Number(off.offered_price).toFixed(2):'-');
    $('back-btn').href='/offers/'+encodeURIComponent(offerId);
  })
  .catch(function(){$('back-btn').href='/offers/'+encodeURIComponent(offerId);});
}
function sendMessage(){
  var content=$('content').value.trim();
  if(!content){toast('{{ __('emptyMessage') }}');return;}
  if(content.length>5000){toast('{{ __('messageTooLong') }}');return;}
  var btn=$('send-btn');
  btn.disabled=true;
  btn.innerHTML='<span class="spinner"></span>';
  fetch('/api/v1/offers/'+encodeURIComponent(offerId)+'/messages',{
    method:'POST',
    headers:{'Accept':'application/json','Content-Type':'application/json','X-CSRF-TOKEN':csrf},
    body:JSON.stringify({content:content})
  })
  .then(function(r){return r.json().then(function(j){return {s:r.status,b:j};});})
  .then(function(res){
    if(res.s===201||res.s===200){
      $('content').value='';
      try{localStorage.removeItem(DRAFT_KEY);}catch(e){}
      loadMessages(true);
    }else if(res.s===422){toast('{{ __('validationFailed') }}');}
    else if(res.s===403){toast('{{ __('notAllowed') }}');}
    else if(res.s===429){toast('{{ __('rateLimited') }}');}
    else{toast('{{ __('sendFailed') }}');}
  })
  .catch(function(){toast('{{ __('sendFailed') }}');})
  .then(function(){btn.disabled=false;btn.innerHTML='➤';});
}
function saveDraft(){try{localStorage.setItem(DRAFT_KEY,$('content').value);}catch(e){}}
function loadDraft(){try{var v=localStorage.getItem(DRAFT_KEY);if(v)$('content').value=v;}catch(e){}}
function autoGrow(){var el=$('content');el.style.height='auto';el.style.height=Math.min(el.scrollHeight,130)+'px';}
offerId=getOfferId();
if(!offerId){showOnly('error');}
else{
  DRAFT_KEY='felagi_draft_msg_'+offerId;
  fetch('/api/v1/auth/me', {credentials:'same-origin', headers:{'Accept':'application/json'}})
    .then(function(r){return r.ok?r.json():{data:null};})
    .then(function(me){if(me && me.data && me.data.id) meId = me.data.id;})
    .catch(function(){})
    .then(function(){
      loadOfferMeta();
      loadMessages();
      loadDraft();
    });
  $('send-btn').addEventListener('click',sendMessage);
  $('content').addEventListener('input',function(){saveDraft();autoGrow();});
  $('content').addEventListener('keydown',function(e){
    if(e.key==='Enter'&&!e.shiftKey){e.preventDefault();sendMessage();}
  });
  pollTimer=setInterval(function(){if(!document.hidden)loadMessages(true);},15000);
  window.addEventListener('offline',function(){$('offline-banner').className='offline-banner on';});
  window.addEventListener('online',function(){$('offline-banner').className='offline-banner';loadMessages(true);});
  if(navigator.onLine===false){$('offline-banner').className='offline-banner on';}
  window.loadMessages=loadMessages;
}
})();
</script>
</body>
</html>
