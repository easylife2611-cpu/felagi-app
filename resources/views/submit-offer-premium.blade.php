<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>{{ __('screenS011') }} — {{ __('brand') }}</title>
<meta name="theme-color" content="#003366">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Ethiopic:wght@400;500;600;700;800;900&family=Inter:wght@400;600;700;800;900&display=swap" rel="stylesheet">
<style>
:root{--navy-950:#001a33;--navy-900:#002b52;--navy-800:#003366;--navy-700:#0b4d83;--navy-600:#1a6bb0;--orange-700:#b35c00;--orange-600:#e8851d;--orange-500:#FF9933;--orange-400:#ffb366;--orange-100:#fff2e0;--ink-900:#0d1a2b;--ink-500:#3c4d61;--ink-300:#5f7185;--ink-200:#8fa0b3;--line-100:#eef2f6;--line-200:#e3e9ef;--line-300:#d5dde6;--surface:#fff;--canvas:#f4f7fa;--canvas-2:#eef3f8;--success-600:#0e6b34;--success-500:#147a3d;--success-100:#e8f5ee;--warn-600:#a86500;--warn-100:#fff7e6;--danger-600:#a32e21;--danger-500:#c0392b;--danger-100:#fdecea;--info-600:#0056d6;--info-100:#e7f1ff;--r-xs:8px;--r-sm:12px;--r-md:16px;--r-lg:22px;--r-xl:28px;--r-pill:999px;--sh-xs:0 1px 2px rgba(0,26,51,.04),0 1px 3px rgba(0,26,51,.06);--sh-sm:0 2px 6px rgba(0,26,51,.05),0 4px 12px rgba(0,26,51,.06);--sh-md:0 4px 12px rgba(0,26,51,.06),0 12px 28px rgba(0,26,51,.08);--sh-navy:0 12px 32px rgba(0,51,102,.28);--sh-orange:0 8px 20px rgba(255,153,51,.32);--f-am:'Noto Sans Ethiopic','Inter',system-ui,sans-serif;--f-en:'Inter','Noto Sans Ethiopic',system-ui,sans-serif;--ease:cubic-bezier(.16,.84,.44,1);--ease-out:cubic-bezier(.22,1,.36,1)}
*{box-sizing:border-box;margin:0;padding:0}
html,body{background:var(--canvas);color:var(--ink-900);font-family:var(--f-am);line-height:1.55;-webkit-font-smoothing:antialiased}
body{padding:0 0 100px;min-height:100vh}
a{color:inherit;text-decoration:none}
button{font-family:inherit;cursor:pointer;border:0;background:transparent;color:inherit}
input,select,textarea{font-family:inherit;font-size:inherit}
:focus-visible{outline:3px solid var(--orange-500);outline-offset:3px;border-radius:8px}
.sr-only{position:absolute;width:1px;height:1px;padding:0;margin:-1px;overflow:hidden;clip:rect(0,0,0,0);white-space:nowrap;border:0}
.sc{max-width:720px;margin:0 auto;min-height:100vh;display:flex;flex-direction:column}
.sh{flex:0 0 auto;color:#fff;padding:18px 22px 22px;position:relative;overflow:hidden;z-index:1}
.mesh{background:radial-gradient(ellipse at top right,rgba(255,153,51,.18),transparent 55%),radial-gradient(ellipse at bottom left,rgba(26,107,176,.35),transparent 60%),linear-gradient(140deg,var(--navy-950),var(--navy-800))}
.sh .tr{display:flex;align-items:center;justify-content:space-between;gap:12px;position:relative;z-index:1}
.sh .br{font-size:17px;font-weight:900;letter-spacing:-.03em;display:flex;align-items:center;gap:9px}
.ib{width:40px;height:40px;border-radius:50%;display:inline-flex;align-items:center;justify-content:center;color:#fff;background:rgba(255,255,255,.10);border:1px solid rgba(255,255,255,.08);transition:all .2s var(--ease)}
.ib:hover{background:rgba(255,255,255,.18)}
.ib svg{width:20px;height:20px;stroke:currentColor;fill:none;stroke-width:2;stroke-linecap:round;stroke-linejoin:round}
.sh .t{margin-top:14px;font-size:22px;font-weight:900;letter-spacing:-.03em;line-height:1.2;position:relative;z-index:1}
.sh .st{margin-top:4px;font-size:12.5px;opacity:.75;font-weight:500;position:relative;z-index:1}
.sh .draft{position:absolute;top:20px;right:74px;font-size:11px;opacity:.75;font-weight:700;font-family:var(--f-en);letter-spacing:.04em}
.sb{flex:1 1 auto;padding:20px 18px 40px;position:relative;z-index:1}
.need-preview{background:linear-gradient(135deg,var(--canvas-2),var(--surface));border:1px solid var(--line-200);border-radius:var(--r-md);padding:14px 16px;margin-bottom:20px}
.need-preview .lbl{font-size:10.5px;color:var(--ink-300);text-transform:uppercase;letter-spacing:.08em;font-weight:800;margin-bottom:5px;font-family:var(--f-en)}
.need-preview .title{font-size:15px;font-weight:900;color:var(--ink-900);line-height:1.3;letter-spacing:-.02em}
.need-preview .meta{font-size:11.5px;color:var(--ink-300);margin-top:5px;font-weight:600}
.fl{position:relative;margin-bottom:16px}
.fl .lbl{display:block;font-size:12.5px;font-weight:800;color:var(--ink-500);margin-bottom:7px;letter-spacing:-.005em}
.fl .lbl .req{color:var(--danger-500);font-weight:900;margin-left:2px}
.fl .lbl .opt{color:var(--ink-300);font-weight:700;font-size:11px;margin-left:5px;font-family:var(--f-en)}
.inp{width:100%;padding:13px 16px;border:1.5px solid var(--line-300);border-radius:var(--r-sm);background:var(--surface);font-size:15px;color:var(--ink-900);outline:none;transition:all .2s var(--ease);font-weight:500;min-height:50px}
.inp:focus{border-color:var(--navy-800);box-shadow:0 0 0 4px rgba(0,51,102,.08)}
.inp::placeholder{color:var(--ink-200);font-weight:400}
textarea.inp{min-height:130px;resize:vertical;line-height:1.6}
select.inp{cursor:pointer}
.row{display:flex;gap:12px;flex-wrap:wrap}
.row .col{flex:1;min-width:140px}
.row .col-currency{flex:0 0 110px}
.hint{font-size:11.5px;color:var(--ink-300);margin-top:6px;font-weight:600;font-family:var(--f-en)}
.err{color:var(--danger-600);font-size:11.5px;margin-top:6px;display:none;font-weight:800}
.err.on{display:flex;align-items:center;gap:5px}
.err.on::before{content:'⚠';font-size:13px}
.inp[aria-invalid="true"]{border-color:var(--danger-500);background:#fff8f7}
.btn{display:inline-flex;align-items:center;justify-content:center;gap:9px;height:52px;padding:0 24px;border-radius:var(--r-sm);font-weight:900;font-size:15px;line-height:1;letter-spacing:-.015em;transition:all .2s var(--ease);font-family:var(--f-am);text-align:center}
.btn:active{transform:scale(.975)}
.btn-primary{background:linear-gradient(135deg,var(--orange-500),var(--orange-600));color:#fff;box-shadow:var(--sh-orange)}
.btn-primary:hover:not(:disabled){box-shadow:0 12px 28px rgba(255,153,51,.42);transform:translateY(-1px)}
.btn-primary:disabled{opacity:.5;cursor:not-allowed}
.btn-ghost{background:var(--surface);color:var(--navy-800);border:1.5px solid var(--line-300);flex:0 0 auto}
.btn-ghost:hover{background:var(--canvas-2);border-color:var(--navy-700)}
.form-actions{position:fixed;bottom:0;left:0;right:0;background:rgba(255,255,255,.98);backdrop-filter:blur(12px);border-top:1px solid var(--line-200);padding:14px 18px;display:flex;gap:12px;max-width:720px;margin:0 auto;z-index:20;box-shadow:0 -4px 16px rgba(0,26,51,.06)}
.form-actions .btn{flex:1}
.status{margin:16px 0;padding:13px 16px;border-radius:var(--r-sm);font-size:13.5px;font-weight:800;display:none;line-height:1.5}
.status.on{display:block}
.status.error{background:var(--danger-100);color:var(--danger-600)}
.status.success{background:var(--success-100);color:var(--success-600)}
.status.warn{background:var(--warn-100);color:var(--warn-600)}
.spinner{display:inline-block;width:16px;height:16px;border:2px solid rgba(255,255,255,.4);border-top-color:#fff;border-radius:50%;animation:spin .8s linear infinite;vertical-align:-3px}
@keyframes spin{to{transform:rotate(360deg)}}
.offline-banner{display:none;background:var(--warn-100);color:var(--warn-600);padding:10px 16px;border-radius:var(--r-sm);font-size:12.5px;font-weight:800;text-align:center;margin-bottom:14px}
.offline-banner.on{display:block}
.state{padding:60px 24px;text-align:center;color:var(--ink-300)}
.state .ic{font-size:44px;margin-bottom:14px;opacity:.5}
.state h3{font-size:18px;font-weight:900;color:var(--navy-800);margin-bottom:8px}
.state p{font-size:13.5px;line-height:1.6;color:var(--ink-500);max-width:300px;margin:0 auto 16px}
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
    <span class="draft" id="draft-indicator"></span>
    <button class="ib" type="button" onclick="history.back()" aria-label="close">
      <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M18 6 6 18M6 6l12 12"/></svg>
    </button>
  </div>
  <h1 class="t" id="page-title" tabindex="-1" role="heading" aria-level="1">{{ __('screenS011') }}</h1>
  <div class="st">{{ __('submitOfferSubtitle') }}</div>
</div>
<div class="sb">
  <div class="offline-banner" id="offline-banner" role="status" aria-live="polite">{{ __('offlineBody') }}</div>
  <div class="status" id="status" role="status" aria-live="polite"></div>

  <div id="state-loading" class="state">
    <div style="display:inline-block;width:28px;height:28px;border:3px solid var(--line-200);border-top-color:var(--navy-800);border-radius:50%;animation:spin .8s linear infinite"></div>
    <p style="margin-top:12px">{{ __('loading') }}...</p>
  </div>

  <div id="state-need-error" class="state" hidden>
    <div class="ic">⚠️</div>
    <h3>{{ __('loadErrorTitle') }}</h3>
    <p>{{ __('loadErrorBody') }}</p>
    <a href="/browse" class="btn btn-primary">{{ __('backToBrowse') }}</a>
  </div>

  <div id="state-form" hidden>
    <div class="need-preview" id="need-preview" hidden>
      <div class="lbl">{{ __('needDetails') }}</div>
      <div class="title" id="need-title"></div>
      <div class="meta" id="need-meta"></div>
    </div>

    <form id="offer-form" novalidate>
      <div class="fl">
        <label class="lbl" for="offered_price">{{ __('offeredPrice') }} <span class="req">*</span></label>
        <div class="row">
          <div class="col">
            <input type="number" id="offered_price" name="offered_price" class="inp" min="0" step="0.01" required placeholder="{{ __('offeredPricePlaceholder') }}">
          </div>
          <div class="col-currency">
            <select id="currency" name="currency" class="inp">
              <option value="ETB">ETB</option>
              <option value="USD">USD</option>
            </select>
          </div>
        </div>
        <div class="err" id="err-offered_price"></div>
      </div>

      <div class="fl">
        <label class="lbl" for="proposal_message">{{ __('proposalMessage') }} <span class="req">*</span></label>
        <textarea id="proposal_message" name="proposal_message" class="inp" maxlength="10000" required minlength="20" placeholder="{{ __('proposalMessagePlaceholder') }}"></textarea>
        <div class="hint"><span id="msg-count">0</span> / 10000</div>
        <div class="err" id="err-proposal_message"></div>
      </div>

      <div class="fl">
        <label class="lbl" for="delivery_time_text">{{ __('deliveryTime') }} <span class="opt">{{ __('optional') }}</span></label>
        <input type="text" id="delivery_time_text" name="delivery_time_text" class="inp" maxlength="255" placeholder="{{ __('deliveryTimePlaceholder') }}" autocomplete="off">
        <div class="err" id="err-delivery_time_text"></div>
      </div>

      <div class="fl">
        <label class="lbl" for="availability_text">{{ __('availability') }} <span class="opt">{{ __('optional') }}</span></label>
        <input type="text" id="availability_text" name="availability_text" class="inp" maxlength="255" placeholder="{{ __('availabilityPlaceholder') }}" autocomplete="off">
        <div class="err" id="err-availability_text"></div>
      </div>

      <div class="fl">
        <label class="lbl" for="additional_notes">{{ __('additionalNotes') }} <span class="opt">{{ __('optional') }}</span></label>
        <textarea id="additional_notes" name="additional_notes" class="inp" maxlength="10000" placeholder="{{ __('additionalNotesPlaceholder') }}"></textarea>
        <div class="err" id="err-additional_notes"></div>
      </div>
    </form>
  </div>
</div>

<div class="form-actions" id="form-actions" hidden>
  <button type="button" class="btn btn-ghost" onclick="window.location.href=document.getElementById('back-btn').href">{{ __('cancel') }}</button>
  <button type="submit" form="offer-form" class="btn btn-primary" id="submit-btn">{{ __('submit') }}</button>
</div>
</div>
<script>
function felagiLocalizedName(obj){if(!obj)return '';var am=document.documentElement.lang==='am';return am?(obj.name_am||obj.name_en||obj.slug||''):(obj.name_en||obj.name_am||obj.slug||'');}
(function(){
'use strict';
var csrf=(document.querySelector('meta[name="csrf-token"]')||{}).content||'';
var LS_TOKEN='felagi_token';
var DRAFT_PREFIX='felagi_draft_s011_';
var META_PREFIX='felagi_meta_s011_';
var FIELDS=['offered_price','currency','proposal_message','delivery_time_text','availability_text','additional_notes'];
var needId='';
var needData=null;
function $(id){return document.getElementById(id);}
function getToken(){return 'session';}
function esc(s){return String(s==null?'':s).replace(/[&<>"']/g,function(c){return {'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c];});}
function getNeedId(){var parts=window.location.pathname.split('/').filter(Boolean);return parts.length>=2?parts[1]:'';}
function showStatus(type,msg){var el=$('status');el.className='status on '+type;el.textContent=msg;}
function hideStatus(){var el=$('status');el.className='status';el.textContent='';}
function clearErrors(){
  Array.prototype.forEach.call(document.querySelectorAll('.err'),function(e){e.className='err';e.textContent='';});
  Array.prototype.forEach.call(document.querySelectorAll('input,select,textarea'),function(e){e.removeAttribute('aria-invalid');e.removeAttribute('aria-describedby');});
}
function showErr(field,msg){
  var el=$('err-'+field);if(!el)return;
  el.textContent=msg;el.className='err on';el.setAttribute('role','alert');
  var inp=$(field);if(inp){inp.setAttribute('aria-invalid','true');inp.setAttribute('aria-describedby','err-'+field);}
}
function showValidationErrors(errors){
  clearErrors();
  Object.keys(errors).forEach(function(k){var msgs=errors[k];showErr(k,Array.isArray(msgs)?msgs[0]:msgs);});
}
function showOnly(name){
  ['state-loading','state-need-error','state-form'].forEach(function(id){
    var el=$(id);if(el)el.hidden=(id!=='state-'+name);
  });
  var fa=$('form-actions');if(fa)fa.hidden=(name!=='form');
}
function updateDraftIndicator(show){var el=$('draft-indicator');if(el)el.textContent=show?'{{ __('draftSaved') }}':'';}
function uuidv4(){
  if(window.crypto&&crypto.randomUUID)return crypto.randomUUID();
  return 'xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx'.replace(/[xy]/g,function(c){var r=Math.random()*16|0,v=c==='x'?r:(r&0x3|0x8);return v.toString(16);});
}
async function sha256Hex(text){
  if(window.crypto&&crypto.subtle&&crypto.subtle.digest){
    var buf=new TextEncoder().encode(text);
    var h=await crypto.subtle.digest('SHA-256',buf);
    return Array.from(new Uint8Array(h)).map(function(b){return b.toString(16).padStart(2,'0');}).join('');
  }
  var h=0;for(var i=0;i<text.length;i++){h=((h<<5)-h)+text.charCodeAt(i);h|=0;}
  return ('00000000'+Math.abs(h).toString(16)).slice(-8).repeat(8);
}
function loadDraftMeta(){try{var raw=localStorage.getItem(META_PREFIX+needId);if(!raw)return {};return JSON.parse(raw)||{};}catch(e){return {};}}
function saveDraftMeta(meta){try{var cur=loadDraftMeta();for(var k in meta){cur[k]=meta[k];}localStorage.setItem(META_PREFIX+needId,JSON.stringify(cur));}catch(e){}}
function clearDraftMeta(){try{localStorage.removeItem(META_PREFIX+needId);}catch(e){}}
function collectForm(){
  var data={};
  FIELDS.forEach(function(f){
    var el=$(f);if(!el)return;
    var v=el.value;
    if(f==='offered_price'){data[f]=v===''?null:parseFloat(v);}
    else if(f==='currency'){data[f]=v||'ETB';}
    else{data[f]=v===''?null:v;}
  });
  return data;
}
function saveDraft(){try{var data=collectForm();localStorage.setItem(DRAFT_PREFIX+needId,JSON.stringify({data:data,at:Date.now()}));updateDraftIndicator(true);}catch(e){}}
function loadDraft(){
  try{
    var raw=localStorage.getItem(DRAFT_PREFIX+needId);
    if(!raw)return false;
    var parsed=JSON.parse(raw);
    if(!parsed||!parsed.data)return false;
    FIELDS.forEach(function(f){var el=$(f);if(!el)return;var v=parsed.data[f];if(v!=null)el.value=v;});
    return true;
  }catch(e){return false;}
}
function clearDraft(){try{localStorage.removeItem(DRAFT_PREFIX+needId);clearDraftMeta();updateDraftIndicator(false);}catch(e){}}
function updateMsgCount(){var c=$('proposal_message').value.length;$('msg-count').textContent=c;}
function renderNeedPreview(need){
  $('need-preview').hidden=false;
  $('need-title').textContent=need.title||'';
  var cat=need.category||{};var catName=felagiLocalizedName(cat);
  var meta=[];
  if(catName)meta.push(catName);
  if(need.location_text)meta.push('📍 '+esc(need.location_text));
  if(need.budget_min!=null||need.budget_max!=null){
    var cur=need.currency||'ETB';
    var mn=need.budget_min!=null?Number(need.budget_min):null;
    var mx=need.budget_max!=null?Number(need.budget_max):null;
    var b='';
    if(mn!=null&&mx!=null&&mn!==mx)b='<bdi>'+cur+' '+mn+' - '+mx+'</bdi>';
    else b='<bdi>'+cur+' '+(mn!=null?mn:mx)+'</bdi>';
    meta.push(b);
  }
  $('need-meta').innerHTML=meta.join(' · ');
}
function loadNeed(){
  needId=getNeedId();
  if(!needId){showOnly('need-error');return;}
  var h={'Accept':'application/json'};
  fetch('/api/v1/needs/'+encodeURIComponent(needId),{headers:h})
    .then(function(r){
      if(r.status===404)throw new Error('notfound');
      if(r.status===401)throw new Error('auth');
      if(!r.ok)throw new Error('HTTP '+r.status);
      return r.json();
    })
    .then(function(j){
      var need=(j&&j.data)?j.data:j;
      if(!need||!need.id){showOnly('need-error');return;}
      if(need.is_owner){
        showStatus('error','{{ __('cannotOfferOwnNeed') }}');
        setTimeout(function(){window.location.href='/needs/'+needId;},1800);
        return;
      }
      if(need.status!=='OPEN'){
        showStatus('warn','{{ __('needNotOpen') }}');
        setTimeout(function(){window.location.href='/needs/'+needId;},1800);
        return;
      }
      needData=need;
      renderNeedPreview(need);
      $('back-btn').href='/needs/'+encodeURIComponent(needId);
      loadDraft();updateMsgCount();
      showOnly('form');
      var hEl=document.getElementById('page-title');
      if(hEl){try{hEl.focus();}catch(e){}}
    })
    .catch(function(e){
      var m=String(e.message||e);
      if(m==='auth'){localStorage.removeItem(LS_TOKEN);window.location.href='/';return;}
      showOnly('need-error');
    });
}
function clientValidate(){
  clearErrors();
  var ok=true;
  var price=$('offered_price').value;
  if(price===''||isNaN(parseFloat(price))||parseFloat(price)<0){showErr('offered_price','{{ __('required') }}');ok=false;}
  var msg=$('proposal_message').value.trim();
  if(msg.length<20){showErr('proposal_message','{{ __('minChars20') }}');ok=false;}
  return ok;
}
async function submitForm(e){
  e.preventDefault();
  hideStatus();
  if(!clientValidate()){showStatus('error','{{ __('validationFailed') }}');return;}
  var btn=$('submit-btn');
  btn.disabled=true;
  btn.innerHTML='<span class="spinner"></span>{{ __('saving') }}';
  var meta=loadDraftMeta();
  var draftId=meta.draft_id||uuidv4();
  var idemKey=meta.idempotency_key||uuidv4();
  var version=(meta.draft_version||0)+1;
  saveDraftMeta({draft_id:draftId,draft_version:version,idempotency_key:idemKey});
  var payload=collectForm();
  payload.need_id=needId;
  payload.idempotency_key=idemKey;
  payload.draft_id=draftId;
  payload.draft_version=version;
  var hash='';try{hash=await sha256Hex(JSON.stringify(payload));}catch(e){}
  payload.draft_hash=hash;
  fetch('/api/v1/offer-submissions',{
    method:'POST',
    headers:{'Accept':'application/json','Content-Type':'application/json','X-CSRF-TOKEN':csrf},
    body:JSON.stringify(payload)
  })
  .then(function(r){return r.json().then(function(j){return {status:r.status,body:j};});})
  .then(function(res){
    if(res.status===201){
      clearDraft();showStatus('success','{{ __('offerSubmitted') }}');
      var offerId=res.body.data&&res.body.data.offer_id;
      setTimeout(function(){window.location.href=offerId?('/offers/'+offerId):('/needs/'+needId);},700);
      return;
    }
    if(res.status===202){
      var sid=res.body.data&&res.body.data.id;
      saveDraftMeta({draft_id:draftId,draft_version:version,idempotency_key:idemKey,submission_id:sid});
      showStatus('success','{{ __('paymentRequired') }}');
      setTimeout(function(){window.location.href='/needs/'+encodeURIComponent(needId)+'/offers/unlock?submission_id='+encodeURIComponent(sid||'');},700);
      return;
    }
    if(res.status===503){showStatus('error','{{ __('policyUnknown') }}');return;}
    if(res.status===422){
      var b=res.body||{};
      if(b.errors){showValidationErrors(b.errors);}
      var code=(b.code||b.error||'').toString().toUpperCase();
      if(code.indexOf('DEADLINE')!==-1){showStatus('warn','{{ __('offerDeadlinePassed') }}');}
      else{showStatus('error','{{ __('validationFailed') }}');}
      return;
    }
    if(res.status===409){
      var b409=res.body||{};
      var code409=(b409.code||b409.error||'').toString().toUpperCase();
      if(code409.indexOf('EXISTS')!==-1||code409.indexOf('DUPLICATE')!==-1){showStatus('warn','{{ __('offerAlreadyExists') }}');}
      else if(code409.indexOf('STATE')!==-1||code409.indexOf('OPEN')!==-1){showStatus('warn','{{ __('needNotOpen') }}');}
      else{showStatus('warn','{{ __('stateConflict') }}');}
      return;
    }
    if(res.status===403){showStatus('error','{{ __('cannotOfferOwnNeed') }}');return;}
    if(res.status===401){localStorage.removeItem(LS_TOKEN);localStorage.removeItem('felagi_user');window.location.href='/';return;}
    if(res.status===429){showStatus('error','{{ __('rateLimited') }}');return;}
    showStatus('error','{{ __('saveFailed') }}');
  })
  .catch(function(err){console.error('[S011] submit',err);showStatus('error','{{ __('saveFailed') }}');})
  .finally(function(){btn.disabled=false;btn.textContent='{{ __('submit') }}';});
}
$('offer-form').addEventListener('submit',submitForm);
FIELDS.forEach(function(f){var el=$(f);if(!el)return;el.addEventListener('input',saveDraft);el.addEventListener('change',saveDraft);});
$('proposal_message').addEventListener('input',updateMsgCount);
window.addEventListener('offline',function(){$('offline-banner').className='offline-banner on';});
window.addEventListener('online',function(){$('offline-banner').className='offline-banner';});
if(navigator.onLine===false){$('offline-banner').className='offline-banner on';}
loadNeed();
})();
</script>
</body>
</html>
