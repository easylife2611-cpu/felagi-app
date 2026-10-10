<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>{{ __('screenS021') }} — {{ __('brand') }}</title>
<meta name="theme-color" content="#003366">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Ethiopic:wght@400;500;600;700;800;900&family=Inter:wght@400;600;700;800;900&display=swap" rel="stylesheet">
<style>
:root{--navy-950:#001a33;--navy-900:#002b52;--navy-800:#003366;--navy-700:#0b4d83;--navy-600:#1a6bb0;--orange-700:#b35c00;--orange-600:#e8851d;--orange-500:#FF9933;--orange-400:#ffb366;--orange-100:#fff2e0;--ink-900:#0d1a2b;--ink-500:#3c4d61;--ink-300:#5f7185;--ink-200:#8fa0b3;--line-100:#eef2f6;--line-200:#e3e9ef;--line-300:#d5dde6;--surface:#fff;--canvas:#f4f7fa;--canvas-2:#eef3f8;--success-600:#0e6b34;--success-500:#147a3d;--success-100:#e8f5ee;--warn-600:#a86500;--warn-100:#fff7e6;--danger-600:#a32e21;--danger-500:#c0392b;--danger-100:#fdecea;--info-600:#0056d6;--info-100:#e7f1ff;--r-xs:8px;--r-sm:12px;--r-md:16px;--r-lg:22px;--r-pill:999px;--sh-xs:0 1px 2px rgba(0,26,51,.04),0 1px 3px rgba(0,26,51,.06);--sh-sm:0 2px 6px rgba(0,26,51,.05),0 4px 12px rgba(0,26,51,.06);--sh-md:0 4px 12px rgba(0,26,51,.06),0 12px 28px rgba(0,26,51,.08);--sh-navy:0 12px 32px rgba(0,51,102,.28);--sh-orange:0 8px 20px rgba(255,153,51,.32);--f-am:'Noto Sans Ethiopic','Inter',system-ui,sans-serif;--f-en:'Inter','Noto Sans Ethiopic',system-ui,sans-serif;--ease:cubic-bezier(.16,.84,.44,1);--ease-out:cubic-bezier(.22,1,.36,1)}
*{box-sizing:border-box;margin:0;padding:0}
html,body{background:var(--canvas);color:var(--ink-900);font-family:var(--f-am);line-height:1.55;-webkit-font-smoothing:antialiased}
body{padding:0 0 110px;min-height:100vh}
a{color:inherit;text-decoration:none}
button{font-family:inherit;cursor:pointer;border:0;background:transparent;color:inherit}
input,select,textarea{font-family:inherit;font-size:inherit}
:focus-visible{outline:3px solid var(--orange-500);outline-offset:3px;border-radius:8px}
.sr-only{position:absolute;width:1px;height:1px;padding:0;margin:-1px;overflow:hidden;clip:rect(0,0,0,0);white-space:nowrap;border:0}
.sc{max-width:640px;margin:0 auto;min-height:100vh;display:flex;flex-direction:column}
.sh{flex:0 0 auto;color:#fff;padding:18px 22px 22px;position:relative;overflow:hidden;z-index:1}
.mesh{background:radial-gradient(ellipse at top right,rgba(255,153,51,.18),transparent 55%),radial-gradient(ellipse at bottom left,rgba(26,107,176,.35),transparent 60%),linear-gradient(140deg,var(--navy-950),var(--navy-800))}
.sh .tr{display:flex;align-items:center;gap:12px;position:relative;z-index:1}
.ib{width:40px;height:40px;border-radius:50%;display:inline-flex;align-items:center;justify-content:center;color:#fff;background:rgba(255,255,255,.10);border:1px solid rgba(255,255,255,.08);transition:all .2s var(--ease);flex:0 0 auto}
.ib:hover{background:rgba(255,255,255,.18)}
.ib svg{width:20px;height:20px;stroke:currentColor;fill:none;stroke-width:2;stroke-linecap:round;stroke-linejoin:round}
.sh .t{margin-top:12px;font-size:22px;font-weight:900;letter-spacing:-.03em;line-height:1.2;position:relative;z-index:1}
.sb{flex:1 1 auto;padding:20px 18px 40px;position:relative;z-index:1}
.intro{background:linear-gradient(135deg,var(--info-100),#f0f7ff);border:1px solid rgba(13,110,253,.18);border-radius:var(--r-md);padding:14px 16px;font-size:13px;line-height:1.6;color:var(--navy-800);margin-bottom:16px;font-weight:600}
.intro strong{display:block;margin-bottom:5px;font-weight:900;letter-spacing:-.01em;font-size:13.5px}
.cd{background:var(--surface);border:1px solid var(--line-100);border-radius:var(--r-lg);padding:20px;margin-bottom:14px;box-shadow:var(--sh-xs)}
.cd h2{font-size:12px;font-weight:900;color:var(--navy-800);text-transform:uppercase;letter-spacing:.08em;margin-bottom:16px;font-family:var(--f-en)}
.fl{margin-bottom:18px}
.fl:last-child{margin-bottom:0}
.fl label{display:block;font-size:12.5px;font-weight:800;margin-bottom:8px;color:var(--ink-500);letter-spacing:-.005em}
.fl .req{color:var(--danger-500);font-weight:900;margin-left:2px}
.fl .opt{color:var(--ink-300);font-weight:700;font-size:11px;margin-left:5px;font-family:var(--f-en)}
.inp{width:100%;padding:13px 16px;border:1.5px solid var(--line-300);border-radius:var(--r-sm);background:var(--surface);font-size:14.5px;color:var(--ink-900);outline:none;transition:all .2s var(--ease);font-weight:500;min-height:50px}
.inp:focus{border-color:var(--navy-800);box-shadow:0 0 0 4px rgba(0,51,102,.08)}
.inp::placeholder{color:var(--ink-200);font-weight:400}
textarea.inp{min-height:140px;resize:vertical;line-height:1.6}
select.inp{cursor:pointer;background-image:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%235f7185' stroke-width='2'%3E%3Cpath d='m6 9 6 6 6-6'/%3E%3C/svg%3E");background-repeat:no-repeat;background-position:right 14px center;background-size:18px;padding-right:42px;-webkit-appearance:none;appearance:none}
.hint{font-size:11.5px;color:var(--ink-300);margin-top:6px;font-weight:600}
.err{color:var(--danger-600);font-size:11.5px;margin-top:6px;display:none;font-weight:800}
.err.on{display:flex;align-items:center;gap:5px}
.err.on::before{content:'⚠';font-size:13px}
.actions{position:fixed;bottom:0;left:0;right:0;background:rgba(255,255,255,.98);backdrop-filter:blur(12px);border-top:1px solid var(--line-200);padding:14px 18px;display:flex;gap:10px;max-width:640px;margin:0 auto;z-index:20;box-shadow:0 -4px 16px rgba(0,26,51,.06)}
.bt{display:inline-flex;align-items:center;justify-content:center;gap:9px;height:52px;padding:0 24px;border-radius:var(--r-sm);font-weight:900;font-size:15px;line-height:1;letter-spacing:-.015em;transition:all .2s var(--ease);font-family:var(--f-am);text-align:center;flex:1}
.bt:active{transform:scale(.975)}
.bt-primary{background:linear-gradient(135deg,var(--orange-500),var(--orange-600));color:#fff;box-shadow:var(--sh-orange)}
.bt-primary:hover:not(:disabled){transform:translateY(-1px);box-shadow:0 12px 28px rgba(255,153,51,.42)}
.bt-primary:disabled{opacity:.5;cursor:not-allowed}
.bt-ghost{background:var(--surface);color:var(--navy-800);border:1.5px solid var(--line-300);flex:0 0 auto}
.bt-ghost:hover{background:var(--canvas-2);border-color:var(--navy-700)}
.status{margin-bottom:14px;padding:14px 16px;border-radius:var(--r-md);font-size:13.5px;font-weight:800;display:none;line-height:1.5}
.status.on{display:block}
.status.error{background:var(--danger-100);color:var(--danger-600)}
.status.success{background:var(--success-100);color:var(--success-600)}
.spinner{display:inline-block;width:16px;height:16px;border:2px solid rgba(255,255,255,.4);border-top-color:#fff;border-radius:50%;animation:spin .8s linear infinite}
@keyframes spin{to{transform:rotate(360deg)}}
.offline-banner{display:none;background:var(--warn-100);color:var(--warn-600);padding:10px 16px;border-radius:var(--r-sm);font-size:12.5px;font-weight:800;text-align:center;margin-bottom:14px}
.offline-banner.on{display:block}
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
    <h1 class="t" id="page-title" tabindex="-1" role="heading" aria-level="1">{{ __('reportTitle') }}</h1>
  </div>
</div>
<div class="sb">
  <div class="intro">
    <strong>{{ __('reportIntroTitle') }}</strong>
    {{ __('reportIntroBody') }}
  </div>
  <div class="offline-banner" id="offline-banner" role="status" aria-live="polite">{{ __('offlineBody') }}</div>
  <div class="status" id="status" role="status" aria-live="polite"></div>

  <form id="report-form" novalidate>
    <div class="cd">
      <h2>{{ __('reportWhat') }}</h2>

      <div class="fl">
        <label for="type">{{ __('reportType') }} <span class="req">*</span></label>
        <select id="type" class="inp" required>
          <option value="">{{ __('selectType') }}</option>
          <option value="spam">{{ __('reportTypeSpam') }}</option>
          <option value="harassment">{{ __('reportTypeHarassment') }}</option>
          <option value="fraud">{{ __('reportTypeFraud') }}</option>
          <option value="inappropriate">{{ __('reportTypeInappropriate') }}</option>
          <option value="other">{{ __('reportTypeOther') }}</option>
        </select>
        <div class="err" id="err-type"></div>
      </div>

      <div class="fl">
        <label for="entity_type">{{ __('reportTarget') }} <span class="req">*</span></label>
        <select id="entity_type" class="inp" required>
          <option value="">{{ __('selectTarget') }}</option>
          <option value="user">{{ __('targetUser') }}</option>
          <option value="need">{{ __('targetNeed') }}</option>
          <option value="offer">{{ __('targetOffer') }}</option>
          <option value="message">{{ __('targetMessage') }}</option>
        </select>
        <div class="err" id="err-entity_type"></div>
      </div>

      <div class="fl">
        <label for="entity_id">{{ __('reportTargetId') }} <span class="opt">{{ __('optional') }}</span></label>
        <input type="text" id="entity_id" class="inp" maxlength="64" placeholder="{{ __('targetIdPlaceholder') }}" autocomplete="off">
        <div class="hint">{{ __('targetIdHint') }}</div>
        <div class="err" id="err-entity_id"></div>
      </div>
    </div>

    <div class="cd">
      <h2>{{ __('reportDetails') }}</h2>
      <div class="fl">
        <label for="reason">{{ __('reportReason') }} <span class="req">*</span></label>
        <textarea id="reason" class="inp" maxlength="5000" required minlength="20" placeholder="{{ __('reasonPlaceholder') }}"></textarea>
        <div class="hint"><span id="reason-count">0</span> / 5000 ({{ __('minChars20') }})</div>
        <div class="err" id="err-reason"></div>
      </div>
    </div>

    <div class="cd">
      <h2>{{ __('reportContact') }}</h2>
      <div class="fl">
        <label for="contact">{{ __('contactInfo') }} <span class="opt">{{ __('optional') }}</span></label>
        <input type="text" id="contact" class="inp" maxlength="255" placeholder="{{ __('contactPlaceholder') }}" autocomplete="off">
        <div class="hint">{{ __('contactHint') }}</div>
      </div>
    </div>
  </form>
</div>

<div class="actions">
  <a href="#" id="cancel-btn" class="bt bt-ghost">{{ __('cancel') }}</a>
  <button type="submit" form="report-form" class="bt bt-primary" id="submit-btn">{{ __('submitReport') }}</button>
</div>
</div>
<script>
(function(){
'use strict';
var csrf=(document.querySelector('meta[name="csrf-token"]')||{}).content||'';
var LS_TOKEN='felagi_token';
function $(id){return document.getElementById(id);}
function getToken(){return 'session';}
function showStatus(type,msg){var el=$('status');el.className='status on '+type;el.textContent=msg;}
function hideStatus(){var el=$('status');el.className='status';el.textContent='';}
function clearErrors(){Array.prototype.forEach.call(document.querySelectorAll('.err'),function(e){e.className='err';e.textContent='';});}
function showErr(field,msg){var el=$('err-'+field);if(!el)return;el.textContent=msg;el.className='err on';}
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
  if(!clientValidate()){showStatus('error','{{ __('validationFailed') }}');return;}
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
    headers:{'Accept':'application/json','Content-Type':'application/json','X-CSRF-TOKEN':csrf},
    body:JSON.stringify(payload)
  })
  .then(function(r){return r.json().then(function(j){return {s:r.status,b:j};});})
  .then(function(res){
    if(res.s===201||res.s===200){
      showStatus('success','{{ __('reportSubmitted') }}');
      setTimeout(function(){var back=$('back-btn').getAttribute('href');window.location.href=(back&&back!=='#')?back:'/my/needs';},1500);
      return;
    }
    if(res.s===422){
      var b=res.body||{};
      if(b.errors){Object.keys(b.errors).forEach(function(k){var msgs=b.errors[k];showErr(k,Array.isArray(msgs)?msgs[0]:msgs);});}
      showStatus('error','{{ __('validationFailed') }}');
      return;
    }
    if(res.s===401){localStorage.removeItem(LS_TOKEN);window.location.href='/';return;}
    if(res.s===429){showStatus('error','{{ __('rateLimited') }}');return;}
    showStatus('error','{{ __('reportFailed') }}');
  })
  .catch(function(){showStatus('error','{{ __('reportFailed') }}');})
  .then(function(){btn.disabled=false;btn.textContent='{{ __('submitReport') }}';});
}
$('back-btn').href=document.referrer||'/my/needs';
$('cancel-btn').href=document.referrer||'/my/needs';
$('report-form').addEventListener('submit',submitReport);
$('reason').addEventListener('input',function(){$('reason-count').textContent=$('reason').value.length;});
window.addEventListener('offline',function(){$('offline-banner').className='offline-banner on';});
window.addEventListener('online',function(){$('offline-banner').className='offline-banner';});
if(navigator.onLine===false){$('offline-banner').className='offline-banner on';}
})();
</script>
</body>
</html>
