<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>{{ __('screenS012') }} — {{ __('brand') }}</title>
<style>
*{box-sizing:border-box;margin:0;padding:0}
body{font-family:system-ui,-apple-system,sans-serif;background:#F4F6F8;color:#192431;line-height:1.5;min-height:100vh;padding-bottom:80px}
header{background:#003366;color:#fff;height:64px;padding:0 16px;display:flex;align-items:center;gap:12px;position:sticky;top:0;z-index:30}
header a{color:#fff;text-decoration:none;font-size:20px;padding:8px;border-radius:6px;line-height:1}
header a:hover{background:rgba(255,255,255,.12)}
header .title{font-size:16px;font-weight:600;flex:1;overflow:hidden;text-overflow:ellipsis;white-space:nowrap}
main{max-width:720px;margin:0 auto;padding:16px}
.card{background:#fff;border-radius:12px;padding:20px;box-shadow:0 1px 3px rgba(0,0,0,.08);margin-bottom:16px}
.card h3{font-size:15px;color:#003366;margin:0 0 12px;font-weight:600}
.status-banner{padding:14px 16px;border-radius:10px;font-size:14px;font-weight:600;margin-bottom:16px;display:flex;align-items:center;gap:10px}
.status-banner.pending{background:#fff3e0;color:#e65100}
.status-banner.accepted{background:#e8f5e9;color:#1b5e20}
.status-banner.rejected{background:#f5f5f5;color:#616161}
.status-banner.withdrawn{background:#f5f5f5;color:#616161}
.status-banner .icon{font-size:20px}
.offer-price{font-size:28px;font-weight:700;color:#1b5e20;margin-bottom:4px}
.offer-price .cur{font-size:18px;font-weight:600;margin-right:4px}
.offer-price-sub{font-size:13px;color:#586675;margin-bottom:16px}
.msg-block{font-size:15px;color:#3a4a5a;line-height:1.6;white-space:pre-wrap;background:#f6f8fa;padding:14px;border-radius:8px;margin-bottom:12px}
.detail-row{display:flex;justify-content:space-between;gap:12px;padding:10px 0;border-bottom:1px solid #eef1f4;font-size:14px}
.detail-row:last-child{border-bottom:none}
.detail-row .lbl{color:#586675;flex:0 0 auto}
.detail-row .val{color:#192431;font-weight:600;text-align:right;word-break:break-word}
.provider{display:flex;align-items:center;gap:12px;padding:12px;background:#f6f8fa;border-radius:8px}
.avatar{width:48px;height:48px;border-radius:50%;background:#003366;color:#fff;display:flex;align-items:center;justify-content:center;font-size:18px;font-weight:700;flex:0 0 auto;overflow:hidden}
.avatar img{width:100%;height:100%;object-fit:cover}
.provider .name{font-weight:600;font-size:15px}
.provider .meta{font-size:12px;color:#586675}
.rating{color:#f5a623;font-weight:600}
.need-link{display:block;padding:14px;background:#f6f8fa;border-radius:8px;text-decoration:none;color:inherit;transition:background .15s}
.need-link:hover{background:#eef1f4}
.need-link .lbl{font-size:11px;color:#586675;text-transform:uppercase;letter-spacing:.5px;font-weight:600;margin-bottom:4px}
.need-link .title{font-size:15px;font-weight:600;color:#192431}
.need-link .meta{font-size:12px;color:#586675;margin-top:2px}
.actions{display:flex;flex-direction:column;gap:10px}
.btn{padding:14px 20px;border-radius:10px;font-size:15px;font-weight:600;cursor:pointer;border:none;font-family:inherit;text-decoration:none;text-align:center;display:block;transition:background .15s}
.btn.primary{background:#003366;color:#fff}
.btn.primary:hover:not(:disabled){background:#002a52}
.btn.success{background:#1b5e20;color:#fff}
.btn.success:hover:not(:disabled){background:#154a19}
.btn.danger{background:#fff;color:#c62828;border:1px solid #ffcdd2}
.btn.danger:hover:not(:disabled){background:#ffebee}
.btn.ghost{background:#eef1f4;color:#192431}
.btn.ghost:hover:not(:disabled){background:#e0e4e8}
.btn:disabled{opacity:.5;cursor:not-allowed}
.btn-row{display:flex;gap:10px;flex-wrap:wrap}
.btn-row .btn{flex:1;min-width:140px}
.state{padding:60px 20px;text-align:center;color:#586675}
.state h3{color:#003366;font-size:18px;margin:0 0 8px}
.state p{margin:0 0 16px}
.state .btn{display:inline-block;max-width:220px;margin:0 auto}
.spinner{display:inline-block;width:20px;height:20px;border:3px solid #e0e0e0;border-top-color:#003366;border-radius:50%;animation:spin .8s linear infinite}
@keyframes spin{to{transform:rotate(360deg)}}
.offline-banner{background:#fff8e1;border:1px solid #ffe082;color:#8a6d00;padding:10px 14px;border-radius:8px;font-size:13px;margin-bottom:16px;display:none}
.offline-banner.on{display:block}
.toast{position:fixed;bottom:100px;left:50%;transform:translateX(-50%);background:#192431;color:#fff;padding:12px 20px;border-radius:8px;font-size:14px;z-index:40;opacity:0;pointer-events:none;transition:opacity .2s;max-width:90vw}
.toast.on{opacity:1}
.modal-backdrop{position:fixed;inset:0;background:rgba(0,0,0,.5);z-index:50;display:none;align-items:center;justify-content:center;padding:20px}
.modal-backdrop.on{display:flex}
.modal{background:#fff;border-radius:12px;padding:24px;max-width:420px;width:100%}
.modal h3{font-size:18px;color:#003366;margin:0 0 12px}
.modal p{font-size:14px;color:#586675;margin-bottom:20px;line-height:1.5}
.modal .actions{flex-direction:row;justify-content:flex-end;gap:8px}
.modal .btn{flex:0 0 auto;padding:10px 18px}

:focus-visible{outline:3px solid #1b5e20;outline-offset:2px;border-radius:6px}
#page-title:focus{outline:none}
.sr-only{position:absolute;width:1px;height:1px;padding:0;margin:-1px;overflow:hidden;clip:rect(0,0,0,0);white-space:nowrap;border:0}
</style>
@include('partials.felagi-polish')
</head>
<body>
<header>
<a href="#" id="back-btn" title="{{ __('back') }}">&#8592;</a>
<span class="title">{{ __('offerDetailsTitle') }}</span>
</header>

<main role="main" aria-labelledby="page-title">
<h1 id="page-title" tabindex="-1" class="sr-only">{{ __('screenS012') }}</h1>
<div class="offline-banner" id="offline-banner">{{ __('offlineBody') }}</div>

<div id="state-loading" class="state">
<div class="spinner"></div>
<p style="margin-top:12px">{{ __('loading') }}...</p>
</div>

<div id="state-denied" class="state" hidden>
<h3>{{ __('accessDenied') }}</h3>
<p>{{ __('accessDeniedOffer') }}</p>
<a href="/browse" class="btn primary">{{ __('backToBrowse') }}</a>
</div>

<div id="state-error" class="state" hidden>
<h3>{{ __('loadErrorTitle') }}</h3>
<p>{{ __('loadErrorBody') }}</p>
<button type="button" class="btn primary" onclick="loadOffer()">{{ __('retry') }}</button>
</div>

<div id="content" hidden>

<div class="status-banner" id="status-banner">
<span class="icon" id="status-icon"></span>
<span id="status-text"></span>
</div>

<div class="card">
<div class="offer-price"><span class="cur" id="offer-cur">ETB</span><span id="offer-price">0</span></div>
<div class="offer-price-sub">{{ __('offeredPriceLabel') }}</div>

<h3>{{ __('proposalMessage') }}</h3>
<div class="msg-block" id="offer-msg"></div>
</div>

<div class="card">
<h3>{{ __('offerDetailsTitle') }}</h3>
<div id="offer-meta"></div>
</div>

<div class="card" id="provider-card" hidden>
<h3 id="provider-heading">{{ __('provider') }}</h3>
<div class="provider">
<div class="avatar" id="provider-avatar"></div>
<div>
<div class="name" id="provider-name"></div>
<div class="meta" id="provider-meta"></div>
</div>
</div>
</div>

<div class="card" id="need-card" hidden>
<h3>{{ __('relatedNeed') }}</h3>
<a href="#" class="need-link" id="need-link">
<div class="lbl">{{ __('needDetails') }}</div>
<div class="title" id="need-title"></div>
<div class="meta" id="need-meta"></div>
</a>
</div>

<div class="card" id="actions-card" hidden>
<h3>{{ __('actions') }}</h3>
<div class="actions">
<a href="#" id="btn-messages" class="btn ghost" hidden>{{ __('openMessages') }}</a>

<div class="btn-row" id="btn-row-owner" hidden>
<button type="button" class="btn success" id="btn-accept">{{ __('acceptOffer') }}</button>
<button type="button" class="btn danger" id="btn-reject">{{ __('rejectOffer') }}</button>
</div>

<button type="button" class="btn danger" id="btn-withdraw" hidden>{{ __('withdrawOffer') }}</button>
</div>
</div>

</div>
</main>

<div class="modal-backdrop" id="modal-backdrop">
<div class="modal">
<h3 id="modal-title"></h3>
<p id="modal-body"></p>
<div class="actions">
<button type="button" class="btn ghost" id="modal-cancel">{{ __('cancel') }}</button>
<button type="button" class="btn primary" id="modal-confirm">{{ __('confirm') }}</button>
</div>
</div>
</div>

<div class="toast" id="toast"></div>
<script>
(function(){
'use strict';
var csrf=(document.querySelector('meta[name="csrf-token"]')||{}).content||'';
var LS_TOKEN='felagi_token';
var offerId='';
var offer=null;
var need=null;
var isProvider=false;
var isOwner=false;
var pendingAction=null;

function $(id){return document.getElementById(id);}
function getToken(){return 'session'; /* L305d */}
function esc(s){return String(s==null?'':s).replace(/[&<>"']/g,function(c){return {'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c];});}

function getOfferId(){
  var parts=window.location.pathname.split('/').filter(Boolean);
  // /offers/{id}
  return parts.length>=2?parts[1]:'';
}

function toast(msg,ms){
  ms=ms||2500;
  var el=$('toast');
  el.textContent=msg;
  el.className='toast on';
  setTimeout(function(){el.className='toast';},ms);
}

function showOnly(name){
  ['state-loading','state-denied','state-error','content'].forEach(function(id){
    var el=$(id); if(el)el.hidden=(id!==((name==='content')?'content':'state-'+name));
  });
}

function fmtPrice(){
  if(!offer)return;
  var cur=offer.currency||'ETB';
  var p=offer.offered_price!=null?Number(offer.offered_price).toFixed(2):'-';
  $('offer-cur').textContent=cur;
  $('offer-price').textContent=p;
}

function fmtDate(iso){
  if(!iso)return '';
  var d=new Date(iso);
  if(isNaN(d))return '';
  return d.toLocaleDateString()+' '+d.toLocaleTimeString([],{hour:'2-digit',minute:'2-digit'});
}

function statusInfo(s){
  return ({
    'PENDING':{cls:'pending',icon:'&#9203;',text:'{{ __('offerStatusPending') }}'},
    'ACCEPTED':{cls:'accepted',icon:'&#10003;',text:'{{ __('offerStatusAccepted') }}'},
    'REJECTED':{cls:'rejected',icon:'&#10007;',text:'{{ __('offerStatusRejected') }}'},
    'WITHDRAWN':{cls:'withdrawn',icon:'&#8634;',text:'{{ __('offerStatusWithdrawn') }}'}
  })[s]||{cls:'pending',icon:'',text:s||''};
}

function renderStatusBanner(){
  if(!offer)return;
  var info=statusInfo(offer.status);
  var banner=$('status-banner');
  banner.className='status-banner '+info.cls;
  $('status-icon').innerHTML=info.icon;
  $('status-text').textContent=info.text;
}

function renderOfferDetails(){
  if(!offer)return;
  fmtPrice();
  $('offer-msg').textContent=offer.proposal_message||'';

  var rows=[];
  if(offer.delivery_time_text)rows.push(['{{ __('deliveryTime') }}', offer.delivery_time_text]);
  if(offer.availability_text)rows.push(['{{ __('availability') }}', offer.availability_text]);
  if(offer.additional_notes)rows.push(['{{ __('additionalNotes') }}', offer.additional_notes]);
  if(offer.created_at)rows.push(['{{ __('postedLabel') }}', fmtDate(offer.created_at)]);
  if(offer.accepted_at)rows.push(['{{ __('acceptedAt') }}', fmtDate(offer.accepted_at)]);
  if(offer.withdrawn_at)rows.push(['{{ __('withdrawnAt') }}', fmtDate(offer.withdrawn_at)]);

  $('offer-meta').innerHTML=rows.length
    ? rows.map(function(r){
        return '<div class="detail-row"><span class="lbl">'+esc(r[0])+'</span><span class="val">'+esc(r[1])+'</span></div>';
      }).join('')
    : '<div class="detail-row"><span class="lbl">{{ __('noAdditionalDetails') }}</span></div>';
}

function renderProvider(){
  var prov=offer&&offer.provider?offer.provider:null;
  if(!prov||!prov.id){$('provider-card').hidden=true;return;}
  $('provider-card').hidden=false;
  var initial=String(prov.full_name||'?').trim().charAt(0).toUpperCase();
  $('provider-avatar').innerHTML=prov.profile_photo_url
    ? '<img src="'+esc(prov.profile_photo_url)+'" alt="">'
    : esc(initial);
  $('provider-name').textContent=prov.full_name||'{{ __('anonymous') }}';
  var meta=[];
  if(prov.rating_score)meta.push('<span class="rating">&#9733; '+Number(prov.rating_score).toFixed(1)+'</span>');
  if(prov.rating_count)meta.push('('+prov.rating_count+')');
  $('provider-meta').innerHTML=meta.join(' ')||'';
}

function renderNeed(){
  need=offer&&offer.need?offer.need:null;
  if(!need||!need.id){$('need-card').hidden=true;return;}
  $('need-card').hidden=false;
  $('need-title').textContent=need.title||'';
  var meta=[];
  if(need.status)meta.push(need.status);
  if(need.location_text)meta.push('&#128205; '+esc(need.location_text));
  $('need-meta').innerHTML=meta.join(' · ');
  $('need-link').href='/needs/'+encodeURIComponent(need.id);
}

function renderActions(){
  if(!offer)return;
  var card=$('actions-card');
  var hasAny=false;

  // Messages link (S017) — participants only
  var msg=$('btn-messages');
  if(isProvider||isOwner){
    msg.hidden=false;
    msg.href='/offers/'+encodeURIComponent(offer.id)+'/messages';
    hasAny=true;
  }else{
    msg.hidden=true;
  }

  // Owner actions (accept/reject) — PENDING + need OPEN
  var ownerRow=$('btn-row-owner');
  if(isOwner&&offer.status==='PENDING'&&need&&need.status==='OPEN'){
    ownerRow.hidden=false;
    hasAny=true;
  }else{
    ownerRow.hidden=true;
  }

  // Provider actions (withdraw) — PENDING
  var wd=$('btn-withdraw');
  if(isProvider&&offer.status==='PENDING'){
    wd.hidden=false;
    hasAny=true;
  }else{
    wd.hidden=true;
  }

  card.hidden=!hasAny;
}

function renderOffer(){
  if(!offer)return;
  document.title=(need&&need.title?need.title:'{{ __('offerDetailsTitle') }}')+' — {{ __('brand') }}';
  renderStatusBanner();
  renderOfferDetails();
  renderProvider();
  renderNeed();
  renderActions();
  showOnly('content');
}

function loadOffer(){
  offerId=getOfferId();
  if(!offerId){showOnly('error');return;}

  var token=getToken();
  /* L305d */

  showOnly('loading');

  var h={'Accept':'application/json'}/* L305d */;
  fetch('/api/v1/offers/'+encodeURIComponent(offerId),{headers:h})
    .then(function(r){
      if(r.status===404)throw new Error('notfound');
      if(r.status===401)throw new Error('auth');
      if(r.status===403)throw new Error('denied');
      if(r.status===429)throw new Error('rate-limited');
      if(!r.ok)throw new Error('HTTP '+r.status);
      return r.json();
    })
    .then(function(j){
      offer=(j&&j.data)?j.data:j;
      if(!offer||!offer.id){showOnly('error');return;}

      // L332 — fetch current user from server (cookie session); localStorage 'felagi_user' is dead
      fetch('/api/v1/auth/me', {credentials:'same-origin', headers:{'Accept':'application/json'}})
        .then(function(r){return r.ok?r.json():{data:null};})
        .then(function(me){
          var uid = (me && me.data && me.data.id) ? me.data.id : null;

          isProvider=!!(uid&&offer.provider_id===uid);
          var ownerId=offer.need&&offer.need.requester_id?offer.need.requester_id:null;
          isOwner=!!(uid&&ownerId===uid);

          if(!isProvider&&!isOwner){showOnly('denied');return;}

          $('back-btn').href=ownerId?('/needs/'+encodeURIComponent(offer.need_id||offer.need.id)):'/my/offers';

          renderOffer();
        })
        .catch(function(){showOnly('error');});
    })
    .catch(function(e){
      var m=String(e.message||e);
      if(m==='auth'){localStorage.removeItem(LS_TOKEN);window.location.href='/';return;}
      if(m==='denied'){showOnly('denied');return;}
      showOnly('error');
    });
}

function openModal(title,body,action){
  pendingAction=action;
  $('modal-title').textContent=title;
  $('modal-body').textContent=body;
  $('modal-backdrop').className='modal-backdrop on';
}

function closeModal(){
  pendingAction=null;
  $('modal-backdrop').className='modal-backdrop';
}

function callAction(endpoint,onSuccess){
  var token=getToken();
  /* L305d */
  var btn=null;
  if(endpoint.indexOf('accept')!==-1)btn=$('btn-accept');
  else if(endpoint.indexOf('reject')!==-1)btn=$('btn-reject');
  else if(endpoint.indexOf('withdraw')!==-1)btn=$('btn-withdraw');
  if(btn)btn.disabled=true;

  fetch('/api/v1/offers/'+encodeURIComponent(offerId)+endpoint,{
    method:'POST',
    headers:{
      'Accept':'application/json',
      'X-CSRF-TOKEN':csrf
    }
  })
  .then(function(r){return r.json().then(function(j){return {s:r.status,b:j};});})
  .then(function(res){
    if(res.s===200){
      toast(onSuccess,2200);
      setTimeout(loadOffer,700);
    }else if(res.s===409){
      toast('{{ __('stateConflict') }}',2600);
      setTimeout(loadOffer,1000);
    }else if(res.s===403){
      toast('{{ __('notAllowed') }}',2600);
    }else if(res.s===404){
      toast('{{ __('offerNotFound') }}',2600);
    }else{
      toast('{{ __('actionFailed') }}',2600);
    }
  })
  .catch(function(){
    toast('{{ __('actionFailed') }}',2600);
  })
  .then(function(){
    if(btn)btn.disabled=false;
  });
}

// Buttons
$('btn-accept').addEventListener('click',function(){
  openModal(
    '{{ __('acceptOffer') }}',
    '{{ __('confirmAccept') }}',
    function(){callAction('/accept','{{ __('offerAccepted') }}');}
  );
});
$('btn-reject').addEventListener('click',function(){
  openModal(
    '{{ __('rejectOffer') }}',
    '{{ __('confirmReject') }}',
    function(){callAction('/reject','{{ __('offerRejected') }}');}
  );
});
$('btn-withdraw').addEventListener('click',function(){
  openModal(
    '{{ __('withdrawOffer') }}',
    '{{ __('confirmWithdraw') }}',
    function(){callAction('/withdraw','{{ __('offerWithdrawn') }}');}
  );
});

$('modal-cancel').addEventListener('click',closeModal);
$('modal-confirm').addEventListener('click',function(){
  var a=pendingAction;
  closeModal();
  if(a)a();
});
$('modal-backdrop').addEventListener('click',function(e){
  if(e.target===$('modal-backdrop'))closeModal();
});

// Offline / online
window.addEventListener('offline',function(){$('offline-banner').className='offline-banner on';});
window.addEventListener('online',function(){$('offline-banner').className='offline-banner';loadOffer();});
if(navigator.onLine===false){$('offline-banner').className='offline-banner on';}

window.loadOffer=loadOffer;
loadOffer();
})();
</script>
</body>
</html>
