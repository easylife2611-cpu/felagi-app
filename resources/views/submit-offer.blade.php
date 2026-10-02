<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>{{ __('submitOffer') }} — {{ __('brand') }}</title>
<style>
*{box-sizing:border-box;margin:0;padding:0}
body{font-family:system-ui,-apple-system,sans-serif;background:#F4F6F8;color:#192431;line-height:1.5;min-height:100vh}
header{background:#003366;color:#fff;height:64px;padding:0 16px;display:flex;align-items:center;gap:12px;position:sticky;top:0;z-index:30}
header a{color:#fff;text-decoration:none;font-size:20px;padding:8px;border-radius:6px;line-height:1}
header a:hover{background:rgba(255,255,255,.12)}
header .brand{font-size:18px;font-weight:600}
header .draft{font-size:12px;opacity:.75;margin-left:auto}
main{max-width:720px;margin:0 auto;padding:16px 16px 100px}
h2{font-size:22px;margin-bottom:6px;color:#003366}
.subtitle{color:#586675;font-size:14px;margin-bottom:16px}
.need-preview{background:#fff;border-radius:10px;padding:14px;margin-bottom:20px;box-shadow:0 1px 3px rgba(0,0,0,.08)}
.need-preview .lbl{font-size:11px;color:#586675;text-transform:uppercase;letter-spacing:.5px;font-weight:600;margin-bottom:4px}
.need-preview .title{font-size:16px;font-weight:600;color:#192431;line-height:1.3}
.need-preview .meta{font-size:12px;color:#586675;margin-top:6px}
.form-group{margin-bottom:18px}
label{display:block;font-size:14px;font-weight:600;margin-bottom:6px;color:#192431}
label .req{color:#c62828}
label .opt{color:#586675;font-weight:400;font-size:12px;margin-left:4px}
input[type=text],input[type=number],select,textarea{width:100%;padding:11px 13px;border:1px solid #d0d7de;border-radius:8px;font-family:inherit;font-size:15px;background:#fff;transition:border .15s,box-shadow .15s}
input:focus,select:focus,textarea:focus{outline:none;border-color:#003366;box-shadow:0 0 0 3px rgba(0,51,102,.1)}
textarea{min-height:120px;resize:vertical;line-height:1.5}
select{cursor:pointer}
.row{display:flex;gap:12px;flex-wrap:wrap}
.row .col{flex:1;min-width:140px}
.row .col-currency{flex:0 0 110px}
.hint{font-size:12px;color:#586675;margin-top:4px}
.err{color:#c62828;font-size:12px;margin-top:4px;display:none}
.err.on{display:block}
.form-actions{position:fixed;bottom:0;left:0;right:0;background:#fff;border-top:1px solid #eef1f4;padding:12px 16px;display:flex;gap:12px;max-width:720px;margin:0 auto;z-index:20;box-shadow:0 -1px 3px rgba(0,0,0,.04)}
.btn{flex:1;padding:12px 20px;border-radius:8px;font-size:15px;font-weight:600;cursor:pointer;border:none;font-family:inherit;text-decoration:none;text-align:center;transition:background .15s}
.btn.primary{background:#003366;color:#fff}
.btn.primary:hover:not(:disabled){background:#002a52}
.btn.primary:disabled{opacity:.5;cursor:not-allowed}
.btn.ghost{background:#eef1f4;color:#192431;flex:0 0 auto;padding:12px 20px}
.btn.ghost:hover{background:#e0e4e8}
.status{margin:16px 0;padding:12px 16px;border-radius:8px;font-size:14px;display:none}
.status.on{display:block}
.status.error{background:#ffebee;color:#b71c1c}
.status.success{background:#e8f5e9;color:#1b5e20}
.status.warn{background:#fff8e1;color:#8a6d00}
.spinner{display:inline-block;width:14px;height:14px;border:2px solid rgba(255,255,255,.4);border-top-color:#fff;border-radius:50%;animation:spin .8s linear infinite;vertical-align:-2px;margin-right:6px}
@keyframes spin{to{transform:rotate(360deg)}}
.offline-banner{background:#fff8e1;border:1px solid #ffe082;color:#8a6d00;padding:10px 14px;border-radius:8px;font-size:13px;margin-bottom:16px;display:none}
.offline-banner.on{display:block}
.state{padding:60px 20px;text-align:center;color:#586675}
.state h3{color:#003366;font-size:18px;margin:0 0 8px}
.state p{margin:0 0 16px}
.state .btn{display:inline-block;max-width:220px;flex:none;padding:12px 20px;text-decoration:none}
</style>
</head>
<body>
<header>
<a href="#" id="back-btn" title="{{ __('back') }}">&#8592;</a>
<span class="brand">{{ __('brand') }}</span>
<span class="draft" id="draft-indicator"></span>
</header>

<main>
<h2>{{ __('submitOffer') }}</h2>
<p class="subtitle">{{ __('submitOfferSubtitle') }}</p>

<div class="offline-banner" id="offline-banner">{{ __('offlineBody') }}</div>
<div class="status" id="status"></div>

<div id="state-loading" class="state">
<div class="spinner" style="border-color:#d0d7de;border-top-color:#003366;width:24px;height:24px;border-width:3px"></div>
<p style="margin-top:12px">{{ __('loading') }}...</p>
</div>

<div id="state-need-error" class="state" hidden>
<h3>{{ __('loadErrorTitle') }}</h3>
<p>{{ __('loadErrorBody') }}</p>
<a href="/browse" class="btn primary">{{ __('backToBrowse') }}</a>
</div>

<div id="state-form" hidden>

<div class="need-preview" id="need-preview" hidden>
<div class="lbl">{{ __('needDetails') }}</div>
<div class="title" id="need-title"></div>
<div class="meta" id="need-meta"></div>
</div>

<form id="offer-form" novalidate>

<div class="form-group">
<label for="offered_price">{{ __('offeredPrice') }} <span class="req">*</span></label>
<div class="row">
<div class="col">
<input type="number" id="offered_price" name="offered_price" min="0" step="0.01" required placeholder="{{ __('offeredPricePlaceholder') }}">
</div>
<div class="col-currency">
<select id="currency" name="currency">
<option value="ETB">ETB</option>
<option value="USD">USD</option>
</select>
</div>
</div>
<div class="err" id="err-offered_price"></div>
</div>

<div class="form-group">
<label for="proposal_message">{{ __('proposalMessage') }} <span class="req">*</span></label>
<textarea id="proposal_message" name="proposal_message" maxlength="10000" required minlength="20" placeholder="{{ __('proposalMessagePlaceholder') }}"></textarea>
<div class="hint"><span id="msg-count">0</span> / 10000</div>
<div class="err" id="err-proposal_message"></div>
</div>

<div class="form-group">
<label for="delivery_time_text">{{ __('deliveryTime') }} <span class="opt">{{ __('optional') }}</span></label>
<input type="text" id="delivery_time_text" name="delivery_time_text" maxlength="255" placeholder="{{ __('deliveryTimePlaceholder') }}" autocomplete="off">
<div class="err" id="err-delivery_time_text"></div>
</div>

<div class="form-group">
<label for="availability_text">{{ __('availability') }} <span class="opt">{{ __('optional') }}</span></label>
<input type="text" id="availability_text" name="availability_text" maxlength="255" placeholder="{{ __('availabilityPlaceholder') }}" autocomplete="off">
<div class="err" id="err-availability_text"></div>
</div>

<div class="form-group">
<label for="additional_notes">{{ __('additionalNotes') }} <span class="opt">{{ __('optional') }}</span></label>
<textarea id="additional_notes" name="additional_notes" maxlength="10000" placeholder="{{ __('additionalNotesPlaceholder') }}"></textarea>
<div class="err" id="err-additional_notes"></div>
</div>

</form>
</div>
</main>

<div class="form-actions" id="form-actions" hidden>
<button type="button" class="btn ghost" onclick="window.location.href=document.getElementById('back-btn').href">{{ __('cancel') }}</button>
<button type="submit" form="offer-form" class="btn primary" id="submit-btn">{{ __('submit') }}</button>
</div>
<script>
(function(){
'use strict';
var csrf=(document.querySelector('meta[name="csrf-token"]')||{}).content||'';
var LS_TOKEN='felagi_token';
var DRAFT_PREFIX='felagi_draft_s011_';
var FIELDS=['offered_price','currency','proposal_message','delivery_time_text','availability_text','additional_notes'];
var needId='';
var needData=null;

function $(id){return document.getElementById(id);}
function getToken(){return 'session'; /* L305d */}
function esc(s){return String(s==null?'':s).replace(/[&<>"']/g,function(c){return {'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c];});}

function getNeedId(){
  var parts=window.location.pathname.split('/').filter(Boolean);
  // /needs/{id}/offers/new
  return parts.length>=2?parts[1]:'';
}

function showStatus(type,msg){
  var el=$('status');
  el.className='status on '+type;
  el.textContent=msg;
}
function hideStatus(){var el=$('status');el.className='status';el.textContent='';}

function clearErrors(){
  Array.prototype.forEach.call(document.querySelectorAll('.err'),function(e){e.className='err';e.textContent='';});
}
function showErr(field,msg){
  var el=$('err-'+field);
  if(!el)return;
  el.textContent=msg;
  el.className='err on';
}
function showValidationErrors(errors){
  clearErrors();
  Object.keys(errors).forEach(function(k){
    var msgs=errors[k];
    showErr(k,Array.isArray(msgs)?msgs[0]:msgs);
  });
}

function showOnly(name){
  ['state-loading','state-need-error','state-form'].forEach(function(id){
    var el=$(id); if(el)el.hidden=(id!==((name==='content')?'content':'state-'+name));
  });
  var fa=$('form-actions');
  if(fa)fa.hidden=(name!=='form');
}

function updateDraftIndicator(show){
  var el=$('draft-indicator');
  if(el)el.textContent=show?'{{ __('draftSaved') }}':'';
}

function collectForm(){
  var data={};
  FIELDS.forEach(function(f){
    var el=$(f); if(!el)return;
    var v=el.value;
    if(f==='offered_price'){data[f]=v===''?null:parseFloat(v);}
    else if(f==='currency'){data[f]=v||'ETB';}
    else{data[f]=v===''?null:v;}
  });
  return data;
}

function saveDraft(){
  try{
    var data=collectForm();
    localStorage.setItem(DRAFT_PREFIX+needId,JSON.stringify({data:data,at:Date.now()}));
    updateDraftIndicator(true);
  }catch(e){}
}

function loadDraft(){
  try{
    var raw=localStorage.getItem(DRAFT_PREFIX+needId);
    if(!raw)return false;
    var parsed=JSON.parse(raw);
    if(!parsed||!parsed.data)return false;
    FIELDS.forEach(function(f){
      var el=$(f); if(!el)return;
      var v=parsed.data[f];
      if(v!=null)el.value=v;
    });
    return true;
  }catch(e){return false;}
}

function clearDraft(){
  try{localStorage.removeItem(DRAFT_PREFIX+needId);updateDraftIndicator(false);}catch(e){}
}

function updateMsgCount(){
  var c=$('proposal_message').value.length;
  $('msg-count').textContent=c;
}

function renderNeedPreview(need){
  $('need-preview').hidden=false;
  $('need-title').textContent=need.title||'';
  var cat=need.category||{};
  var catName=cat.name_en||cat.name_am||cat.slug||'';
  var meta=[];
  if(catName)meta.push(catName);
  if(need.location_text)meta.push('&#128205; '+esc(need.location_text));
  if(need.budget_min!=null||need.budget_max!=null){
    var cur=need.currency||'ETB';
    var mn=need.budget_min!=null?Number(need.budget_min):null;
    var mx=need.budget_max!=null?Number(need.budget_max):null;
    var b='';
    if(mn!=null&&mx!=null&&mn!==mx)b=cur+' '+mn+' - '+mx;
    else b=cur+' '+(mn!=null?mn:mx);
    meta.push(b);
  }
  $('need-meta').innerHTML=meta.join(' · ');
}

function loadNeed(){
  needId=getNeedId();
  if(!needId){showOnly('need-error');return;}

  var token=getToken();
  /* L305d */

  var h={'Accept':'application/json'}/* L305d */;
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

      // Set back button
      $('back-btn').href='/needs/'+encodeURIComponent(needId);

      // Load draft
      loadDraft();
      updateMsgCount();

      showOnly('form');
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
  if(price===''||isNaN(parseFloat(price))||parseFloat(price)<0){
    showErr('offered_price','{{ __('required') }}');
    ok=false;
  }
  var msg=$('proposal_message').value.trim();
  if(msg.length<20){
    showErr('proposal_message','{{ __('minChars20') }}');
    ok=false;
  }
  return ok;
}

function submitForm(e){
  e.preventDefault();
  hideStatus();
  if(!clientValidate()){
    showStatus('error','{{ __('validationFailed') }}');
    return;
  }
  var token=getToken();
  /* L305d */

  var btn=$('submit-btn');
  btn.disabled=true;
  btn.innerHTML='<span class="spinner"></span>{{ __('saving') }}';

  var payload=collectForm();
  fetch('/api/v1/needs/'+encodeURIComponent(needId)+'/offers',{
    method:'POST',
    headers:{
      'Accept':'application/json',
      'Content-Type':'application/json',
      'X-CSRF-TOKEN':csrf
    },
    body:JSON.stringify(payload)
  })
  .then(function(r){return r.json().then(function(j){return {status:r.status,body:j};});})
  .then(function(res){
    if(res.status===201||res.status===200){
      clearDraft();
      showStatus('success','{{ __('offerSubmitted') }}');
      var offerId=(res.body.data&&res.body.data.id)||(res.body.id);
      setTimeout(function(){
        window.location.href=offerId?('/offers/'+offerId):('/needs/'+needId);
      },900);
      return;
    }
    if(res.status===422){
      var b=res.body||{};
      if(b.errors){showValidationErrors(b.errors);}
      // Deadline-passed or other business rule
      var code=(b.code||b.error||'').toString().toUpperCase();
      if(code.indexOf('DEADLINE')!==-1){
        showStatus('warn','{{ __('offerDeadlinePassed') }}');
      }else{
        showStatus('error','{{ __('validationFailed') }}');
      }
      return;
    }
    if(res.status===409){
      var b409=res.body||{};
      var code409=(b409.code||b409.error||'').toString().toUpperCase();
      if(code409.indexOf('EXISTS')!==-1||code409.indexOf('DUPLICATE')!==-1){
        showStatus('warn','{{ __('offerAlreadyExists') }}');
      }else if(code409.indexOf('STATE')!==-1||code409.indexOf('OPEN')!==-1){
        showStatus('warn','{{ __('needNotOpen') }}');
      }else{
        showStatus('warn','{{ __('stateConflict') }}');
      }
      return;
    }
    if(res.status===403){
      showStatus('error','{{ __('cannotOfferOwnNeed') }}');
      return;
    }
    if(res.status===401){
      localStorage.removeItem(LS_TOKEN);
      localStorage.removeItem('felagi_user');
      window.location.href='/';
      return;
    }
    if(res.status===429){
      showStatus('error','{{ __('rateLimited') }}');
      return;
    }
    showStatus('error','{{ __('saveFailed') }}');
  })
  .catch(function(err){
    console.error('[S011] submit',err);
    showStatus('error','{{ __('saveFailed') }}');
  })
  .then(function(){
    btn.disabled=false;
    btn.textContent='{{ __('submit') }}';
  });
}

// Events
$('offer-form').addEventListener('submit',submitForm);
FIELDS.forEach(function(f){
  var el=$(f); if(!el)return;
  el.addEventListener('input',saveDraft);
  el.addEventListener('change',saveDraft);
});
$('proposal_message').addEventListener('input',updateMsgCount);

window.addEventListener('offline',function(){$('offline-banner').className='offline-banner on';});
window.addEventListener('online',function(){$('offline-banner').className='offline-banner';});

if(navigator.onLine===false){$('offline-banner').className='offline-banner on';}

// Boot
loadNeed();
})();
</script>
</body>
</html>
