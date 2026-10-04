<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>{{ __('screenS005') }} — {{ __('brand') }}</title>
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
.subtitle{color:#586675;font-size:14px;margin-bottom:20px}
.form-group{margin-bottom:18px}
label{display:block;font-size:14px;font-weight:600;margin-bottom:6px;color:#192431}
label .req{color:#c62828}
label .opt{color:#586675;font-weight:400;font-size:12px;margin-left:4px}
input[type=text],input[type=number],input[type=datetime-local],input[type=file],select,textarea{width:100%;padding:11px 13px;border:1px solid #d0d7de;border-radius:8px;font-family:inherit;font-size:15px;background:#fff;transition:border .15s,box-shadow .15s}
input:focus,select:focus,textarea:focus{outline:none;border-color:#003366;box-shadow:0 0 0 3px rgba(0,51,102,.1)}
textarea{min-height:120px;resize:vertical;line-height:1.5}
select{cursor:pointer}
.row{display:flex;gap:12px;flex-wrap:wrap}
.row .col{flex:1;min-width:140px}
.hint{font-size:12px;color:#586675;margin-top:4px}
.err{color:#c62828;font-size:12px;margin-top:4px;display:none}
.err.on{display:block}
.checkbox-row{display:flex;gap:10px;align-items:flex-start;padding:14px;background:#fff8e1;border:1px solid #ffe082;border-radius:8px;margin-bottom:18px}
.checkbox-row input[type=checkbox]{margin-top:4px;flex:0 0 auto;width:auto}
.checkbox-row label{font-weight:400;font-size:13px;color:#5f4900;margin:0;line-height:1.5}
.form-actions{position:fixed;bottom:0;left:0;right:0;background:#fff;border-top:1px solid #eef1f4;padding:12px 16px;display:flex;gap:12px;max-width:720px;margin:0 auto;z-index:20;box-shadow:0 -1px 3px rgba(0,0,0,.04)}
.btn{flex:1;padding:12px 20px;border-radius:8px;font-size:15px;font-weight:600;cursor:pointer;border:none;font-family:inherit;text-decoration:none;text-align:center;transition:background .15s}
.btn.primary{background:#003366;color:#fff}
.btn.primary:hover:not(:disabled){background:#002a52}
.btn.primary:disabled{opacity:.5;cursor:not-allowed}
.btn.ghost{background:#eef1f4;color:#192431;flex:0 0 auto;padding:12px 20px}
.status{margin:16px 0;padding:12px 16px;border-radius:8px;font-size:14px;display:none}
.status.on{display:block}
.status.error{background:#ffebee;color:#b71c1c}
.status.success{background:#e8f5e9;color:#1b5e20}
.status.info{background:#e3f2fd;color:#0d47a1}
.spinner{display:inline-block;width:14px;height:14px;border:2px solid rgba(255,255,255,.4);border-top-color:#fff;border-radius:50%;animation:spin .8s linear infinite;vertical-align:-2px;margin-right:6px}
@keyframes spin{to{transform:rotate(360deg)}}
.offline-banner{background:#fff8e1;border:1px solid #ffe082;color:#8a6d00;padding:10px 14px;border-radius:8px;font-size:13px;margin-bottom:16px;display:none}
.offline-banner.on{display:block}

:focus-visible{outline:3px solid #1b5e20;outline-offset:2px;border-radius:6px}
#page-title:focus{outline:none}
.sr-only{position:absolute;width:1px;height:1px;padding:0;margin:-1px;overflow:hidden;clip:rect(0,0,0,0);white-space:nowrap;border:0}
</style>
</head>
<body>
<header>
<a href="/browse" title="{{ __('back') }}">&#8592;</a>
<span class="brand">{{ __('brand') }}</span>
<span class="draft" id="draft-indicator"></span>
</header>

<main role="main" aria-labelledby="page-title">
<h2 id="page-title" tabindex="-1">{{ __('createNeed') }}</h2>
<p class="subtitle">{{ __('createNeedSubtitle') }}</p>

<div class="offline-banner" id="offline-banner">{{ __('offlineBody') }}</div>
<div class="status" id="status"></div>

<form id="need-form" novalidate>

<div class="form-group">
<label for="title">{{ __('needTitle') }} <span class="req">*</span></label>
<input type="text" id="title" name="title" maxlength="255" required minlength="5" placeholder="{{ __('needTitlePlaceholder') }}" autocomplete="off">
<div class="err" id="err-title"></div>
</div>

<div class="form-group">
<label for="category_id">{{ __('category') }} <span class="req">*</span></label>
<select id="category_id" name="category_id" required>
<option value="">{{ __('selectCategory') }}</option>
</select>
<div class="err" id="err-category_id"></div>
</div>

<div class="form-group">
<label for="description">{{ __('description') }} <span class="req">*</span></label>
<textarea id="description" name="description" maxlength="10000" required minlength="20" placeholder="{{ __('descriptionPlaceholder') }}"></textarea>
<div class="hint"><span id="desc-count">0</span> / 10000</div>
<div class="err" id="err-description"></div>
</div>

<div class="form-group">
<label for="location_text">{{ __('location') }} <span class="opt">{{ __('optional') }}</span></label>
<input type="text" id="location_text" name="location_text" maxlength="255" placeholder="{{ __('locationPlaceholder') }}" autocomplete="off">
<div class="err" id="err-location_text"></div>
</div>

<div class="form-group">
<label>{{ __('budget') }} <span class="opt">{{ __('optional') }}</span></label>
<div class="row">
<div class="col">
<input type="number" id="budget_min" name="budget_min" min="0" step="0.01" placeholder="{{ __('budgetMin') }}">
</div>
<div class="col">
<input type="number" id="budget_max" name="budget_max" min="0" step="0.01" placeholder="{{ __('budgetMax') }}">
</div>
<div class="col" style="max-width:120px">
<select id="currency" name="currency" aria-label="Currency">
<option value="ETB">ETB</option>
<option value="USD">USD</option>
</select>
</div>
</div>
<div class="err" id="err-budget_min"></div>
<div class="err" id="err-budget_max"></div>
</div>

<div class="form-group">
<label for="quantity">{{ __('quantity') }} <span class="opt">{{ __('optional') }}</span></label>
<input type="number" id="quantity" name="quantity" min="0.01" step="0.01" placeholder="{{ __('quantityPlaceholder') }}">
<div class="err" id="err-quantity"></div>
</div>

<div class="form-group">
<label for="deadline_at">{{ __('deadline') }} <span class="opt">{{ __('optional') }}</span></label>
<input type="datetime-local" id="deadline_at" name="deadline_at" min="{{ now()->addMinutes(5)->format('Y-m-d\TH:i') }}">
<div class="err" id="err-deadline_at"></div>
</div>

<div class="form-group">
<label for="offer_deadline_at">{{ __('offerDeadline') }} <span class="opt">{{ __('optional') }}</span></label>
<input type="datetime-local" id="offer_deadline_at" name="offer_deadline_at" min="{{ now()->addMinutes(5)->format('Y-m-d\TH:i') }}">
<div class="hint">{{ __('offerDeadlineHint') }}</div>
<div class="err" id="err-offer_deadline_at"></div>
</div>

<div class="checkbox-row">
<input type="checkbox" id="telegram_ack" name="telegram_publication_acknowledged">
<label for="telegram_ack">{{ __('telegramConsent') }}</label>
</div>
<div class="err" id="err-telegram_publication_acknowledged"></div>

</form>
</main>

<div class="form-actions">
<a href="/browse" class="btn ghost">{{ __('cancel') }}</a>
<button type="submit" form="need-form" class="btn primary" id="submit-btn">{{ __('continue') }}</button>
</div>
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
var csrf = (document.querySelector('meta[name="csrf-token"]')||{}).content||'';
var LS_TOKEN='felagi_token';
var DRAFT_KEY='felagi_draft_s005';
var FIELDS=['title','category_id','description','location_text','budget_min','budget_max','currency','quantity','deadline_at','offer_deadline_at'];

function $(id){return document.getElementById(id);}
function getToken(){return 'session'; /* L305d */}
function esc(s){return String(s==null?'':s).replace(/[&<>"']/g,function(c){return {'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c];});}

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
    showErr(k, Array.isArray(msgs)?msgs[0]:msgs);
  });
}

function loadCategories(){
  fetch('/api/v1/categories',{credentials:'same-origin',headers:{'Accept':'application/json'}})
    .then(function(r){return r.ok?r.json():{data:[]};})
    .then(function(j){
      var sel=$('category_id');
      var cats=(j.data||[]);
      var html='<option value="">'+sel.options[0].text+'</option>';
      cats.forEach(function(c){
        html+='<option value="'+esc(c.id)+'">'+esc(felagiLocalizedName(c))+'</option>';
      });
      sel.innerHTML=html;
    })
    .catch(function(e){console.warn('[S005] cats',e);});
}

function collectForm(){
  var data={};
  FIELDS.forEach(function(f){
    var el=$(f);
    if(!el)return;
    var v=el.value;
    if(f==='currency'){data[f]=v||'ETB';return;}
    if(f==='budget_min'||f==='budget_max'||f==='quantity'){
      data[f]=v===''?null:parseFloat(v);
    }else{
      data[f]=v===''?null:v;
    }
  });
  data.telegram_publication_acknowledged=$('telegram_ack').checked;
  return data;
}

function saveDraft(){
  try{
    var data=collectForm();
    localStorage.setItem(DRAFT_KEY,JSON.stringify({data:data,at:Date.now()}));
    updateDraftIndicator(true);
  }catch(e){}
}

function loadDraft(){
  try{
    var raw=localStorage.getItem(DRAFT_KEY);
    if(!raw)return false;
    var parsed=JSON.parse(raw);
    if(!parsed||!parsed.data)return false;
    FIELDS.forEach(function(f){
      var el=$(f);
      if(!el)return;
      var v=parsed.data[f];
      if(v!=null)el.value=v;
    });
    if(parsed.data.telegram_publication_acknowledged){$('telegram_ack').checked=true;}
    return true;
  }catch(e){return false;}
}

function clearDraft(){
  try{localStorage.removeItem(DRAFT_KEY);updateDraftIndicator(false);}catch(e){}
}

function updateDraftIndicator(show){
  var el=$('draft-indicator');
  if(el)el.textContent=show?'{{ __("draftSaved") }}':'';
}

function updateDescCount(){
  var c=$('description').value.length;
  $('desc-count').textContent=c;
}

function clientValidate(){
  clearErrors();
  var ok=true;
  var t=$('title').value.trim();
  if(t.length<5){showErr('title','Min 5 characters');ok=false;}
  if(!$('category_id').value){showErr('category_id','Please choose a category');ok=false;}
  var d=$('description').value.trim();
  if(d.length<20){showErr('description','Min 20 characters');ok=false;}
  var bmin=$('budget_min').value;
  var bmax=$('budget_max').value;
  if(bmin!==''&&bmax!==''&&parseFloat(bmax)<parseFloat(bmin)){
    showErr('budget_max','Must be >= min');ok=false;
  }
  if(!$('telegram_ack').checked){
    showErr('telegram_publication_acknowledged','Please acknowledge');
    ok=false;
  }
  return ok;
}

function submitForm(e){
  e.preventDefault();
  hideStatus();
  if(!clientValidate()){
    showStatus('error','Please fix the errors below');
    return;
  }
  var token=getToken();
  /* L305d */

  var btn=$('submit-btn');
  btn.disabled=true;
  btn.innerHTML='<span class="spinner"></span>{{ __('saving') }}';

  var payload=collectForm();
  fetch('/api/v1/needs',{
    method:'POST',
    headers:{
      'Accept':'application/json',
      'Content-Type':'application/json',
      'X-CSRF-TOKEN':csrf
    },
    body:JSON.stringify(payload)
  })
  .then(function(r){
    return r.json().then(function(j){return {status:r.status,body:j};});
  })
  .then(function(res){
    if(res.status===201||res.status===200){
      clearDraft();
      showStatus('success','{{ __('needCreated') }}');
      var needId=(res.body.data&&res.body.data.id)||(res.body.id);
      setTimeout(function(){
        window.location.href=needId?('/needs/'+needId):'/browse';
      },800);
      return;
    }
    if(res.status===422&&res.body.errors){
      showValidationErrors(res.body.errors);
      showStatus('error','{{ __('validationFailed') }}');
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
    console.error('[S005] submit',err);
    showStatus('error','{{ __('saveFailed') }}');
  })
  .then(function(){
    btn.disabled=false;
    btn.textContent='{{ __('continue') }}';
  });
}

// ─── Events ───
$('need-form').addEventListener('submit',submitForm);
FIELDS.forEach(function(f){
  var el=$(f);
  if(el)el.addEventListener('input',function(){saveDraft();});
  if(el)el.addEventListener('change',function(){saveDraft();});
});

// L347-U: keep offer_deadline_at strictly before deadline_at
function bindDeadlineConstraint(){
  var dl = $('deadline_at');
  var od = $('offer_deadline_at');
  if(!dl || !od) return;

  function sync(){
    if(dl.value){
      // offer deadline must be <= (deadline - 1 min)
      var d = new Date(dl.value);
      d.setMinutes(d.getMinutes() - 1);
      var pad = function(n){return String(n).padStart(2,'0');};
      var max = d.getFullYear()+'-'+pad(d.getMonth()+1)+'-'+pad(d.getDate())+'T'+pad(d.getHours())+':'+pad(d.getMinutes());
      od.setAttribute('max', max);
    } else {
      od.removeAttribute('max');
    }
    // Also update offer_deadline_at min to be after now
    var now = new Date();
    var pad2 = function(n){return String(n).padStart(2,'0');};
    var min = now.getFullYear()+'-'+pad2(now.getMonth()+1)+'-'+pad2(now.getDate())+'T'+pad2(now.getHours())+':'+pad2(now.getMinutes());
    od.setAttribute('min', min);
  }
  dl.addEventListener('change', sync);
  sync();
}
bindDeadlineConstraint();
$('telegram_ack').addEventListener('change',saveDraft);
$('description').addEventListener('input',updateDescCount);

window.addEventListener('offline',function(){$('offline-banner').className='offline-banner on';});
window.addEventListener('online',function(){$('offline-banner').className='offline-banner';});

// ─── Boot ───
loadCategories();
loadDraft();
updateDescCount();
if(navigator.onLine===false){$('offline-banner').className='offline-banner on';}
})();
</script>
</body>
</html>
