<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>{{ __('screenS008') }} — {{ __('brand') }}</title>
<meta name="theme-color" content="#003366">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Ethiopic:wght@400;500;600;700;800;900&family=Inter:wght@400;600;700;800;900&display=swap" rel="stylesheet">
<style>
:root{--navy-950:#001a33;--navy-900:#002b52;--navy-800:#003366;--navy-700:#0b4d83;--navy-600:#1a6bb0;--orange-700:#b35c00;--orange-600:#e8851d;--orange-500:#FF9933;--orange-400:#ffb366;--orange-100:#fff2e0;--ink-900:#0d1a2b;--ink-700:#132238;--ink-500:#3c4d61;--ink-300:#5f7185;--ink-200:#8fa0b3;--line-100:#eef2f6;--line-200:#e3e9ef;--line-300:#d5dde6;--surface:#fff;--canvas:#f4f7fa;--canvas-2:#eef3f8;--success-600:#0e6b34;--success-500:#147a3d;--success-100:#e8f5ee;--warn-600:#a86500;--warn-100:#fff7e6;--danger-600:#a32e21;--danger-500:#c0392b;--danger-100:#fdecea;--info-600:#0056d6;--info-500:#0d6efd;--info-100:#e7f1ff;--r-xs:8px;--r-sm:12px;--r-md:16px;--r-lg:22px;--r-xl:28px;--r-pill:999px;--sh-xs:0 1px 2px rgba(0,26,51,.04),0 1px 3px rgba(0,26,51,.06);--sh-sm:0 2px 6px rgba(0,26,51,.05),0 4px 12px rgba(0,26,51,.06);--sh-md:0 4px 12px rgba(0,26,51,.06),0 12px 28px rgba(0,26,51,.08);--sh-navy:0 12px 32px rgba(0,51,102,.28);--sh-orange:0 8px 20px rgba(255,153,51,.32),0 2px 6px rgba(255,153,51,.20);--f-am:'Noto Sans Ethiopic','Inter',system-ui,sans-serif;--f-en:'Inter','Noto Sans Ethiopic',system-ui,sans-serif;--ease:cubic-bezier(.16,.84,.44,1);--ease-out:cubic-bezier(.22,1,.36,1)}
*{box-sizing:border-box;margin:0;padding:0}
html,body{background:var(--canvas);color:var(--ink-900);font-family:var(--f-am);line-height:1.55;-webkit-font-smoothing:antialiased}
body{padding:0 0 100px;min-height:100vh}
a{color:inherit;text-decoration:none}
button{font-family:inherit;cursor:pointer;border:0;background:transparent;color:inherit}
input,select,textarea{font-family:inherit;font-size:inherit}
:focus-visible{outline:3px solid var(--orange-500);outline-offset:3px;border-radius:8px}
.hidden{display:none!important}
.sr-only{position:absolute;width:1px;height:1px;padding:0;margin:-1px;overflow:hidden;clip:rect(0,0,0,0);white-space:nowrap;border:0}
.sc{max-width:720px;margin:0 auto;min-height:100vh;display:flex;flex-direction:column}
.sh{flex:0 0 auto;color:#fff;padding:18px 22px 22px;position:relative;overflow:hidden;z-index:1}
.mesh{background:radial-gradient(ellipse at top right,rgba(255,153,51,.18),transparent 55%),radial-gradient(ellipse at bottom left,rgba(26,107,176,.35),transparent 60%),linear-gradient(140deg,var(--navy-950),var(--navy-800))}
.sh .tr{display:flex;align-items:center;justify-content:space-between;gap:12px;position:relative;z-index:1}
.sh .ha{display:flex;gap:6px;align-items:center}
.ib{width:40px;height:40px;border-radius:50%;display:inline-flex;align-items:center;justify-content:center;color:#fff;background:rgba(255,255,255,.10);border:1px solid rgba(255,255,255,.08);transition:all .2s var(--ease);position:relative}
.ib:hover{background:rgba(255,255,255,.18)}
.ib svg{width:20px;height:20px;stroke:currentColor;fill:none;stroke-width:2;stroke-linecap:round;stroke-linejoin:round}
.sh .t{margin-top:10px;font-size:17px;font-weight:900;letter-spacing:-.025em;line-height:1.3;position:relative;z-index:1}
.sb{flex:1 1 auto;padding:20px 18px 40px;position:relative;z-index:1}
.dh{background:linear-gradient(155deg,var(--navy-950) 0%,var(--navy-800) 40%,var(--navy-700) 100%);color:#fff;border-radius:var(--r-xl);padding:28px 24px 26px;margin-bottom:20px;position:relative;overflow:hidden;box-shadow:var(--sh-navy)}
.dh::before{content:'';position:absolute;top:-30%;right:-20%;width:320px;height:320px;background:radial-gradient(circle,rgba(255,153,51,.24),transparent 60%)}
.dh>*{position:relative;z-index:1}
.dh .cat-pill{background:rgba(255,153,51,.18);color:var(--orange-400);padding:7px 14px;border-radius:var(--r-pill);font-size:11px;font-weight:800;letter-spacing:.06em;text-transform:uppercase;border:1px solid rgba(255,153,51,.28);font-family:var(--f-en);display:inline-block;margin-bottom:14px}
.dh h2{font-size:26px;font-weight:900;letter-spacing:-.04em;line-height:1.2;margin-bottom:14px;font-family:var(--f-am)}
.dh .pr{font-size:32px;font-weight:900;color:var(--orange-500);letter-spacing:-.04em;line-height:1;font-family:var(--f-en);display:flex;align-items:baseline;gap:6px}
.dh .pr::before{content:'ETB';font-size:12px;color:rgba(255,255,255,.6);font-weight:800;letter-spacing:.08em}
.dh .fact-row{display:flex;gap:14px;flex-wrap:wrap;font-size:12px;color:rgba(255,255,255,.72);font-weight:500;margin-top:14px}
.dh .fact-row span{display:inline-flex;align-items:center;gap:5px}
.cd{background:var(--surface);border:1px solid var(--line-100);border-radius:var(--r-md);padding:20px;margin-bottom:14px;box-shadow:var(--sh-xs)}
.cd h3{font-size:13px;font-weight:900;color:var(--navy-800);text-transform:uppercase;letter-spacing:.08em;margin-bottom:12px;font-family:var(--f-en)}
.cd p{font-size:14px;color:var(--ink-500);line-height:1.75;white-space:pre-wrap}
.meta-grid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));column-gap:20px}
.meta-item{background:transparent;padding:12px 0;border-bottom:1px solid var(--line-100)}
.meta-item .lbl{font-size:10.5px;color:var(--ink-300);text-transform:uppercase;letter-spacing:.08em;font-weight:800;line-height:1.35;font-family:var(--f-en)}
.meta-item .val{font-size:15px;font-weight:800;color:var(--ink-900);margin-top:4px;line-height:1.45;word-break:break-word}
.meta-item .val.budget{color:var(--success-600);font-size:17px;font-family:var(--f-en)}
.meta-item.full{grid-column:1/-1}
.av{width:46px;height:46px;border-radius:50%;background:linear-gradient(135deg,var(--navy-800),var(--navy-600));color:#fff;display:flex;align-items:center;justify-content:center;font-weight:900;font-size:15px;box-shadow:0 2px 8px rgba(0,51,102,.16);flex-shrink:0;overflow:hidden}
.av.lg{width:60px;height:60px;font-size:20px}
.av img{width:100%;height:100%;object-fit:cover}
.bg{display:inline-flex;align-items:center;gap:5px;padding:4px 10px;border-radius:var(--r-pill);font-size:10px;font-weight:800;letter-spacing:.05em;font-family:var(--f-en)}
.bg::before{content:'●';font-size:6px}
.bg.ok,.bg.open{background:var(--success-100);color:var(--success-600)}
.bg.info{background:var(--info-100);color:var(--info-600)}
.bg.warn,.bg.in_progress{background:var(--warn-100);color:var(--warn-600)}
.bg.danger{background:var(--danger-100);color:var(--danger-600)}
.bg.cancelled,.bg.completed{background:var(--line-100);color:var(--ink-500)}
.bt{display:inline-flex;align-items:center;justify-content:center;gap:9px;height:52px;padding:0 24px;border-radius:var(--r-sm);font-weight:900;font-size:15px;line-height:1;letter-spacing:-.015em;transition:all .2s var(--ease);font-family:var(--f-am);text-align:center}
.bt:active{transform:scale(.975)}
.bt-p{background:linear-gradient(135deg,var(--orange-500),var(--orange-600));color:#fff;box-shadow:var(--sh-orange)}
.bt-p:hover{box-shadow:0 12px 28px rgba(255,153,51,.42);transform:translateY(-1px)}
.bt-n{background:linear-gradient(135deg,var(--navy-800),var(--navy-700));color:#fff}
.bt-g{background:var(--surface);color:var(--navy-800);border:1.5px solid var(--line-300)}
.bt-g:hover{background:var(--canvas-2);border-color:var(--navy-700)}
.bt-danger{background:#fff;color:var(--danger-600);border:1.5px solid rgba(192,57,43,.25)}
.bt-danger:hover{background:var(--danger-100)}
.bt-success{background:linear-gradient(135deg,var(--success-500),var(--success-600));color:#fff}
.bt-b{width:100%}
.bt-s{height:42px;padding:0 16px;font-size:13px;border-radius:var(--r-sm)}
.bt[disabled]{opacity:.5;cursor:not-allowed}
.trust-row{display:grid;grid-template-columns:repeat(2,1fr);gap:10px;margin-top:12px}
.trust-item{background:var(--canvas-2);border:1px solid var(--line-200);border-radius:var(--r-sm);padding:12px 14px;text-align:center}
.trust-item strong{display:block;font-size:14px;font-weight:900;color:var(--navy-800);margin-bottom:4px;font-family:var(--f-am)}
.trust-item span{font-size:10.5px;color:var(--ink-300);font-weight:700;font-family:var(--f-en);letter-spacing:.04em;text-transform:uppercase}
.offline-banner{display:none;background:var(--warn-100);color:var(--warn-600);padding:10px 16px;border-radius:var(--r-sm);font-size:12.5px;font-weight:800;text-align:center;margin-bottom:14px}
.offline-banner.on{display:block}
.state{padding:60px 24px;text-align:center;color:var(--ink-300)}
.state .ic{font-size:44px;margin-bottom:14px;opacity:.5}
.state h3{font-size:18px;font-weight:900;color:var(--navy-800);margin-bottom:8px}
.state p{font-size:13.5px;line-height:1.6;color:var(--ink-500);max-width:300px;margin:0 auto 16px}
.state a{color:var(--navy-800);text-decoration:underline;font-weight:800}
.toast{position:fixed;bottom:100px;left:50%;transform:translateX(-50%) translateY(20px);background:var(--navy-900);color:#fff;padding:12px 20px;border-radius:var(--r-pill);font-size:13px;font-weight:700;opacity:0;pointer-events:none;transition:all .3s var(--ease-out);z-index:50;box-shadow:var(--sh-md);max-width:90vw;text-align:center}
.toast.on{opacity:1;transform:translateX(-50%) translateY(0)}
.requester-card{background:var(--surface);border:1px solid var(--line-100);border-radius:var(--r-md);padding:20px;margin-bottom:14px;box-shadow:var(--sh-xs)}
.requester-row{display:flex;gap:14px;align-items:center;margin-bottom:14px}
.req-proof{display:flex;gap:6px;flex-wrap:wrap;margin-top:10px}
.proof{display:inline-flex;align-items:center;gap:5px;padding:5px 11px;border-radius:var(--r-pill);background:var(--canvas-2);color:var(--ink-500);font-size:10.5px;font-weight:800;letter-spacing:.03em;font-family:var(--f-en)}
.proof.verified{background:var(--success-100);color:var(--success-600)}
.req-name{font-size:16px;font-weight:900;color:var(--ink-900);letter-spacing:-.01em;margin-bottom:3px}
.req-meta{font-size:12px;color:var(--ink-300);font-weight:700;font-family:var(--f-en)}
@media(min-width:600px){.sb{padding:28px 24px 48px}.dh{padding:36px 28px 30px}.dh h2{font-size:30px}.dh .pr{font-size:38px}.trust-row{grid-template-columns:repeat(3,1fr)}}
</style>
</head>
<body>
<div class="sc">
<div class="sh mesh">
  <div class="tr">
    <a href="/browse" class="ib" title="{{ __('back') }}" aria-label="{{ __('back') }}">
      <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M19 12H5M12 19l-7-7 7-7"/></svg>
    </a>
    <div class="ha">
      <button class="ib" type="button" aria-label="favorite" title="favorite">
        <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78Z"/></svg>
      </button>
      <button class="ib" type="button" aria-label="share" title="share">
        <svg viewBox="0 0 24 24" aria-hidden="true"><circle cx="18" cy="5" r="3"/><circle cx="6" cy="12" r="3"/><circle cx="18" cy="19" r="3"/><path d="m8.59 13.51 6.83 3.98M15.41 6.51l-6.82 3.98"/></svg>
      </button>
    </div>
  </div>
  <h1 class="t" id="page-title" tabindex="-1" role="heading" aria-level="1">{{ __('needDetails') }}</h1>
</div>
<div class="sb">
  <div class="offline-banner" id="offline-banner" role="status" aria-live="polite">{{ __('offlineBody') }}</div>
  <div class="state" id="state-loading"><div class="ic">⏳</div><h3>{{ __('loading') }}...</h3></div>
  <div class="state" id="state-error" hidden><div class="ic">⚠️</div><h3>{{ __('loadErrorTitle') }}</h3><p>{{ __('loadErrorBody') }}</p><button type="button" class="bt bt-n bt-s" onclick="loadNeed()">{{ __('retry') }}</button></div>
  <div class="state" id="state-denied" hidden><div class="ic">🔒</div><h3>{{ __('accessDenied') }}</h3><p>{{ __('accessDeniedBody') }}</p><a href="/browse" class="bt bt-n bt-s">{{ __('backToBrowse') }}</a></div>
  <div id="content" hidden>
    <div class="dh">
      <span class="cat-pill" id="need-cat" style="display:none"></span>
      <h2 id="need-title"></h2>
      <div class="pr" id="need-budget-hero"></div>
      <div class="fact-row" id="need-hero-facts"></div>
    </div>
    <div class="cd"><h3>{{ __('needDetails') }}</h3><p id="need-desc"></p></div>
    <div class="cd"><h3>{{ __('budgetLabel') }} / {{ __('status') }}</h3><div class="meta-grid" id="need-meta"></div></div>
    <div class="cd" id="requirements-section" hidden><h3>{{ __('quantity') }}</h3><div class="trust-row" id="need-requirements"></div></div>
    <div class="requester-card" id="requester-card" hidden>
      <h3 style="font-size:13px;font-weight:900;color:var(--navy-800);text-transform:uppercase;letter-spacing:.08em;margin-bottom:14px;font-family:var(--f-en)">{{ __('postedBy') }}</h3>
      <div class="requester-row">
        <div class="av lg" id="req-avatar"></div>
        <div style="flex:1;min-width:0"><div class="req-name" id="req-name"></div><div class="req-meta" id="req-meta"></div></div>
      </div>
      <div class="req-proof" id="req-proof"></div>
    </div>
    <div id="owner-actions" hidden>
      <a href="#" id="btn-view-offers" class="bt bt-n bt-b" hidden>{{ __('viewOffers') }}</a>
      <a href="#" id="btn-edit" class="bt bt-g bt-b" style="margin-top:10px" hidden>{{ __('editNeed') }}</a>
      <button type="button" id="btn-complete" class="bt bt-success bt-b" style="margin-top:10px" hidden>{{ __('markComplete') }}</button>
      <button type="button" id="btn-cancel" class="bt bt-danger bt-b" style="margin-top:10px" hidden>{{ __('cancelNeed') }}</button>
    </div>
    <div id="provider-actions" hidden>
      <a href="#" id="btn-submit-offer" class="bt bt-p bt-b">{{ __('submitOffer') }}</a>
    </div>
  </div>
</div>
<div class="toast" id="toast" role="status" aria-live="polite"></div>
</div>
<script>
function felagiLocalizedName(obj){if(!obj)return '';var am=document.documentElement.lang==='am';return am?(obj.name_am||obj.name_en||obj.slug||''):(obj.name_en||obj.name_am||obj.slug||'');}
(function(){
'use strict';
var csrf=(document.querySelector('meta[name="csrf-token"]')||{}).content||'';
function $(id){return document.getElementById(id);}
function getToken(){return 'session';}
function esc(s){return String(s==null?'':s).replace(/[&<>"']/g,function(c){return {'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c];});}
function toast(msg,ms){ms=ms||2500;var el=$('toast');el.textContent=msg;el.className='toast on';setTimeout(function(){el.className='toast';},ms);}
function getNeedId(){var parts=window.location.pathname.split('/').filter(Boolean);return parts[parts.length-1]||'';}
function statusClass(s){var k=String(s||'').toLowerCase().replace(/\s+/g,'_');return 'bg '+k;}
function fmtBudget(it){var cur=it.currency||'ETB';var mn=it.budget_min!=null?Number(it.budget_min):null;var mx=it.budget_max!=null?Number(it.budget_max):null;if(mn==null&&mx==null)return '{{ __('negotiable') }}';if(mn!=null&&mx!=null&&mn!==mx)return cur+' '+mn+' - '+mx;return cur+' '+(mn!=null?mn:mx);}
function fmtDate(iso){if(!iso)return '';var d=new Date(iso);if(isNaN(d))return '';return d.toLocaleDateString()+' '+d.toLocaleTimeString([],{hour:'2-digit',minute:'2-digit'});}
function showOnly(id){['state-loading','state-error','state-denied','content'].forEach(function(x){var el=$(x);if(el)el.hidden=(x!==id);});}
function renderMeta(need){
  var html='';
  html+='<div class="meta-item"><div class="lbl">{{ __('budgetLabel') }}</div><div class="val budget">'+esc(fmtBudget(need))+'</div></div>';
  html+='<div class="meta-item"><div class="lbl">{{ __('status') }}</div><div class="val"><span class="'+statusClass(need.status)+'">'+esc(need.status||'')+'</span></div></div>';
  if(need.location_text){html+='<div class="meta-item full"><div class="lbl">{{ __('location') }}</div><div class="val">'+esc(need.location_text)+'</div></div>';}
  if(need.quantity!=null){html+='<div class="meta-item"><div class="lbl">{{ __('quantity') }}</div><div class="val">'+esc(need.quantity)+'</div></div>';}
  if(need.deadline_at){html+='<div class="meta-item"><div class="lbl">{{ __('deadline') }}</div><div class="val">'+esc(fmtDate(need.deadline_at))+'</div></div>';}
  if(need.offer_deadline_at){html+='<div class="meta-item full"><div class="lbl">{{ __('offerDeadline') }}</div><div class="val">'+esc(fmtDate(need.offer_deadline_at))+'</div></div>';}
  if(need.created_at){html+='<div class="meta-item full"><div class="lbl">{{ __('postedLabel') }}</div><div class="val">'+esc(fmtDate(need.created_at))+'</div></div>';}
  $('need-meta').innerHTML=html;
  var facts=[];
  if(need.location_text)facts.push('<span>📍 '+esc(need.location_text)+'</span>');
  if(need.deadline_at)facts.push('<span>🕐 '+esc(fmtDate(need.deadline_at))+'</span>');
  if(need.offer_count!=null)facts.push('<span>📩 '+esc(need.offer_count)+'</span>');
  $('need-hero-facts').innerHTML=facts.join('');
  var req=[];
  if(need.quantity!=null)req.push('<div class="trust-item"><strong>'+esc(need.quantity)+'</strong><span>{{ __('quantity') }}</span></div>');
  if(need.offer_count!=null)req.push('<div class="trust-item"><strong>'+esc(need.offer_count)+'</strong><span>'+(document.documentElement.lang==='am'?'የተቀበሉ አቅርቦቶች':'Offers received')+'</span></div>');
  $('requirements-section').hidden=req.length===0;
  $('need-requirements').innerHTML=req.join('');
}
function renderRequester(need){
  var r=need.requester||{};
  if(!r.full_name&&!r.id){$('requester-card').hidden=true;return;}
  $('requester-card').hidden=false;
  var initial=String(r.full_name||'?').trim().charAt(0).toUpperCase();
  $('req-avatar').innerHTML=r.profile_photo_url?'<img src="'+esc(r.profile_photo_url)+'" alt="">':esc(initial);
  $('req-name').textContent=r.full_name||'{{ __('anonymous') }}';
  var meta=[];
  if(r.rating_score)meta.push('★ '+Number(r.rating_score).toFixed(1));
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
  var isOwner=!!need.is_owner;var status=need.status||'';var nid=need.id||getNeedId();
  if(isOwner){
    $('owner-actions').hidden=false;$('provider-actions').hidden=true;
    var vo=$('btn-view-offers');
    vo.href='/needs/'+nid+'/offers';
    vo.textContent='{{ __('viewOffers') }}'+(need.offer_count?' ('+need.offer_count+')':'');
    vo.hidden=false;
    if(status==='OPEN'){$('btn-edit').href='/needs/'+nid+'/edit';$('btn-edit').hidden=false;$('btn-cancel').hidden=false;$('btn-complete').hidden=true;}
    else if(status==='IN_PROGRESS'){$('btn-edit').hidden=true;$('btn-cancel').hidden=true;$('btn-complete').hidden=false;}
    else{$('btn-edit').hidden=true;$('btn-cancel').hidden=true;$('btn-complete').hidden=true;}
  }else{
    $('owner-actions').hidden=true;
    if(status==='OPEN'){$('provider-actions').hidden=false;$('btn-submit-offer').href='/needs/'+nid+'/offers/new';}
    else{$('provider-actions').hidden=true;}
  }
}
function renderNeed(need){
  document.title=(need.title||'{{ __('needDetails') }}')+' — {{ __('brand') }}';
  $('page-title').textContent=need.title||'{{ __('needDetails') }}';
  $('need-title').textContent=need.title||'';
  $('need-desc').textContent=need.description||'';
  var cat=need.category||{};var catName=felagiLocalizedName(cat);
  if(catName){$('need-cat').textContent=catName;$('need-cat').style.display='inline-block';}else{$('need-cat').style.display='none';}
  renderMeta(need);renderRequester(need);renderActions(need);
  showOnly('content');
}
function loadNeed(){
  var nid=getNeedId();
  if(!nid){showOnly('state-error');return;}
  if(navigator.onLine===false){$('offline-banner').className='offline-banner on';}
  var h={'Accept':'application/json'};
  var t=getToken();if(t){}
  fetch('/api/v1/needs/'+encodeURIComponent(nid),{headers:h})
    .then(function(r){
      if(r.status===404)throw new Error('notfound');
      if(r.status===401||r.status===403)throw new Error('denied');
      if(r.status===429)throw new Error('rate-limited');
      if(!r.ok)throw new Error('HTTP '+r.status);
      return r.json();
    })
    .then(function(j){var need=(j&&j.data)?j.data:j;if(!need||!need.id){showOnly('state-error');return;}renderNeed(need);})
    .catch(function(e){var m=String(e.message||e);if(m==='denied'){showOnly('state-denied');return;}if(m==='notfound'){showOnly('state-error');return;}showOnly('state-error');});
}
function doComplete(){
  if(!confirm('{{ __('confirmComplete') }}'))return;
  var t=getToken();if(!t){window.location.href='/';return;}
  var b=$('btn-complete');b.disabled=true;
  fetch('/api/v1/needs/'+encodeURIComponent(getNeedId())+'/complete',{method:'POST',headers:{'Accept':'application/json','X-CSRF-TOKEN':csrf}})
    .then(function(r){return r.json().then(function(j){return {s:r.status,b:j};});})
    .then(function(res){if(res.s===200){toast('{{ __('needCompleted') }}');setTimeout(loadNeed,800);}else if(res.s===409){toast('{{ __('stateConflict') }}');}else{toast('{{ __('actionFailed') }}');}})
    .catch(function(){toast('{{ __('actionFailed') }}');})
    .then(function(){b.disabled=false;});
}
function doCancel(){
  if(!confirm('{{ __('confirmCancel') }}'))return;
  var t=getToken();if(!t){window.location.href='/';return;}
  var b=$('btn-cancel');b.disabled=true;
  fetch('/api/v1/needs/'+encodeURIComponent(getNeedId())+'/cancel',{method:'POST',headers:{'Accept':'application/json','X-CSRF-TOKEN':csrf}})
    .then(function(r){return r.json().then(function(j){return {s:r.status,b:j};});})
    .then(function(res){if(res.s===200){toast('{{ __('needCancelled') }}');setTimeout(loadNeed,800);}else if(res.s===409){toast('{{ __('stateConflict') }}');}else{toast('{{ __('actionFailed') }}');}})
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
