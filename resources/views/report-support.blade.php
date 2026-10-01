<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>{{ __('reportTitle') }} — {{ __('brand') }}</title>
<style>
*{box-sizing:border-box;margin:0;padding:0}
body{font-family:system-ui,-apple-system,sans-serif;background:#F4F6F8;color:#192431;line-height:1.5;min-height:100vh;padding-bottom:100px}
header{background:#003366;color:#fff;height:64px;padding:0 16px;display:flex;align-items:center;gap:12px;position:sticky;top:0;z-index:30}
header a{color:#fff;text-decoration:none;font-size:20px;padding:8px;border-radius:6px;line-height:1}
header .title{font-size:16px;font-weight:600;flex:1}
main{max-width:640px;margin:0 auto;padding:16px}
.intro{background:#e3f2fd;border:1px solid #bbdefb;color:#0d47a1;padding:14px 16px;border-radius:10px;font-size:13px;line-height:1.5;margin-bottom:16px}
.intro strong{display:block;margin-bottom:4px}
.card{background:#fff;border-radius:12px;padding:20px;margin-bottom:16px;box-shadow:0 1px 3px rgba(0,0,0,.08)}
.card h2{font-size:15px;font-weight:600;color:#003366;margin-bottom:14px}
label{display:block;font-size:14px;font-weight:600;margin-bottom:6px;color:#192431}
label .req{color:#c62828}
label .opt{color:#586675;font-weight:400;font-size:12px;margin-left:4px}
select,textarea,input[type=text]{width:100%;padding:12px 13px;border:1px solid #d0d7de;border-radius:8px;font-family:inherit;font-size:15px;background:#fff;transition:border .15s,box-shadow .15s}
select:focus,textarea:focus,input:focus{outline:none;border-color:#003366;box-shadow:0 0 0 3px rgba(0,51,102,.1)}
textarea{min-height:140px;resize:vertical;line-height:1.5}
.form-group{margin-bottom:18px}
.form-group:last-child{margin-bottom:0}
.hint{font-size:12px;color:#586675;margin-top:4px}
.err{color:#c62828;font-size:12px;margin-top:4px;display:none}
.err.on{display:block}
.actions{position:fixed;bottom:0;left:0;right:0;background:#fff;border-top:1px solid #eef1f4;padding:12px 16px;display:flex;gap:10px;max-width:640px;margin:0 auto;z-index:20;box-shadow:0 -1px 3px rgba(0,0,0,.04)}
.btn{flex:1;padding:14px 20px;border-radius:10px;font-size:15px;font-weight:600;cursor:pointer;border:none;font-family:inherit;text-decoration:none;text-align:center;display:block;transition:background .15s}
.btn.primary{background:#003366;color:#fff}
.btn.primary:hover:not(:disabled){background:#002a52}
.btn.primary:disabled{opacity:.5;cursor:not-allowed}
.btn.sec{background:#eef1f4;color:#192431}
.btn.sec:hover{background:#e0e4e8}
.status{margin-bottom:16px;padding:12px 16px;border-radius:8px;font-size:14px;display:none}
.status.on{display:block}
.status.error{background:#ffebee;color:#b71c1c}
.status.success{background:#e8f5e9;color:#1b5e20}
.spinner{display:inline-block;width:14px;height:14px;border:2px solid rgba(255,255,255,.4);border-top-color:#fff;border-radius:50%;animation:spin .8s linear infinite;vertical-align:-2px;margin-right:6px}
@keyframes spin{to{transform:rotate(360deg)}}
.offline-banner{background:#fff8e1;border:1px solid #ffe082;color:#8a6d00;padding:10px 14px;border-radius:8px;font-size:13px;margin-bottom:16px;display:none}
.offline-banner.on{display:block}
</style>
</head>
<body>
<header>
<a href="#" id="back-btn" title="{{ __('back') }}">&#8592;</a>
<span class="title">{{ __('reportTitle') }}</span>
</header>

<main>
<div class="intro">
<strong>{{ __('reportIntroTitle') }}</strong>
{{ __('reportIntroBody') }}
</div>

<div class="offline-banner" id="offline-banner">{{ __('offlineBody') }}</div>
<div class="status" id="status"></div>

<form id="report-form" novalidate>

<div class="card">
<h2>{{ __('reportWhat') }}</h2>

<div class="form-group">
<label for="type">{{ __('reportType') }} <span class="req">*</span></label>
<select id="type" required>
<option value="">{{ __('selectType') }}</option>
<option value="spam">{{ __('reportTypeSpam') }}</option>
<option value="harassment">{{ __('reportTypeHarassment') }}</option>
<option value="fraud">{{ __('reportTypeFraud') }}</option>
<option value="inappropriate">{{ __('reportTypeInappropriate') }}</option>
<option value="other">{{ __('reportTypeOther') }}</option>
</select>
<div class="err" id="err-type"></div>
</div>

<div class="form-group">
<label for="entity_type">{{ __('reportTarget') }} <span class="req">*</span></label>
<select id="entity_type" required>
<option value="">{{ __('selectTarget') }}</option>
<option value="user">{{ __('targetUser') }}</option>
<option value="need">{{ __('targetNeed') }}</option>
<option value="offer">{{ __('targetOffer') }}</option>
<option value="message">{{ __('targetMessage') }}</option>
</select>
<div class="err" id="err-entity_type"></div>
</div>

<div class="form-group">
<label for="entity_id">{{ __('reportTargetId') }} <span class="opt">{{ __('optional') }}</span></label>
<input type="text" id="entity_id" maxlength="64" placeholder="{{ __('targetIdPlaceholder') }}" autocomplete="off">
<div class="hint">{{ __('targetIdHint') }}</div>
<div class="err" id="err-entity_id"></div>
</div>
</div>

<div class="card">
<h2>{{ __('reportDetails') }}</h2>

<div class="form-group">
<label for="reason">{{ __('reportReason') }} <span class="req">*</span></label>
<textarea id="reason" maxlength="5000" required minlength="20" placeholder="{{ __('reasonPlaceholder') }}"></textarea>
<div class="hint"><span id="reason-count">0</span> / 5000 ({{ __('minChars20') }})</div>
<div class="err" id="err-reason"></div>
</div>
</div>

<div class="card">
<h2>{{ __('reportContact') }}</h2>
<div class="form-group">
<label for="contact">{{ __('contactInfo') }} <span class="opt">{{ __('optional') }}</span></label>
<input type="text" id="contact" maxlength="255" placeholder="{{ __('contactPlaceholder') }}" autocomplete="off">
<div class="hint">{{ __('contactHint') }}</div>
</div>
</div>

</form>
</main>

<div class="actions">
<a href="#" id="cancel-btn" class="btn sec">{{ __('cancel') }}</a>
<button type="submit" form="report-form" class="btn primary" id="submit-btn">{{ __('submitReport') }}</button>
</div>

<script>
(function(){
'use strict';
var csrf=(document.querySelector('meta[name="csrf-token"]')||{}).content||'';
var LS_TOKEN='felagi_token';

function $(id){return document.getElementById(id);}
function getToken(){return 'session'; /* L305d */}

function showStatus(type,msg){
  var el=$('status');
  el.className='status on '+type;
  el.textContent=msg;
}
function hideStatus(){var el=$('status');el.className='status';el.textContent='';}

function clearErrors(){
  Array.prototype.forEach.call(document.querySelectorAll('.err'),function(e){
    e.className='err';e.textContent='';
  });
}
function showErr(field,msg){
  var el=$('err-'+field);
  if(!el)return;
  el.textContent=msg;
  el.className='err on';
}

function clientValidate(){
  clearErrors();
  var ok=true;
  if(!$('type').value){showErr('type','{{ __('required') }}');ok=false;}
  if(!$('entity_type').value){showErr('entity_type','{{ __('required') }}');ok=false;}
  var reason=$('reason').value.trim();
  if(reason.length<20){showErr('reason','{{ __('minChars20') }}');ok=false;}
  return ok;
}

function submitReport(e){
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
  btn.innerHTML='<span class="spinner"></span>{{ __('submitting') }}';

  var payload={
    type:$('type').value,
    entity_type:$('entity_type').value,
    entity_id:$('entity_id').value.trim()||null,
    reason:$('reason').value.trim()
  };
  var contact=$('contact').value.trim();
  if(contact)payload.contact=contact;

  fetch('/api/v1/reports',{
    method:'POST',
    headers:{
      'Accept':'application/json',
      'Content-Type':'application/json',
      'X-CSRF-TOKEN':csrf
    },
    body:JSON.stringify(payload)
  })
  .then(function(r){return r.json().then(function(j){return {s:r.status,b:j};});})
  .then(function(res){
    if(res.s===201||res.s===200){
      showStatus('success','{{ __('reportSubmitted') }}');
      setTimeout(function(){
        var back=$('back-btn').getAttribute('href');
        window.location.href=(back&&back!=='#')?back:'/my/needs';
      },1500);
      return;
    }
    if(res.s===422){
      var b=res.body||{};
      if(b.errors){
        Object.keys(b.errors).forEach(function(k){
          var msgs=b.errors[k];
          showErr(k,Array.isArray(msgs)?msgs[0]:msgs);
        });
      }
      showStatus('error','{{ __('validationFailed') }}');
      return;
    }
    if(res.s===401){
      localStorage.removeItem(LS_TOKEN);
      window.location.href='/';
      return;
    }
    if(res.s===429){showStatus('error','{{ __('rateLimited') }}');return;}
    showStatus('error','{{ __('reportFailed') }}');
  })
  .catch(function(){showStatus('error','{{ __('reportFailed') }}');})
  .then(function(){
    btn.disabled=false;
    btn.textContent='{{ __('submitReport') }}';
  });
}

// Back button best-effort
$('back-btn').href=document.referrer||'/my/needs';
$('cancel-btn').href=document.referrer||'/my/needs';

$('report-form').addEventListener('submit',submitReport);
$('reason').addEventListener('input',function(){
  $('reason-count').textContent=$('reason').value.length;
});

window.addEventListener('offline',function(){$('offline-banner').className='offline-banner on';});
window.addEventListener('online',function(){$('offline-banner').className='offline-banner';});

if(navigator.onLine===false){$('offline-banner').className='offline-banner on';}
})();
</script>
</body>
</html>
