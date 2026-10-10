<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>{{ __('screenS023') }} — {{ __('brand') }}</title>
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
.hero{text-align:center;padding:24px 16px 20px}
.hero .icon{width:88px;height:88px;border-radius:50%;background:linear-gradient(135deg,var(--info-100),#f0f7ff);color:var(--info-600);display:flex;align-items:center;justify-content:center;font-size:42px;margin:0 auto 18px;box-shadow:0 8px 24px rgba(13,110,253,.16);border:1px solid rgba(13,110,253,.18)}
.hero h1{font-size:22px;font-weight:900;color:var(--navy-800);margin-bottom:10px;letter-spacing:-.03em;line-height:1.3}
.hero h1:focus{outline:none}
.hero p{color:var(--ink-500);font-size:13.5px;line-height:1.6;max-width:420px;margin:0 auto;font-weight:600}
.notice-warn{display:none;background:linear-gradient(135deg,var(--warn-100),#fffbf0);border:1px solid rgba(168,101,0,.18);color:var(--warn-600);padding:14px 16px;border-radius:var(--r-md);font-size:13px;line-height:1.6;margin-bottom:16px;font-weight:600}
.notice-warn.on{display:block}
.cd{background:var(--surface);border:1px solid var(--line-100);border-radius:var(--r-lg);padding:20px;margin-bottom:14px;box-shadow:var(--sh-xs)}
.cd h2{font-size:12px;font-weight:900;color:var(--navy-800);text-transform:uppercase;letter-spacing:.08em;margin-bottom:14px;font-family:var(--f-en)}
.bg{display:inline-flex;align-items:center;gap:5px;padding:6px 12px;border-radius:var(--r-pill);font-size:10.5px;font-weight:800;letter-spacing:.06em;font-family:var(--f-en);text-transform:uppercase;margin-bottom:12px}
.bg::before{content:'●';font-size:6px}
.bg.free{background:var(--success-100);color:var(--success-600)}
.bg.submitted{background:#d1fae5;color:#065f46}
.bg.pending{background:var(--warn-100);color:var(--warn-600)}
.bg.payment-required,.bg.failed{background:var(--danger-100);color:var(--danger-600)}
.bg.payment-verified,.bg.submission-recovery{background:var(--info-100);color:var(--info-600)}
.bg.refund-pending{background:#fef3c7;color:#92400e}
.bg.unknown{background:var(--line-100);color:var(--ink-500)}
.info-row{display:flex;justify-content:space-between;gap:12px;padding:11px 0;border-bottom:1px solid var(--line-100);font-size:13.5px}
.info-row:last-child{border-bottom:none}
.info-row .lbl{color:var(--ink-300);font-weight:800;font-size:11px;text-transform:uppercase;letter-spacing:.05em;font-family:var(--f-en);flex:0 0 auto}
.info-row .val{font-weight:900;color:var(--ink-900);text-align:right;word-break:break-word;max-width:65%;font-family:var(--f-en)}
.price-big{font-size:34px;font-weight:900;color:var(--success-600);text-align:center;margin:16px 0 6px;letter-spacing:-.03em;font-family:var(--f-en);display:flex;align-items:baseline;justify-content:center;gap:6px;line-height:1}
.price-big .cur{font-size:15px;font-weight:800;color:var(--ink-300);letter-spacing:.06em}
.price-sub{text-align:center;font-size:11.5px;color:var(--ink-300);margin-bottom:6px;font-weight:700;text-transform:uppercase;letter-spacing:.04em;font-family:var(--f-en)}
.state{padding:60px 24px;text-align:center;color:var(--ink-300)}
.state .ic{font-size:48px;margin-bottom:14px;opacity:.5}
.state h3{font-size:18px;font-weight:900;color:var(--navy-800);margin-bottom:8px}
.state p{font-size:13.5px;line-height:1.6;color:var(--ink-500);max-width:320px;margin:0 auto 16px}
.actions{position:fixed;bottom:0;left:0;right:0;background:rgba(255,255,255,.98);backdrop-filter:blur(12px);border-top:1px solid var(--line-200);padding:14px 18px;display:flex;gap:10px;max-width:720px;margin:0 auto;z-index:20;box-shadow:0 -4px 16px rgba(0,26,51,.06)}
.bt{display:inline-flex;align-items:center;justify-content:center;gap:9px;height:52px;padding:0 24px;border-radius:var(--r-sm);font-weight:900;font-size:15px;line-height:1;letter-spacing:-.015em;transition:all .2s var(--ease);font-family:var(--f-am);text-align:center;flex:1}
.bt:active{transform:scale(.975)}
.bt-primary{background:linear-gradient(135deg,var(--orange-500),var(--orange-600));color:#fff;box-shadow:var(--sh-orange)}
.bt-primary:hover:not(:disabled){transform:translateY(-1px);box-shadow:0 12px 28px rgba(255,153,51,.42)}
.bt-primary:disabled{opacity:.5;cursor:not-allowed}
.bt-success{background:linear-gradient(135deg,var(--success-500),var(--success-600));color:#fff}
.bt-success:hover:not(:disabled){transform:translateY(-1px);box-shadow:0 12px 28px rgba(20,122,61,.32)}
.bt-success:disabled{opacity:.5;cursor:not-allowed}
.bt-ghost{background:var(--surface);color:var(--navy-800);border:1.5px solid var(--line-300);flex:0 0 auto}
.bt-ghost:hover{background:var(--canvas-2);border-color:var(--navy-700)}
.spinner{display:inline-block;width:26px;height:26px;border:3px solid var(--line-200);border-top-color:var(--navy-800);border-radius:50%;animation:spin .8s linear infinite}
@keyframes spin{to{transform:rotate(360deg)}}
.offline-banner{display:none;background:var(--warn-100);color:var(--warn-600);padding:10px 16px;border-radius:var(--r-sm);font-size:12.5px;font-weight:800;text-align:center;margin-bottom:14px}
.offline-banner.on{display:block}
.toast{position:fixed;bottom:120px;left:50%;transform:translateX(-50%) translateY(20px);background:var(--navy-900);color:#fff;padding:12px 20px;border-radius:var(--r-pill);font-size:13px;font-weight:700;opacity:0;pointer-events:none;transition:all .3s var(--ease-out);z-index:50;box-shadow:var(--sh-md);max-width:90vw;text-align:center}
.toast.on{opacity:1;transform:translateX(-50%) translateY(0)}
@media(min-width:600px){.sb{padding:28px 24px 48px}.sh{padding:20px 28px 26px}}
</style>
</head>
<body>
@php
    $s023 = [
        'labels' => [
            'state'   => __('s023LabelState'),
            'amount'  => __('s023LabelAmount'),
            'policy'  => __('s023LabelPolicy'),
            'offerId' => __('s023LabelOfferId'),
            'created' => __('s023LabelCreated'),
            'missing' => __('s023ToastPaymentMissing'),
            'refund'  => __('s023ToastRefundInProgress'),
            'failed'  => __('s023ToastResumeFailed'),
            'network' => __('s023ToastNetworkError'),
            'ok'      => __('s023ToastResumedOk'),
        ],
        'states' => [
            'free'                => ['title' => __('s023StateFree'),          'desc' => __('s023StateFreeDesc')],
            'payment-required'    => ['title' => __('s023StatePaymentRequired'), 'desc' => __('s023StatePaymentRequiredDesc')],
            'pending'             => ['title' => __('s023StatePending'),       'desc' => __('s023StatePendingDesc')],
            'payment-verified'    => ['title' => __('s023StatePaymentVerified'), 'desc' => __('s023StatePaymentVerifiedDesc')],
            'submission-recovery' => ['title' => __('s023StateRecovery'),      'desc' => __('s023StateRecoveryDesc')],
            'submitted'           => ['title' => __('s023StateSubmitted'),     'desc' => __('s023StateSubmittedDesc')],
            'refund-pending'      => ['title' => __('s023StateRefund'),        'desc' => __('s023StateRefundDesc')],
            'failed'              => ['title' => __('s023StateFailed'),        'desc' => __('s023StateFailedDesc')],
            'unknown'             => ['title' => __('s023StateUnknown'),       'desc' => __('s023StateUnknownDesc')],
        ],
        'actions' => [
            'free'                => __('refresh'),
            'payment-required'    => __('s023ActionPay'),
            'pending'             => __('refresh'),
            'payment-verified'    => __('s023ActionResume'),
            'submission-recovery' => __('s023ActionResume'),
            'submitted'           => __('s023ActionViewOffer'),
            'refund-pending'      => __('s023ActionViewRefund'),
            'failed'              => __('s023ActionRetrySupport'),
            'unknown'             => __('refresh'),
        ],
    ];
@endphp
<div class="sc">
<div class="sh mesh">
  <div class="tr">
    <a href="#" id="back-btn" class="ib" title="{{ __('back') }}" aria-label="{{ __('back') }}">
      <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M19 12H5M12 19l-7-7 7-7"/></svg>
    </a>
    <h1 class="t" id="page-title" tabindex="-1" role="heading" aria-level="1">{{ __('unlockTitle') }}</h1>
  </div>
</div>
<div class="sb">
  <div class="hero">
    <div class="icon" aria-hidden="true">🔓</div>
    <h1 id="page-title-hero">{{ __('unlockHeroTitle') }}</h1>
    <p>{{ __('unlockHeroBody') }}</p>
  </div>

  <div class="notice-warn" id="api-warn" role="alert"></div>
  <div class="offline-banner" id="offline-banner" role="status" aria-live="polite">{{ __('offlineBody') }}</div>

  <div id="state-loading" class="state" hidden aria-busy="true">
    <div class="spinner"></div>
    <p style="margin-top:14px">{{ __('loading') }}...</p>
  </div>

  <div id="state-error" class="state" hidden role="alert">
    <div class="ic">⚠️</div>
    <h3>{{ __('loadErrorTitle') }}</h3>
    <p>{{ __('loadErrorBody') }}</p>
    <button type="button" class="bt bt-primary" onclick="window.load()" style="display:inline-flex;max-width:200px;margin:0 auto">{{ __('retry') }}</button>
  </div>

  <div id="state-empty" class="state" hidden>
    <div class="ic">🔒</div>
    <h3>{{ __('unlockHeroTitle') }}</h3>
    <p>{{ __('unlockHeroBody') }}</p>
    <a href="#" id="empty-back" class="bt bt-ghost" style="display:inline-flex;max-width:220px;margin:0 auto">{{ __('back') }}</a>
  </div>

  <div id="content" hidden>
    <div class="cd">
      <span class="bg" id="state-badge"></span>
      <h2 id="state-title"></h2>
      <p id="state-desc" aria-live="polite" style="font-size:13.5px;color:var(--ink-500);line-height:1.6;font-weight:600"></p>
    </div>

    <div class="cd" id="price-card" hidden>
      <h2>{{ __('unlockCost') }}</h2>
      <div class="price-big"><bdi><span class="cur" id="unlock-currency">ETB</span><span id="unlock-price">—</span></bdi></div>
      <div class="price-sub">{{ __('unlockCostSub') }}</div>
    </div>

    <div class="cd">
      <h2>{{ __('unlockDetails') }}</h2>
      <div id="unlock-rows"></div>
    </div>
  </div>
</div>

<div class="actions" id="actions" hidden>
  <a href="#" id="cancel-btn" class="bt bt-ghost">{{ __('back') }}</a>
  <button type="button" class="bt bt-primary" id="unlock-btn">{{ __('refresh') }}</button>
</div>
</div>
<div class="toast" id="toast" role="status" aria-live="polite"></div>
<script>
var S023 = {!! json_encode($s023, JSON_UNESCAPED_UNICODE) !!};
(function(){
'use strict';
var L        = S023.labels;
var STATES   = S023.states;
var ACTIONS  = S023.actions;
var csrf=(document.querySelector('meta[name="csrf-token"]')||{}).content||'';
var needId='';
var submissionId='';
function $(id){return document.getElementById(id);}
function esc(s){return String(s==null?'':s).replace(/[&<>"']/g,function(c){return {'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c];});}
function toast(msg,ms){ms=ms||2800;var el=$('toast');el.textContent=msg;el.className='toast on';setTimeout(function(){el.className='toast';},ms);}
function getNeedId(){var parts=window.location.pathname.split('/').filter(Boolean);return parts.length>=2?parts[1]:'';}
function getSubmissionId(){try{return new URLSearchParams(window.location.search).get('submission_id')||'';}catch(e){return '';}}
function showState(name){
  $('state-loading').hidden=(name!=='loading');
  $('state-error').hidden=(name!=='error');
  $('state-empty').hidden=(name!=='empty');
  $('content').hidden=(name!=='content');
  $('actions').hidden=(name!=='content');
}
function fmtMinor(minor){if(minor==null)return '—';return (Number(minor)/100).toFixed(2);}
function renderSubmission(s){
  var state=s.state||'unknown';
  var badge=$('state-badge');
  badge.textContent=state;
  badge.className='bg '+state;
  var meta=STATES[state]||STATES['unknown'];
  $('state-title').textContent=meta.title;
  $('state-desc').textContent=meta.desc;
  if(s.amount_minor!=null&&s.amount_minor>0){
    $('price-card').hidden=false;
    $('unlock-currency').textContent=s.currency||'ETB';
    $('unlock-price').textContent=fmtMinor(s.amount_minor);
  }else{$('price-card').hidden=true;}
  var rows='';
  rows+='<div class="info-row"><span class="lbl">'+esc(L.state)+'</span><span class="val">'+esc(state)+'</span></div>';
  rows+='<div class="info-row"><span class="lbl">'+esc(L.amount)+'</span><span class="val"><bdi>'+esc(s.currency||'ETB')+' '+fmtMinor(s.amount_minor)+'</bdi></span></div>';
  if(s.policy_version)rows+='<div class="info-row"><span class="lbl">'+esc(L.policy)+'</span><span class="val">'+esc(s.policy_version)+'</span></div>';
  if(s.offer_id)rows+='<div class="info-row"><span class="lbl">'+esc(L.offerId)+'</span><span class="val">'+esc(s.offer_id)+'</span></div>';
  if(s.created_at)rows+='<div class="info-row"><span class="lbl">'+esc(L.created)+'</span><span class="val">'+esc(s.created_at)+'</span></div>';
  $('unlock-rows').innerHTML=rows;
  var btn=$('unlock-btn');
  var handlerFns={
    'payment-required': function(){toast(L.missing,4000);},
    'submitted':        function(){window.location.href='/offers/'+encodeURIComponent(s.offer_id);},
    'refund-pending':   function(){toast(L.refund,4000);},
    'failed':           function(){window.location.href='/support/report';}
  };
  var fn=handlerFns[state]||load;
  if(state==='payment-verified'||state==='submission-recovery'){fn=resume;}
  btn.textContent=ACTIONS[state]||ACTIONS['unknown'];
  btn.disabled=false;
  btn.onclick=fn;
}
function resume(){
  var btn=$('unlock-btn');
  btn.disabled=true;
  fetch('/api/v1/offer-submissions/'+encodeURIComponent(submissionId)+'/resume',{
    method:'POST',
    credentials:'same-origin',
    headers:{'Accept':'application/json','Content-Type':'application/json','X-CSRF-TOKEN':csrf},
    body:JSON.stringify({})
  })
  .then(function(r){return r.json().then(function(j){return {status:r.status,body:j};});})
  .then(function(res){
    btn.disabled=false;
    if(res.status>=200&&res.status<300){
      var d=(res.body&&res.body.data)?res.body.data:res.body;
      renderSubmission(d);
      toast(L.ok,3000);
    }else{
      toast((res.body&&res.body.error&&res.body.error.message)||L.failed,4000);
    }
  })
  .catch(function(){btn.disabled=false;toast(L.network,4000);});
}
function load(){
  needId=getNeedId();
  submissionId=getSubmissionId();
  var backHref='/needs/'+encodeURIComponent(needId);
  $('back-btn').href=backHref;
  $('cancel-btn').href=backHref;
  $('empty-back').href=backHref;
  if(!submissionId){showState('empty');return;}
  showState('loading');
  fetch('/api/v1/offer-submissions/'+encodeURIComponent(submissionId),{
    credentials:'same-origin',
    headers:{'Accept':'application/json'}
  })
  .then(function(r){
    if(r.status===401)throw new Error('auth');
    if(r.status===404)throw new Error('notfound');
    if(!r.ok)throw new Error('HTTP '+r.status);
    return r.json();
  })
  .then(function(j){
    var sub=(j&&j.data)?j.data:j;
    renderSubmission(sub);
    showState('content');
  })
  .catch(function(e){
    if(String(e.message||e)==='auth'){window.location.href='/';return;}
    showState('error');
  });
}
window.load=load;
window.addEventListener('online',function(){$('offline-banner').className='offline-banner';load();});
window.addEventListener('offline',function(){$('offline-banner').className='offline-banner on';});
if(navigator.onLine===false){$('offline-banner').className='offline-banner on';}
var h=document.getElementById('page-title');
if(h){try{h.focus();}catch(e){}}
load();
})();
</script>
</body>
</html>
