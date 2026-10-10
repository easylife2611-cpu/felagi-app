<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>{{ __('screenS012') }} — {{ __('brand') }}</title>
<meta name="theme-color" content="#003366">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Ethiopic:wght@400;500;600;700;800;900&family=Inter:wght@400;600;700;800;900&display=swap" rel="stylesheet">
<style>
:root{--navy-950:#001a33;--navy-900:#002b52;--navy-800:#003366;--navy-700:#0b4d83;--navy-600:#1a6bb0;--orange-700:#b35c00;--orange-600:#e8851d;--orange-500:#FF9933;--orange-400:#ffb366;--orange-100:#fff2e0;--ink-900:#0d1a2b;--ink-500:#3c4d61;--ink-300:#5f7185;--ink-200:#8fa0b3;--line-100:#eef2f6;--line-200:#e3e9ef;--line-300:#d5dde6;--surface:#fff;--canvas:#f4f7fa;--canvas-2:#eef3f8;--success-600:#0e6b34;--success-500:#147a3d;--success-100:#e8f5ee;--warn-600:#a86500;--warn-100:#fff7e6;--danger-600:#a32e21;--danger-500:#c0392b;--danger-100:#fdecea;--info-600:#0056d6;--info-100:#e7f1ff;--r-xs:8px;--r-sm:12px;--r-md:16px;--r-lg:22px;--r-xl:28px;--r-pill:999px;--sh-xs:0 1px 2px rgba(0,26,51,.04),0 1px 3px rgba(0,26,51,.06);--sh-sm:0 2px 6px rgba(0,26,51,.05),0 4px 12px rgba(0,26,51,.06);--sh-md:0 4px 12px rgba(0,26,51,.06),0 12px 28px rgba(0,26,51,.08);--sh-navy:0 12px 32px rgba(0,51,102,.28);--sh-orange:0 8px 20px rgba(255,153,51,.32);--f-am:'Noto Sans Ethiopic','Inter',system-ui,sans-serif;--f-en:'Inter','Noto Sans Ethiopic',system-ui,sans-serif;--ease:cubic-bezier(.16,.84,.44,1);--ease-out:cubic-bezier(.22,1,.36,1)}
*{box-sizing:border-box;margin:0;padding:0}
html,body{background:var(--canvas);color:var(--ink-900);font-family:var(--f-am);line-height:1.55;-webkit-font-smoothing:antialiased}
body{padding:0 0 80px;min-height:100vh}
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
.status-banner{padding:14px 16px;border-radius:var(--r-md);font-size:14px;font-weight:800;margin-bottom:16px;display:flex;align-items:center;gap:10px}
.status-banner.pending{background:var(--warn-100);color:var(--warn-600)}
.status-banner.accepted{background:var(--success-100);color:var(--success-600)}
.status-banner.rejected,.status-banner.withdrawn{background:var(--line-100);color:var(--ink-500)}
.status-banner .icon{font-size:20px;line-height:1}
.cd{background:var(--surface);border:1px solid var(--line-100);border-radius:var(--r-lg);padding:20px;margin-bottom:14px;box-shadow:var(--sh-xs)}
.cd h3{font-size:12px;font-weight:900;color:var(--navy-800);text-transform:uppercase;letter-spacing:.08em;margin-bottom:12px;font-family:var(--f-en)}
.offer-price{font-size:34px;font-weight:900;color:var(--success-600);letter-spacing:-.03em;font-family:var(--f-en);display:flex;align-items:baseline;gap:6px;line-height:1}
.offer-price .cur{font-size:15px;color:var(--ink-300);font-weight:800;letter-spacing:.06em}
.offer-price-sub{font-size:11.5px;color:var(--ink-300);margin-bottom:14px;margin-top:6px;font-weight:700;text-transform:uppercase;letter-spacing:.04em;font-family:var(--f-en)}
.msg-block{font-size:14.5px;color:var(--ink-700);line-height:1.7;white-space:pre-wrap;background:var(--canvas-2);padding:16px;border-radius:var(--r-md);border:1px solid var(--line-200);font-weight:500}
.detail-row{display:flex;justify-content:space-between;gap:12px;padding:11px 0;border-bottom:1px solid var(--line-100);font-size:13.5px}
.detail-row:last-child{border-bottom:none}
.detail-row .lbl{color:var(--ink-300);flex:0 0 auto;font-weight:700;font-size:12px;text-transform:uppercase;letter-spacing:.05em;font-family:var(--f-en)}
.detail-row .val{color:var(--ink-900);font-weight:800;text-align:right;word-break:break-word}
.provider{display:flex;align-items:center;gap:14px;padding:14px;background:var(--canvas-2);border-radius:var(--r-md);border:1px solid var(--line-200)}
.av{width:56px;height:56px;border-radius:50%;background:linear-gradient(135deg,var(--navy-800),var(--navy-600));color:#fff;display:flex;align-items:center;justify-content:center;font-weight:900;font-size:20px;flex:0 0 auto;overflow:hidden;box-shadow:0 2px 8px rgba(0,51,102,.16)}
.av img{width:100%;height:100%;object-fit:cover}
.provider .name{font-weight:900;font-size:15px;color:var(--ink-900);letter-spacing:-.01em}
.provider .meta{font-size:12px;color:var(--ink-300);font-weight:700;margin-top:3px;font-family:var(--f-en)}
.rating{color:var(--orange-600);font-weight:800}
.need-link{display:block;padding:16px;background:var(--canvas-2);border-radius:var(--r-md);border:1px solid var(--line-200);transition:all .2s var(--ease)}
.need-link:hover{background:var(--surface);border-color:var(--navy-700);transform:translateY(-1px);box-shadow:var(--sh-sm)}
.need-link .lbl{font-size:10.5px;color:var(--ink-300);text-transform:uppercase;letter-spacing:.08em;font-weight:800;margin-bottom:5px;font-family:var(--f-en)}
.need-link .title{font-size:15px;font-weight:900;color:var(--ink-900);letter-spacing:-.02em}
.need-link .meta{font-size:11.5px;color:var(--ink-300);margin-top:5px;font-weight:600}
.actions{display:flex;flex-direction:column;gap:10px}
.btn{display:inline-flex;align-items:center;justify-content:center;gap:9px;height:52px;padding:0 24px;border-radius:var(--r-sm);font-weight:900;font-size:14.5px;line-height:1;letter-spacing:-.015em;transition:all .2s var(--ease);font-family:var(--f-am);text-align:center}
.btn:active{transform:scale(.975)}
.btn-primary{background:linear-gradient(135deg,var(--navy-800),var(--navy-700));color:#fff;box-shadow:var(--sh-navy)}
.btn-primary:hover:not(:disabled){transform:translateY(-1px)}
.btn-success{background:linear-gradient(135deg,var(--success-500),var(--success-600));color:#fff}
.btn-success:hover:not(:disabled){transform:translateY(-1px)}
.btn-danger{background:#fff;color:var(--danger-600);border:1.5px solid rgba(192,57,43,.25)}
.btn-danger:hover:not(:disabled){background:var(--danger-100)}
.btn-ghost{background:var(--surface);color:var(--navy-800);border:1.5px solid var(--line-300)}
.btn-ghost:hover:not(:disabled){background:var(--canvas-2);border-color:var(--navy-700)}
.btn:disabled{opacity:.5;cursor:not-allowed}
.btn-row{display:flex;gap:10px;flex-wrap:wrap}
.btn-row .btn{flex:1;min-width:140px}
.state{padding:60px 24px;text-align:center;color:var(--ink-300)}
.state .ic{font-size:44px;margin-bottom:14px;opacity:.5}
.state h3{font-size:18px;font-weight:900;color:var(--navy-800);margin-bottom:8px}
.state p{font-size:13.5px;line-height:1.6;color:var(--ink-500);max-width:300px;margin:0 auto 16px}
.spinner{display:inline-block;width:24px;height:24px;border:3px solid var(--line-200);border-top-color:var(--navy-800);border-radius:50%;animation:spin .8s linear infinite}
@keyframes spin{to{transform:rotate(360deg)}}
.offline-banner{display:none;background:var(--warn-100);color:var(--warn-600);padding:10px 16px;border-radius:var(--r-sm);font-size:12.5px;font-weight:800;text-align:center;margin-bottom:14px}
.offline-banner.on{display:block}
.toast{position:fixed;bottom:100px;left:50%;transform:translateX(-50%) translateY(20px);background:var(--navy-900);color:#fff;padding:12px 20px;border-radius:var(--r-pill);font-size:13px;font-weight:700;opacity:0;pointer-events:none;transition:all .3s var(--ease-out);z-index:50;box-shadow:var(--sh-md);max-width:90vw;text-align:center}
.toast.on{opacity:1;transform:translateX(-50%) translateY(0)}
.modal-backdrop{position:fixed;inset:0;background:rgba(0,26,51,.5);z-index:50;display:none;align-items:center;justify-content:center;padding:20px}
.modal-backdrop.on{display:flex}
.modal{background:var(--surface);border-radius:var(--r-lg);padding:24px;max-width:440px;width:100%;box-shadow:var(--sh-md)}
.modal h3{font-size:18px;color:var(--navy-800);margin:0 0 12px;font-weight:900;letter-spacing:-.02em}
.modal p{font-size:14px;color:var(--ink-500);margin-bottom:22px;line-height:1.6}
.modal .actions{flex-direction:row;justify-content:flex-end;gap:8px}
.modal .btn{flex:0 0 auto;padding:12px 22px;height:46px}
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
    <h1 class="t" id="page-title" tabindex="-1" role="heading" aria-level="1">{{ __('offerDetailsTitle') }}</h1>
  </div>
</div>
<div class="sb">
  <div class="offline-banner" id="offline-banner" role="status" aria-live="polite">{{ __('offlineBody') }}</div>

  <div id="state-loading" class="state">
    <div class="spinner"></div>
    <p style="margin-top:12px">{{ __('loading') }}...</p>
  </div>

  <div id="state-denied" class="state" hidden>
    <div class="ic">🔒</div>
    <h3>{{ __('accessDenied') }}</h3>
    <p>{{ __('accessDeniedOffer') }}</p>
    <a href="/browse" class="btn btn-primary">{{ __('backToBrowse') }}</a>
  </div>

  <div id="state-error" class="state" hidden>
    <div class="ic">⚠️</div>
    <h3>{{ __('loadErrorTitle') }}</h3>
    <p>{{ __('loadErrorBody') }}</p>
    <button type="button" class="btn btn-primary" onclick="loadOffer()">{{ __('retry') }}</button>
  </div>

  <div id="content" hidden>
    <div class="status-banner" id="status-banner">
      <span class="icon" id="status-icon"></span>
      <span id="status-text"></span>
    </div>

    <div class="cd">
      <div class="offer-price"><span class="cur" id="offer-cur">ETB</span><span id="offer-price">0</span></div>
      <div class="offer-price-sub">{{ __('offeredPriceLabel') }}</div>
      <h3 style="margin-top:16px">{{ __('proposalMessage') }}</h3>
      <div class="msg-block" id="offer-msg"></div>
    </div>

    <div class="cd">
      <h3>{{ __('offerDetailsTitle') }}</h3>
      <div id="offer-meta"></div>
    </div>

    <div class="cd" id="provider-card" hidden>
      <h3 id="provider-heading">{{ __('provider') }}</h3>
      <div class="provider">
        <div class="av" id="provider-avatar"></div>
        <div>
          <div class="name" id="provider-name"></div>
          <div class="meta" id="provider-meta"></div>
        </div>
      </div>
    </div>

    <div class="cd" id="need-card" hidden>
      <h3>{{ __('relatedNeed') }}</h3>
      <a href="#" class="need-link" id="need-link">
        <div class="lbl">{{ __('needDetails') }}</div>
        <div class="title" id="need-title"></div>
        <div class="meta" id="need-meta"></div>
      </a>
    </div>

    <div class="cd" id="actions-card" hidden>
      <h3>{{ __('actions') }}</h3>
      <div class="actions">
        <a href="#" id="btn-messages" class="btn btn-ghost" hidden>{{ __('openMessages') }}</a>
        <div class="btn-row" id="btn-row-owner" hidden>
          <button type="button" class="btn btn-success" id="btn-accept">{{ __('acceptOffer') }}</button>
          <button type="button" class="btn btn-danger" id="btn-reject">{{ __('rejectOffer') }}</button>
        </div>
        <button type="button" class="btn btn-danger" id="btn-withdraw" hidden>{{ __('withdrawOffer') }}</button>
      </div>
    </div>
  </div>
</div>

<div class="modal-backdrop" id="modal-backdrop">
  <div class="modal" role="dialog" aria-modal="true" aria-labelledby="modal-title">
    <h3 id="modal-title"></h3>
    <p id="modal-body"></p>
    <div class="actions">
      <button type="button" class="btn btn-ghost" id="modal-cancel">{{ __('cancel') }}</button>
      <button type="button" class="btn btn-primary" id="modal-confirm">{{ __('confirm') }}</button>
    </div>
  </div>
</div>

<div class="toast" id="toast" role="status" aria-live="polite"></div>
</div>
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
function getToken(){return 'session';}
function esc(s){return String(s==null?'':s).replace(/[&<>"']/g,function(c){return {'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c];});}
function getOfferId(){var parts=window.location.pathname.split('/').filter(Boolean);return parts.length>=2?parts[1]:'';}
function toast(msg,ms){ms=ms||2500;var el=$('toast');el.textContent=msg;el.className='toast on';setTimeout(function(){el.className='toast';},ms);}
function showOnly(name){['state-loading','state-denied','state-error','content'].forEach(function(id){var el=$(id);if(el)el.hidden=(id!==((name==='content')?'content':'state-'+name));});}
function fmtPrice(){
  if(!offer)return;
  var cur=offer.currency||'ETB';
  var p=offer.offered_price!=null?Number(offer.offered_price).toFixed(2):'-';
  $('offer-cur').textContent=cur;
  $('offer-price').textContent=p;
}
function fmtDate(iso){if(!iso)return '';var d=new Date(iso);if(isNaN(d))return '';return d.toLocaleDateString()+' '+d.toLocaleTimeString([],{hour:'2-digit',minute:'2-digit'});}
function statusInfo(s){return ({'PENDING':{cls:'pending',icon:'⏳',text:'{{ __('offerStatusPending') }}'},'ACCEPTED':{cls:'accepted',icon:'✓',text:'{{ __('offerStatusAccepted') }}'},'REJECTED':{cls:'rejected',icon:'✗',text:'{{ __('offerStatusRejected') }}'},'WITHDRAWN':{cls:'withdrawn',icon:'↺',text:'{{ __('offerStatusWithdrawn') }}'}})[s]||{cls:'pending',icon:'',text:s||''};}
function renderStatusBanner(){
  if(!offer)return;
  var info=statusInfo(offer.status);
  var banner=$('status-banner');
  banner.className='status-banner '+info.cls;
  $('status-icon').textContent=info.icon;
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
    ? rows.map(function(r){return '<div class="detail-row"><span class="lbl">'+esc(r[0])+'</span><span class="val">'+esc(r[1])+'</span></div>';}).join('')
    : '<div class="detail-row"><span class="lbl">{{ __('noAdditionalDetails') }}</span></div>';
}
function renderProvider(){
  var prov=offer&&offer.provider?offer.provider:null;
  if(!prov||!prov.id){$('provider-card').hidden=true;return;}
  $('provider-card').hidden=false;
  var initial=String(prov.full_name||'?').trim().charAt(0).toUpperCase();
  $('provider-avatar').innerHTML=prov.profile_photo_url?'<img src="'+esc(prov.profile_photo_url)+'" alt="">':esc(initial);
  $('provider-name').textContent=prov.full_name||'{{ __('anonymous') }}';
  var meta=[];
  if(prov.rating_score)meta.push('<span class="rating">★ '+Number(prov.rating_score).toFixed(1)+'</span>');
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
  if(need.location_text)meta.push('📍 '+esc(need.location_text));
  $('need-meta').innerHTML=meta.join(' · ');
  $('need-link').href='/needs/'+encodeURIComponent(need.id);
}
function renderActions(){
  if(!offer)return;
  var card=$('actions-card');
  var hasAny=false;
  var msg=$('btn-messages');
  if(isProvider||isOwner){msg.hidden=false;msg.href='/offers/'+encodeURIComponent(offer.id)+'/messages';hasAny=true;}
  else{msg.hidden=true;}
  var ownerRow=$('btn-row-owner');
  if(isOwner&&offer.status==='PENDING'&&need&&need.status==='OPEN'){ownerRow.hidden=false;hasAny=true;}
  else{ownerRow.hidden=true;}
  var wd=$('btn-withdraw');
  if(isProvider&&offer.status==='PENDING'){wd.hidden=false;hasAny=true;}
  else{wd.hidden=true;}
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
  showOnly('loading');
  var h={'Accept':'application/json'};
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
  var btn=null;
  if(endpoint.indexOf('accept')!==-1)btn=$('btn-accept');
  else if(endpoint.indexOf('reject')!==-1)btn=$('btn-reject');
  else if(endpoint.indexOf('withdraw')!==-1)btn=$('btn-withdraw');
  if(btn)btn.disabled=true;
  fetch('/api/v1/offers/'+encodeURIComponent(offerId)+endpoint,{
    method:'POST',
    headers:{'Accept':'application/json','X-CSRF-TOKEN':csrf}
  })
  .then(function(r){return r.json().then(function(j){return {s:r.status,b:j};});})
  .then(function(res){
    if(res.s===200){toast(onSuccess,2200);setTimeout(loadOffer,700);}
    else if(res.s===409){toast('{{ __('stateConflict') }}',2600);setTimeout(loadOffer,1000);}
    else if(res.s===403){toast('{{ __('notAllowed') }}',2600);}
    else if(res.s===404){toast('{{ __('offerNotFound') }}',2600);}
    else{toast('{{ __('actionFailed') }}',2600);}
  })
  .catch(function(){toast('{{ __('actionFailed') }}',2600);})
  .then(function(){if(btn)btn.disabled=false;});
}
$('btn-accept').addEventListener('click',function(){openModal('{{ __('acceptOffer') }}','{{ __('confirmAccept') }}',function(){callAction('/accept','{{ __('offerAccepted') }}');});});
$('btn-reject').addEventListener('click',function(){openModal('{{ __('rejectOffer') }}','{{ __('confirmReject') }}',function(){callAction('/reject','{{ __('offerRejected') }}');});});
$('btn-withdraw').addEventListener('click',function(){openModal('{{ __('withdrawOffer') }}','{{ __('confirmWithdraw') }}',function(){callAction('/withdraw','{{ __('offerWithdrawn') }}');});});
$('modal-cancel').addEventListener('click',closeModal);
$('modal-confirm').addEventListener('click',function(){var a=pendingAction;closeModal();if(a)a();});
$('modal-backdrop').addEventListener('click',function(e){if(e.target===$('modal-backdrop'))closeModal();});
window.addEventListener('offline',function(){$('offline-banner').className='offline-banner on';});
window.addEventListener('online',function(){$('offline-banner').className='offline-banner';loadOffer();});
if(navigator.onLine===false){$('offline-banner').className='offline-banner on';}
window.loadOffer=loadOffer;
loadOffer();
})();
</script>
</body>
</html>
