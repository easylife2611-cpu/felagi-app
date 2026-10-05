<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>{{ __('screenS008') }} — {{ __('brand') }}</title>
<style>
*{box-sizing:border-box;margin:0;padding:0}
body{font-family:system-ui,-apple-system,sans-serif;background:#F4F6F8;color:#192431;line-height:1.5;min-height:100vh;padding-bottom:80px}
header{background:#003366;color:#fff;height:64px;padding:0 16px;display:flex;align-items:center;gap:12px;position:sticky;top:0;z-index:30}
header a{color:#fff;text-decoration:none;font-size:20px;padding:8px;border-radius:6px;line-height:1}
header a:hover{background:rgba(255,255,255,.12)}
header .title{font-size:16px;font-weight:600;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;flex:1;min-width:0}
main{max-width:720px;margin:0 auto;padding:16px}
.card{background:#fff;border-radius:12px;padding:20px;box-shadow:0 1px 3px rgba(0,0,0,.08);margin-bottom:16px}
.card h3{font-size:15px;color:#003366;margin:0 0 12px;font-weight:600}
.need-cat{display:inline-block;padding:3px 10px;border-radius:999px;background:#e8eef4;color:#003366;font-size:12px;font-weight:600;margin-bottom:10px}
.need-title{font-size:22px;font-weight:700;line-height:1.3;margin-bottom:12px;color:#192431}
.need-desc{font-size:15px;color:#3a4a5a;line-height:1.6;margin-bottom:16px;white-space:pre-wrap}
.meta-grid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));column-gap:26px;margin-top:4px}
.meta-item{background:transparent;padding:14px 0 13px;border-radius:0;border-bottom:1px solid #e4edf3}
.meta-item .lbl{font-size:11px;color:#718292;text-transform:uppercase;letter-spacing:.06em;font-weight:800;line-height:1.35}
.meta-item .val{font-size:16px;font-weight:800;color:#122a40;margin-top:5px;line-height:1.45;word-break:break-word}
.meta-item .val.budget{color:#176b36;font-size:18px}
.meta-item.full{grid-column:1/-1}
.budget{color:#1b5e20}
.badge{display:inline-block;padding:3px 10px;border-radius:999px;font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.5px}
.badge.open{background:#e8f5e9;color:#1b5e20}
.badge.in_progress{background:#fff3e0;color:#e65100}
.badge.completed{background:#e3f2fd;color:#0d47a1}
.badge.cancelled{background:#f5f5f5;color:#616161}
.requester{display:flex;align-items:center;gap:12px;padding:12px;background:#f6f8fa;border-radius:8px;margin-top:8px}
.avatar{width:40px;height:40px;border-radius:50%;background:#003366;color:#fff;display:flex;align-items:center;justify-content:center;font-size:16px;font-weight:700;flex:0 0 auto;overflow:hidden}
.avatar img{width:100%;height:100%;object-fit:cover}
.requester .name{font-weight:600;font-size:14px}
.requester .meta{font-size:12px;color:#586675}
.rating{color:#f5a623;font-weight:600}
.actions{display:flex;flex-direction:column;gap:8px}
.btn{padding:12px 20px;border-radius:8px;font-size:15px;font-weight:600;cursor:pointer;border:none;font-family:inherit;text-decoration:none;text-align:center;display:block;transition:background .15s}
.btn.primary{background:#003366;color:#fff}
.btn.primary:hover:not(:disabled){background:#002a52}
.btn.danger{background:#fff;color:#c62828;border:1px solid #ffcdd2}
.btn.danger:hover:not(:disabled){background:#ffebee}
.btn.success{background:#1b5e20;color:#fff}
.btn.success:hover:not(:disabled){background:#154a19}
.btn.ghost{background:#eef1f4;color:#192431}
.btn.ghost:hover{background:#e0e4e8}
.btn:disabled{opacity:.5;cursor:not-allowed}
.offers-list{display:flex;flex-direction:column;gap:10px}
.offer-card{border:1px solid #eef1f4;border-radius:8px;padding:12px;display:flex;justify-content:space-between;align-items:center;gap:10px}
.offer-card .offer-info{flex:1;min-width:0}
.offer-card .offer-price{font-weight:700;color:#1b5e20;font-size:15px}
.offer-card .offer-meta{font-size:12px;color:#586675;margin-top:2px}
.offer-card a{font-size:13px;color:#003366;text-decoration:none;font-weight:600;white-space:nowrap}
.offer-card a:hover{text-decoration:underline}
.state{padding:60px 20px;text-align:center;color:#586675}
.state h3{color:#003366;font-size:18px;margin:0 0 8px}
.state p{margin:0 0 16px}
.state .btn{max-width:220px;margin:0 auto}
.spinner{display:inline-block;width:20px;height:20px;border:3px solid #e0e0e0;border-top-color:#003366;border-radius:50%;animation:spin .8s linear infinite}
@keyframes spin{to{transform:rotate(360deg)}}
.offline-banner{background:#fff8e1;border:1px solid #ffe082;color:#8a6d00;padding:10px 14px;border-radius:8px;font-size:13px;margin-bottom:16px;display:none}
.offline-banner.on{display:block}
.toast{position:fixed;bottom:100px;left:50%;transform:translateX(-50%);background:#192431;color:#fff;padding:12px 20px;border-radius:8px;font-size:14px;z-index:40;opacity:0;pointer-events:none;transition:opacity .2s;max-width:90vw}
.toast.on{opacity:1}
.adslot{margin:16px 0;padding:12px;background:#fafbfc;border:1px dashed #d0d7de;border-radius:8px;text-align:center;font-size:12px;color:#586675;min-height:60px;display:flex;align-items:center;justify-content:center}
.dl{color:#586675;font-weight:400;font-size:13px}
.detail-hero{position:relative;background:linear-gradient(135deg,#003366 0%,#0b568c 68%,#176b8a 100%);color:#fff;border-radius:24px;padding:26px 22px 24px;margin-bottom:16px;overflow:hidden;box-shadow:0 12px 30px rgba(0,51,102,.18)}
.detail-hero:after{content:'✦';position:absolute;right:22px;top:10px;color:rgba(255,153,51,.3);font-size:96px;line-height:1;transform:rotate(18deg);pointer-events:none}.detail-hero>*{position:relative;z-index:1}
.detail-hero .need-cat{background:rgba(255,255,255,.15);color:#fff;margin-bottom:14px}.detail-hero .need-title{color:#fff;font-size:clamp(26px,5vw,38px);line-height:1.22;margin-bottom:18px;max-width:680px}.hero-row{display:flex;align-items:flex-end;justify-content:space-between;gap:14px;flex-wrap:wrap}.hero-budget{font-size:28px;font-weight:900;line-height:1.1;color:#fff}.hero-budget small{display:block;color:#d6e5ef;font-size:11px;font-weight:700;letter-spacing:.1em;text-transform:uppercase;margin-bottom:5px}.hero-facts{display:flex;gap:8px;flex-wrap:wrap;color:#e7f1f7;font-size:13px}.hero-facts span{background:rgba(255,255,255,.12);border:1px solid rgba(255,255,255,.18);border-radius:999px;padding:7px 10px}.detail-section{background:#fff;border:1px solid #e4edf4;border-radius:20px;padding:22px;margin-bottom:16px;box-shadow:0 6px 20px rgba(0,51,102,.07)}.detail-section h2{font-size:20px;line-height:1.3;color:#003366;margin-bottom:13px;font-weight:850}.detail-description{font-size:17px;line-height:1.8;color:#31485c;white-space:pre-wrap}.trust-grid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:10px}.trust-item{background:#f5f9fc;border:1px solid #e4edf4;border-radius:12px;padding:12px}.trust-item strong{display:block;color:#003366;font-size:14px}.trust-item span{display:block;color:#647687;font-size:12px;margin-top:3px}.detail-actions{position:sticky;bottom:12px;z-index:20;background:rgba(255,255,255,.94);backdrop-filter:blur(12px);border:1px solid #dce8f0;border-radius:18px;padding:10px;box-shadow:0 12px 30px rgba(0,51,102,.16)}.detail-actions .actions{flex-direction:row;align-items:center}.detail-actions .btn.primary{flex:1;min-height:48px}.detail-actions .btn.ghost,.detail-actions .btn.danger,.detail-actions .btn.success{min-height:48px}.details-divider{height:1px;background:#e8eef3;margin:20px 0}.requirements-empty{color:#647687;font-size:14px;background:#f7fafc;border-radius:12px;padding:14px}
/* Final locked detail polish: label/value presentation, readable description, compact trust. */
.meta-grid{grid-template-columns:repeat(2,minmax(0,1fr));column-gap:26px;margin-top:4px}
.meta-item{background:transparent;padding:14px 0 13px;border-radius:0;border-bottom:1px solid #e4edf3}
.meta-item .lbl{font-size:11px;color:#718292;text-transform:uppercase;letter-spacing:.06em;font-weight:800;line-height:1.35}
.meta-item .val{font-size:16px;font-weight:800;color:#122a40;margin-top:5px;line-height:1.45;word-break:break-word}
.meta-item .val.budget{color:#176b36;font-size:18px}
.detail-section{padding:24px}
.detail-description{font-size:18px;line-height:1.9;color:#263f54}
.trust-grid{display:flex;flex-wrap:wrap;gap:0;border-top:1px solid #e4edf3}
.trust-item{flex:1 1 46%;min-width:145px;background:transparent;border:0;border-bottom:1px solid #e4edf3;border-radius:0;padding:12px 14px 12px 0}
.trust-item strong{line-height:1.4}.trust-item span{line-height:1.4}
.requester-proof{display:flex;flex-wrap:wrap;gap:6px;margin-top:7px}.requester-proof .proof{display:inline-flex;align-items:center;padding:5px 8px;border-radius:999px;background:#f2f7fa;color:#526879;font-size:12px;font-weight:700}.requester-proof .proof.verified{background:#edf8f0;color:#176b36}
.detail-section.description-section{border-color:#d7e6ef;box-shadow:0 10px 28px rgba(0,51,102,.09)}.detail-section.description-section h2{font-size:22px;margin-bottom:18px}.requester-section{box-shadow:0 3px 12px rgba(0,51,102,.05);padding:20px}.requester-section .requester{background:transparent;padding:0;margin-top:8px}.requester-section .name{font-size:16px;font-weight:850;color:#122a40}.requester-section .meta{font-size:13px;color:#586f80;margin-top:3px}.requester-section .avatar{width:48px;height:48px}.requester-section .avatar img{width:48px;height:48px}.owner-section{box-shadow:none;background:#f8fafc}.owner-section .btn.danger{margin-left:auto}.owner-section .actions{gap:10px;flex-wrap:wrap}.owner-section .btn.primary{order:0}.owner-section .btn.ghost{order:1}.owner-section .btn.success{order:2}.owner-section .btn.danger{order:3}
@media(min-width:600px){main{padding:20px}}
@media(max-width:480px){.trust-grid{grid-template-columns:1fr}.detail-hero{padding:22px 18px}.detail-actions .actions{flex-wrap:wrap}.detail-actions .btn.primary{flex-basis:100%}}
@media(max-width:480px){.meta-grid{grid-template-columns:1fr;column-gap:0}.meta-item.full{grid-column:auto}}

:focus-visible{outline:3px solid #1b5e20;outline-offset:2px;border-radius:6px}
#page-title:focus{outline:none}
.sr-only{position:absolute;width:1px;height:1px;padding:0;margin:-1px;overflow:hidden;clip:rect(0,0,0,0);white-space:nowrap;border:0}
</style>
@include('partials.felagi-polish')
</head>
<body>
<header>
<a href="/browse" title="{{ __('back') }}">&#8592;</a>
<span class="title" id="page-title" tabindex="-1" role="heading" aria-level="1">{{ __('needDetails') }}</span>
</header>

<main role="main" aria-labelledby="page-title">
<div class="offline-banner" id="offline-banner">{{ __('offlineBody') }}</div>

<div id="state-loading" class="state">
<div class="spinner"></div>
<p style="margin-top:12px">{{ __('loading') }}...</p>
</div>

<div id="state-error" class="state" hidden>
<h3>{{ __('loadErrorTitle') }}</h3>
<p>{{ __('loadErrorBody') }}</p>
<button class="btn primary" onclick="loadNeed()">{{ __('retry') }}</button>
</div>

<div id="state-denied" class="state" hidden>
<h3>{{ __('accessDenied') }}</h3>
<p>{{ __('accessDeniedBody') }}</p>
<a href="/browse" class="btn primary">{{ __('backToBrowse') }}</a>
</div>

<div id="content" hidden>

<section class="detail-hero" aria-labelledby="need-title">
<span class="need-cat" id="need-cat"></span>
<span class="badge" id="need-status"></span>
<h1 class="need-title" id="need-title"></h1>
<div class="hero-row"><div class="hero-budget"><small>{{ __('budgetLabel') }}</small><span id="need-budget-hero"></span></div><div class="hero-facts" id="need-hero-facts"></div></div>
</section>

<section class="detail-section" aria-labelledby="summary-heading">
<h2 id="summary-heading">{{ __('needDetails') }}</h2>
<div class="meta-grid" id="need-meta"></div>
</section>

<section class="detail-section description-section" aria-labelledby="description-heading">
<h2 id="description-heading">{{ app()->getLocale()==='am' ? 'መግለጫ' : 'Description' }}</h2>
<div class="detail-description" id="need-desc"></div>
</section>

<section class="detail-section" id="requirements-section" aria-labelledby="requirements-heading" hidden>
<h2 id="requirements-heading">{{ app()->getLocale()==='am' ? 'መስፈርቶች' : 'Requirements' }}</h2>
<div id="need-requirements"></div>
</section>

<section class="detail-section" aria-labelledby="trust-heading">
<h2 id="trust-heading">{{ app()->getLocale()==='am' ? 'እምነት እና ደህንነት' : 'Trust & safety' }}</h2>
<div class="trust-grid" id="need-trust"></div>
</section>

<div class="card requester-section" id="requester-card" hidden>
<h3>{{ __('postedBy') }}</h3>
<div class="requester">
<div class="avatar" id="req-avatar"></div>
<div>
<div class="name" id="req-name"></div>
<div class="meta" id="req-meta"></div>
<div class="requester-proof" id="req-proof"></div>
</div>
</div>
</div>

<div id="owner-actions" hidden>
<div class="detail-section owner-section">
<h3>{{ __('manage') }}</h3>
<div class="actions">
<a href="#" id="btn-view-offers" class="btn primary" hidden></a>
<a href="#" id="btn-edit" class="btn ghost" hidden>{{ __('editNeed') }}</a>
<button type="button" id="btn-complete" class="btn success" hidden>{{ __('markComplete') }}</button>
<button type="button" id="btn-cancel" class="btn danger" hidden>{{ __('cancelNeed') }}</button>
</div>
</div>
</div>

<div id="provider-actions" hidden>
<div class="detail-section detail-actions">
<div class="actions">
<a href="#" id="btn-submit-offer" class="btn primary">{{ __('submitOffer') }}</a>
</div>
</div>
</div>

<div class="adslot" data-ad-slot="AD_NEED_DETAIL_BOTTOM_01"></div>

</div>
</main>

<div class="toast" id="toast"></div>
<script>
// L342: locale-aware category name (respects app locale)
function felagiLocalizedName(obj) {
  if (!obj) return '';
  var am = document.documentElement.lang === 'am';
  return am
    ? (obj.name_am || obj.name_en || obj.slug || '')
    : (obj.name_en || obj.name_am || obj.slug || '');
}

(function(){
'use strict';
var csrf=(document.querySelector('meta[name="csrf-token"]')||{}).content||'';
var LS_TOKEN='felagi_token';

function $(id){return document.getElementById(id);}
function getToken(){return 'session'; /* L305d */}
function esc(s){return String(s==null?'':s).replace(/[&<>"']/g,function(c){return {'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c];});}
function toast(msg,ms){ms=ms||2500;var el=$('toast');el.textContent=msg;el.className='toast on';setTimeout(function(){el.className='toast';},ms);}
function getNeedId(){
  var parts=window.location.pathname.split('/').filter(Boolean);
  return parts[parts.length-1]||'';
}
function statusClass(s){return 'badge '+String(s||'').toLowerCase();}
function fmtBudget(it){
  var cur=it.currency||'ETB';
  var mn=it.budget_min!=null?Number(it.budget_min):null;
  var mx=it.budget_max!=null?Number(it.budget_max):null;
  if(mn==null&&mx==null)return '{{ __('negotiable') }}';
  if(mn!=null&&mx!=null&&mn!==mx)return cur+' '+mn+' - '+mx;
  return cur+' '+(mn!=null?mn:mx);
}
function fmtDate(iso){
  if(!iso)return '';
  var d=new Date(iso);
  if(isNaN(d))return '';
  return d.toLocaleDateString()+' '+d.toLocaleTimeString([],{hour:'2-digit',minute:'2-digit'});
}

function showOnly(id){
  ['state-loading','state-error','state-denied','content'].forEach(function(x){
    var el=$(x); if(el)el.hidden=(x!==id);
  });
}

function renderMeta(need){
  var html='';
  html+='<div class="meta-item"><div class="lbl">{{ __('budgetLabel') }}</div><div class="val budget">'+esc(fmtBudget(need))+'</div></div>';
  html+='<div class="meta-item"><div class="lbl">{{ __('status') }}</div><div class="val">'+esc(need.status||'')+'</div></div>';
  if(need.location_text){html+='<div class="meta-item full"><div class="lbl">{{ __('location') }}</div><div class="val">'+esc(need.location_text)+'</div></div>';}
  if(need.quantity!=null){html+='<div class="meta-item"><div class="lbl">{{ __('quantity') }}</div><div class="val">'+esc(need.quantity)+'</div></div>';}
  if(need.deadline_at){html+='<div class="meta-item"><div class="lbl">{{ __('deadline') }}</div><div class="val">'+esc(fmtDate(need.deadline_at))+'</div></div>';}
  if(need.offer_deadline_at){html+='<div class="meta-item full"><div class="lbl">{{ __('offerDeadline') }}</div><div class="val">'+esc(fmtDate(need.offer_deadline_at))+'</div></div>';}
  if(need.created_at){html+='<div class="meta-item full"><div class="lbl">{{ __('postedLabel') }}</div><div class="val dl">'+esc(fmtDate(need.created_at))+'</div></div>';}
  $('need-meta').innerHTML=html;
  $('need-budget-hero').textContent=fmtBudget(need);
  var facts=[];
  if(need.location_text)facts.push('⌖ '+need.location_text);
  if(need.created_at)facts.push('◷ '+fmtDate(need.created_at));
  if(need.deadline_at)facts.push('⌛ '+fmtDate(need.deadline_at));
  $('need-hero-facts').innerHTML=facts.map(function(x){return '<span>'+esc(x)+'</span>';}).join('');
  var req=[];
  if(need.quantity!=null)req.push('<div class="trust-item"><strong>'+esc(need.quantity)+'</strong><span>{{ __('quantity') }}</span></div>');
  if(need.offer_count!=null)req.push('<div class="trust-item"><strong>'+esc(need.offer_count)+'</strong><span>'+(document.documentElement.lang==='am'?'የተቀበሉ አቅርቦቶች':'Offers received')+'</span></div>');
  $('requirements-section').hidden=req.length===0;
  $('need-requirements').innerHTML=req.join('');
  $('need-trust').innerHTML='<div class="trust-item"><strong>✓ '+(document.documentElement.lang==='am'?'ንቁ ፍላጎት':'Active need')+'</strong><span>'+(document.documentElement.lang==='am'?'ሁኔታ':'Status')+'</span></div>'+(need.offer_count!=null?'<div class="trust-item"><strong>👥 '+esc(need.offer_count)+'</strong><span>'+(document.documentElement.lang==='am'?'አቅርቦቶች':'Offers')+'</span></div>':'')+(need.location_text?'<div class="trust-item"><strong>⌖ '+esc(need.location_text)+'</strong><span>'+(document.documentElement.lang==='am'?'ቦታ':'Location')+'</span></div>':'')+'<div class="trust-item"><strong>🤖 '+(document.documentElement.lang==='am'?'ውሳኔ የተጠቃሚው ነው':'AI supports your decision')+'</strong><span>'+(document.documentElement.lang==='am'?'AI እንደ ድጋፍ ብቻ ይሰራል':'AI is decision support only')+'</span></div>';
}

function renderRequester(need){
  var r=need.requester||{};
  if(!r.full_name&&!r.id){$('requester-card').hidden=true;return;}
  $('requester-card').hidden=false;
  var initial=String(r.full_name||'?').trim().charAt(0).toUpperCase();
  $('req-avatar').innerHTML=r.profile_photo_url?'<img src="'+esc(r.profile_photo_url)+'" alt="">':esc(initial);
  $('req-name').textContent=r.full_name||'{{ __('anonymous') }}';
  var meta=[];
  if(r.rating_score)meta.push('<span class="rating">&#9733; '+Number(r.rating_score).toFixed(1)+'</span>');
  if(r.rating_count)meta.push('('+r.rating_count+')');
  $('req-meta').innerHTML=meta.join(' ');
  var proof=[];
  if(r.verified===true||r.is_verified===true||r.verified_at)proof.push('<span class="proof verified">✓ '+(document.documentElement.lang==='am'?'የተረጋገጠ':'Verified')+'</span>');
  var memberSince=r.member_since||r.created_at;
  if(memberSince)proof.push('<span class="proof">'+(document.documentElement.lang==='am'?'አባል ከ':'Member since')+' '+esc(fmtDate(memberSince))+'</span>');
  if(r.response_rate||r.response_time)proof.push('<span class="proof">'+(document.documentElement.lang==='am'?'ምላሽ':'Response')+' '+esc(String(r.response_rate||r.response_time))+'</span>');
  $('req-proof').innerHTML=proof.join('');
}

function renderActions(need){
  var isOwner=!!need.is_owner;
  var status=need.status||'';
  var nid=need.id||getNeedId();

  if(isOwner){
    $('owner-actions').hidden=false;
    $('provider-actions').hidden=true;

    var vo=$('btn-view-offers');
    vo.href='/needs/'+nid+'/offers';
    vo.textContent='{{ __('viewOffers') }}'+(need.offer_count?' ('+need.offer_count+')':'');
    vo.hidden=false;

    if(status==='OPEN'){
      $('btn-edit').href='/needs/'+nid+'/edit';
      $('btn-edit').hidden=false;
      $('btn-cancel').hidden=false;
      $('btn-complete').hidden=true;
    }else if(status==='IN_PROGRESS'){
      $('btn-edit').hidden=true;
      $('btn-cancel').hidden=true;
      $('btn-complete').hidden=false;
    }else{
      $('btn-edit').hidden=true;
      $('btn-cancel').hidden=true;
      $('btn-complete').hidden=true;
    }
  }else{
    $('owner-actions').hidden=true;
    if(status==='OPEN'){
      $('provider-actions').hidden=false;
      $('btn-submit-offer').href='/needs/'+nid+'/offers/new';
    }else{
      $('provider-actions').hidden=true;
    }
  }
}

function renderNeed(need){
  document.title=(need.title||'{{ __('needDetails') }}')+' — {{ __('brand') }}';
  $('page-title').textContent=need.title||'{{ __('needDetails') }}';
  $('need-title').textContent=need.title||'';
  $('need-desc').textContent=need.description||'';

  var cat=need.category||{};
  var catName=felagiLocalizedName(cat);
  if(catName){$('need-cat').textContent=catName;$('need-cat').style.display='inline-block';}
  else{$('need-cat').style.display='none';}

  $('need-status').textContent=need.status||'';
  $('need-status').className=statusClass(need.status);

  renderMeta(need);
  renderRequester(need);
  renderActions(need);

  showOnly('content');
}

function loadNeed(){
  var nid=getNeedId();
  if(!nid){showOnly('state-error');return;}

  if(navigator.onLine===false){
    $('offline-banner').className='offline-banner on';
  }

  var h={'Accept':'application/json'}/* L305d */;
  var t=getToken();
  if(t)/* L305d: cookie auth */

  fetch('/api/v1/needs/'+encodeURIComponent(nid),{headers:h})
    .then(function(r){
      if(r.status===404)throw new Error('notfound');
      if(r.status===401||r.status===403)throw new Error('denied');
      if(r.status===429)throw new Error('rate-limited');
      if(!r.ok)throw new Error('HTTP '+r.status);
      return r.json();
    })
    .then(function(j){
      var need=(j&&j.data)?j.data:j;
      if(!need||!need.id){showOnly('state-error');return;}
      renderNeed(need);
    })
    .catch(function(e){
      var m=String(e.message||e);
      if(m==='denied'){showOnly('state-denied');return;}
      if(m==='notfound'){showOnly('state-error');return;}
      showOnly('state-error');
    });
}

function doComplete(){
  if(!confirm('{{ __('confirmComplete') }}'))return;
  var t=getToken();
  if(!t){window.location.href='/';return;}
  var b=$('btn-complete');
  b.disabled=true;
  fetch('/api/v1/needs/'+encodeURIComponent(getNeedId())+'/complete',{
    method:'POST',
    headers:{'Accept':'application/json','X-CSRF-TOKEN':csrf}
  }).then(function(r){return r.json().then(function(j){return {s:r.status,b:j};});})
    .then(function(res){
      if(res.s===200){toast('{{ __('needCompleted') }}');setTimeout(loadNeed,800);}
      else if(res.s===409){toast('{{ __('stateConflict') }}');}
      else{toast('{{ __('actionFailed') }}');}
    })
    .catch(function(){toast('{{ __('actionFailed') }}');})
    .then(function(){b.disabled=false;});
}

function doCancel(){
  if(!confirm('{{ __('confirmCancel') }}'))return;
  var t=getToken();
  if(!t){window.location.href='/';return;}
  var b=$('btn-cancel');
  b.disabled=true;
  fetch('/api/v1/needs/'+encodeURIComponent(getNeedId())+'/cancel',{
    method:'POST',
    headers:{'Accept':'application/json','X-CSRF-TOKEN':csrf}
  }).then(function(r){return r.json().then(function(j){return {s:r.status,b:j};});})
    .then(function(res){
      if(res.s===200){toast('{{ __('needCancelled') }}');setTimeout(loadNeed,800);}
      else if(res.s===409){toast('{{ __('stateConflict') }}');}
      else{toast('{{ __('actionFailed') }}');}
    })
    .catch(function(){toast('{{ __('actionFailed') }}');})
    .then(function(){b.disabled=false;});
}

window.loadNeed=loadNeed;
$('btn-complete').addEventListener('click',doComplete);
$('btn-cancel').addEventListener('click',doCancel);

window.addEventListener('offline',function(){$('offline-banner').className='offline-banner on';});
window.addEventListener('online',function(){$('offline-banner').className='offline-banner';loadNeed();});

loadNeed();
})();
</script>
</body>
</html>
