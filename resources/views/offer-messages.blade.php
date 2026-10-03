<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>{{ __('screenS017') }} — {{ __('brand') }}</title>
<style>
*{box-sizing:border-box;margin:0;padding:0}
html,body{height:100%}
body{font-family:system-ui,-apple-system,sans-serif;background:#F4F6F8;color:#192431;line-height:1.5;display:flex;flex-direction:column}
header{background:#003366;color:#fff;height:64px;padding:0 16px;display:flex;align-items:center;gap:12px;position:sticky;top:0;z-index:30;flex:0 0 auto}
header a{color:#fff;text-decoration:none;font-size:20px;padding:8px;border-radius:6px;line-height:1}
header a:hover{background:rgba(255,255,255,.12)}
header .title{font-size:15px;font-weight:600;flex:1;overflow:hidden;text-overflow:ellipsis;white-space:nowrap}
header .sub{font-size:11px;opacity:.75;font-weight:400}
main{flex:1;overflow-y:auto;padding:16px;max-width:720px;width:100%;margin:0 auto}
.msg{display:flex;gap:8px;margin-bottom:12px;align-items:flex-end}
.msg.mine{flex-direction:row-reverse}
.msg .avatar{width:32px;height:32px;border-radius:50%;background:#003366;color:#fff;display:flex;align-items:center;justify-content:center;font-size:12px;font-weight:700;flex:0 0 auto;overflow:hidden}
.msg .avatar img{width:100%;height:100%;object-fit:cover}
.msg .bubble{max-width:75%;padding:10px 14px;border-radius:14px;background:#fff;box-shadow:0 1px 2px rgba(0,0,0,.06);word-wrap:break-word}
.msg.mine .bubble{background:#003366;color:#fff}
.msg .bubble .meta{font-size:10px;opacity:.6;margin-top:4px;display:block}
.msg .bubble .sender{font-size:11px;font-weight:600;margin-bottom:4px;display:block;color:#003366}
.msg.mine .bubble .sender{color:rgba(255,255,255,.85)}
.day-sep{text-align:center;font-size:11px;color:#586675;margin:16px 0;font-weight:600}
.state{padding:60px 20px;text-align:center;color:#586675;flex:1;display:flex;flex-direction:column;justify-content:center;align-items:center}
.state h3{color:#003366;font-size:18px;margin:0 0 8px}
.state p{margin:0 0 16px}
.composer{background:#fff;border-top:1px solid #eef1f4;padding:12px 16px;display:flex;gap:8px;align-items:flex-end;max-width:720px;width:100%;margin:0 auto;flex:0 0 auto;position:sticky;bottom:0;box-shadow:0 -1px 3px rgba(0,0,0,.04)}
.composer textarea{flex:1;padding:10px 12px;border:1px solid #d0d7de;border-radius:20px;font-family:inherit;font-size:15px;resize:none;max-height:120px;min-height:42px;line-height:1.4;background:#f6f8fa}
.composer textarea:focus{outline:none;border-color:#003366;background:#fff;box-shadow:0 0 0 3px rgba(0,51,102,.18)}
.composer button{background:#003366;color:#fff;border:none;width:42px;height:42px;border-radius:50%;cursor:pointer;font-size:18px;display:flex;align-items:center;justify-content:center;flex:0 0 auto;transition:background .15s}
.composer button:hover:not(:disabled){background:#002a52}
.composer button:disabled{opacity:.5;cursor:not-allowed}
.btn{padding:12px 20px;border-radius:8px;font-size:15px;font-weight:600;cursor:pointer;border:none;font-family:inherit;text-decoration:none;text-align:center;display:inline-block;background:#003366;color:#fff;transition:background .15s}
.btn:hover:not(:disabled){background:#002a52}
.btn.sec{background:#eef1f4;color:#192431}
.btn.sec:hover{background:#e0e4e8}
.skeleton-msg{display:flex;gap:8px;margin-bottom:12px}
.sk-bubble{height:44px;background:#eef1f4;border-radius:14px;width:60%}
.sk-bubble.right{margin-left:auto}
.spinner{display:inline-block;width:14px;height:14px;border:2px solid rgba(255,255,255,.4);border-top-color:#fff;border-radius:50%;animation:spin .8s linear infinite}
@keyframes spin{to{transform:rotate(360deg)}}
.offline-banner{background:#fff8e1;border:1px solid #ffe082;color:#8a6d00;padding:10px 14px;font-size:13px;display:none;text-align:center;flex:0 0 auto}
.offline-banner.on{display:block}
.toast{position:fixed;bottom:120px;left:50%;transform:translateX(-50%);background:#192431;color:#fff;padding:12px 20px;border-radius:8px;font-size:14px;z-index:40;opacity:0;pointer-events:none;transition:opacity .2s;max-width:90vw}
.toast.on{opacity:1}

:focus-visible{outline:3px solid #1b5e20;outline-offset:2px;border-radius:6px}
#page-title:focus{outline:none}
.sr-only{position:absolute;width:1px;height:1px;padding:0;margin:-1px;overflow:hidden;clip:rect(0,0,0,0);white-space:nowrap;border:0}
</style>
</head>
<body>
<header>
<a href="#" id="back-btn" title="{{ __('back') }}">&#8592;</a>
<div style="flex:1;min-width:0">
<div class="title" id="page-title" tabindex="-1" role="heading" aria-level="1">{{ __('messagesTitle') }}</div>
<div class="sub" id="page-sub"></div>
</div>
</header>

<div class="offline-banner" id="offline-banner">{{ __('offlineBody') }}</div>

<div id="state-loading" style="flex:1;padding:16px;max-width:720px;margin:0 auto;width:100%">
<div class="skeleton-msg"><div class="sk-bubble"></div></div>
<div class="skeleton-msg"><div class="sk-bubble right"></div></div>
<div class="skeleton-msg"><div class="sk-bubble"></div></div>
</div>

<div id="state-denied" class="state" hidden>
<h3>{{ __('accessDenied') }}</h3>
<p>{{ __('accessDeniedMessages') }}</p>
<a href="/browse" class="btn sec">{{ __('backToBrowse') }}</a>
</div>

<div id="state-error" class="state" hidden>
<h3>{{ __('loadErrorTitle') }}</h3>
<p>{{ __('loadErrorBody') }}</p>
<button type="button" class="btn" onclick="loadMessages()">{{ __('retry') }}</button>
</div>

<main id="state-list" hidden role="main" aria-labelledby="page-title">
<div id="messages-container"></div>
<div id="empty-hint" class="state" hidden style="padding:40px 20px">
<p style="color:#586675">{{ __('noMessagesYet') }}</p>
</div>
</main>

<div class="composer" id="composer" hidden>
<textarea id="content" placeholder="{{ __('typeMessage') }}" maxlength="5000" rows="1"></textarea>
<button type="button" id="send-btn" title="{{ __('send') }}">&#10148;</button>
</div>

<div class="toast" id="toast"></div>

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
function getToken(){return 'session'; /* L305d */}
function esc(s){return String(s==null?'':s).replace(/[&<>"']/g,function(c){return {'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c];});}

function getOfferId(){
  var parts=window.location.pathname.split('/').filter(Boolean);
  // /offers/{id}/messages
  return parts.length>=2?parts[1]:'';
}

function toast(msg,ms){ms=ms||2200;var el=$('toast');el.textContent=msg;el.className='toast on';setTimeout(function(){el.className='toast';},ms);}

function showOnly(name){
  ['state-loading','state-denied','state-error','state-list'].forEach(function(id){
    var el=$(id); if(el)el.hidden=(id!==((name==='content')?'content':'state-'+name));
  });
  var composer=$('composer');
  if(composer)composer.hidden=(name!=='list');
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
  if(!messages.length){
    wrap.innerHTML='';
    empty.hidden=false;
    return;
  }
  empty.hidden=true;

  var html='';
  var lastDay='';
  messages.forEach(function(m){
    var day=fmtDay(m.created_at);
    if(day&&day!==lastDay){
      html+='<div class="day-sep">'+esc(day)+'</div>';
      lastDay=day;
    }
    var mine=m.sender_id===meId;
    var sender=m.sender||{};
    var initial=String(sender.full_name||'?').trim().charAt(0).toUpperCase();
    var avatar=sender.profile_photo_url
      ? '<img src="'+esc(sender.profile_photo_url)+'" alt="">'
      : esc(initial);
    var senderName=!mine&&sender.full_name?('<span class="sender">'+esc(sender.full_name)+'</span>'):'';
    html+='<div class="msg '+(mine?'mine':'')+'">'+
      '<div class="avatar">'+avatar+'</div>'+
      '<div class="bubble">'+senderName+esc(m.content||'')+
        '<span class="meta">'+esc(fmtTime(m.created_at))+'</span>'+
      '</div>'+
    '</div>';
  });
  wrap.innerHTML=html;

  // Scroll to bottom
  var main=document.querySelector('main');
  if(main)main.scrollTop=main.scrollHeight;
}

function loadMessages(silent){
  if(!silent)showOnly('loading');
  var token=getToken();
  /* L305d */

  var h={'Accept':'application/json'}/* L305d */;
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
      // API returns DESC; reverse to ASC
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
  var token=getToken();
  if(!token)return;
  fetch('/api/v1/offers/'+encodeURIComponent(offerId),{
    credentials:'same-origin',headers:{'Accept':'application/json'}
  })
  .then(function(r){return r.ok?r.json():null;})
  .then(function(j){
    var off=(j&&j.data)?j.data:j;
    if(!off)return;
    var need=off.need||{};
    $('page-title').textContent=need.title||'{{ __('messagesTitle') }}';
    $('page-sub').textContent=off.currency+' '+off.offered_price;
    $('back-btn').href='/offers/'+encodeURIComponent(offerId);
  })
  .catch(function(){});
}

function sendMessage(){
  var content=$('content').value.trim();
  if(!content){toast('{{ __('emptyMessage') }}');return;}
  if(content.length>5000){toast('{{ __('messageTooLong') }}');return;}

  var token=getToken();
  /* L305d */

  var btn=$('send-btn');
  btn.disabled=true;
  btn.innerHTML='<span class="spinner"></span>';

  fetch('/api/v1/offers/'+encodeURIComponent(offerId)+'/messages',{
    method:'POST',
    headers:{
      'Accept':'application/json',
      'Content-Type':'application/json',
      'X-CSRF-TOKEN':csrf
    },
    body:JSON.stringify({content:content})
  })
  .then(function(r){return r.json().then(function(j){return {s:r.status,b:j};});})
  .then(function(res){
    if(res.s===201||res.s===200){
      $('content').value='';
      try{localStorage.removeItem(DRAFT_KEY);}catch(e){}
      loadMessages(true);
    }else if(res.s===422){
      toast('{{ __('validationFailed') }}');
    }else if(res.s===403){
      toast('{{ __('notAllowed') }}');
    }else if(res.s===429){
      toast('{{ __('rateLimited') }}');
    }else{
      toast('{{ __('sendFailed') }}');
    }
  })
  .catch(function(){toast('{{ __('sendFailed') }}');})
  .then(function(){
    btn.disabled=false;
    btn.innerHTML='&#10148;';
  });
}

// Draft auto-save
function saveDraft(){
  try{localStorage.setItem(DRAFT_KEY,$('content').value);}catch(e){}
}
function loadDraft(){
  try{
    var v=localStorage.getItem(DRAFT_KEY);
    if(v)$('content').value=v;
  }catch(e){}
}

// Auto-grow textarea
function autoGrow(){
  var el=$('content');
  el.style.height='auto';
  el.style.height=Math.min(el.scrollHeight,120)+'px';
}

// Boot
offerId=getOfferId();
if(!offerId){showOnly('error');return;}
DRAFT_KEY='felagi_draft_msg_'+offerId;

// L332 — fetch current user from server (cookie session); localStorage 'felagi_user' is dead
fetch('/api/v1/auth/me', {credentials:'same-origin', headers:{'Accept':'application/json'}})
  .then(function(r){return r.ok?r.json():{data:null};})
  .then(function(me){
    if(me && me.data && me.data.id) meId = me.data.id;
  })
  .catch(function(){})
  .then(function(){
    loadOfferMeta();
    loadMessages();
    loadDraft();
  });

$('send-btn').addEventListener('click',sendMessage);
$('content').addEventListener('input',function(){saveDraft();autoGrow();});
$('content').addEventListener('keydown',function(e){
  if(e.key==='Enter'&&!e.shiftKey){
    e.preventDefault();
    sendMessage();
  }
});

// Polling for new messages every 15s
pollTimer=setInterval(function(){if(!document.hidden)loadMessages(true);},15000);

window.addEventListener('offline',function(){$('offline-banner').className='offline-banner on';});
window.addEventListener('online',function(){$('offline-banner').className='offline-banner';loadMessages(true);});

if(navigator.onLine===false){$('offline-banner').className='offline-banner on';}

window.loadMessages=loadMessages;
})();
</script>
</body>
</html>
