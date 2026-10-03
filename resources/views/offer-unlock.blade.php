@php
$s023 = [
    'labels' => [
        'state'    => __('s023LabelState'),
        'amount'   => __('s023LabelAmount'),
        'policy'   => __('s023LabelPolicy'),
        'offerId'  => __('s023LabelOfferId'),
        'created'  => __('s023LabelCreated'),
        'missing'  => __('s023ToastPaymentMissing'),
        'refund'   => __('s023ToastRefundInProgress'),
        'failed'   => __('s023ToastResumeFailed'),
        'network'  => __('s023ToastNetworkError'),
        'ok'       => __('s023ToastResumedOk'),
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
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>{{ __('screenS023') }} — {{ __('brand') }}</title>
<style>
*{box-sizing:border-box;margin:0;padding:0}
body{font-family:system-ui,-apple-system,sans-serif;background:#F4F6F8;color:#192431;line-height:1.5;min-height:100vh;padding-bottom:100px}
header{background:#003366;color:#fff;height:64px;padding:0 16px;display:flex;align-items:center;gap:12px;position:sticky;top:0;z-index:30}
header a{color:#fff;text-decoration:none;font-size:20px;padding:8px;border-radius:6px;line-height:1}
header .title{font-size:16px;font-weight:600;flex:1}
main{max-width:720px;margin:0 auto;padding:16px}
.hero{text-align:center;padding:32px 16px}
.hero .icon{width:88px;height:88px;border-radius:50%;background:#e3f2fd;color:#0d47a1;display:flex;align-items:center;justify-content:center;font-size:44px;margin:0 auto 20px}
.hero h1{font-size:22px;font-weight:700;color:#003366;margin-bottom:10px}
.hero h1:focus{outline:none}
.hero p{color:#586675;font-size:14px;line-height:1.5;max-width:400px;margin:0 auto}
:focus-visible{outline:3px solid #1b5e20;outline-offset:2px;border-radius:6px}
.card{background:#fff;border-radius:12px;padding:20px;margin-bottom:16px;box-shadow:0 1px 3px rgba(0,0,0,.08)}
.card h2{font-size:15px;font-weight:600;color:#003366;margin-bottom:12px}
.info-row{display:flex;justify-content:space-between;padding:10px 0;border-bottom:1px solid #eef1f4;font-size:14px}
.info-row:last-child{border-bottom:none}
.info-row .lbl{color:#586675}
.info-row .val{font-weight:600;text-align:right;max-width:60%;word-break:break-word}
.price-big{font-size:26px;font-weight:700;color:#1b5e20;text-align:center;margin:16px 0 6px}
.price-big .cur{font-size:16px;font-weight:600;margin-right:4px}
.price-sub{text-align:center;font-size:12px;color:#586675;margin-bottom:8px}
.notice-warn{background:#fff8e1;border:1px solid #ffe082;color:#8a6d00;padding:14px 16px;border-radius:10px;font-size:13px;line-height:1.5;margin-bottom:16px;display:none}
.notice-warn.on{display:block}
.state{padding:60px 20px;text-align:center;color:#586675}
.state h3{color:#003366;font-size:18px;margin:0 0 8px}
.state p{margin:0 0 16px}
.actions{position:fixed;bottom:0;left:0;right:0;background:#fff;border-top:1px solid #eef1f4;padding:12px 16px;display:flex;gap:10px;max-width:720px;margin:0 auto;z-index:20;box-shadow:0 -1px 3px rgba(0,0,0,.04)}
.btn{flex:1;padding:14px 20px;border-radius:10px;font-size:15px;font-weight:600;cursor:pointer;border:none;font-family:inherit;text-decoration:none;text-align:center;display:block;transition:background .15s}
.btn.primary{background:#003366;color:#fff}
.btn.primary:hover:not(:disabled){background:#002a52}
.btn.primary:disabled{opacity:.5;cursor:not-allowed}
.btn.sec{background:#eef1f4;color:#192431}
.btn.sec:hover{background:#e0e4e8}
.btn.success{background:#1b5e20;color:#fff}
.btn.success:hover:not(:disabled){background:#154a19}
.btn.success:disabled{opacity:.5;cursor:not-allowed}
.spinner{display:inline-block;width:20px;height:20px;border:3px solid #e0e0e0;border-top-color:#003366;border-radius:50%;animation:spin .8s linear infinite}
@keyframes spin{to{transform:rotate(360deg)}}
.offline-banner{background:#fff8e1;border:1px solid #ffe082;color:#8a6d00;padding:10px 14px;border-radius:8px;font-size:13px;margin-bottom:16px;display:none}
.offline-banner.on{display:block}
.toast{position:fixed;bottom:120px;left:50%;transform:translateX(-50%);background:#192431;color:#fff;padding:12px 20px;border-radius:8px;font-size:14px;z-index:40;opacity:0;pointer-events:none;transition:opacity .2s;max-width:90vw;text-align:center}
.toast.on{opacity:1}
.state-badge{display:inline-block;padding:6px 14px;border-radius:999px;font-size:12px;font-weight:700;text-transform:uppercase;letter-spacing:.5px;margin-bottom:12px;background:#eef1f4;color:#192431}
.state-badge.free{background:#e8f5e9;color:#1b5e20}
.state-badge.submitted{background:#d1fae5;color:#065f46}
.state-badge.pending{background:#fff8e1;color:#8a6d00}
.state-badge.payment-required{background:#ffebee;color:#b91c1c}
.state-badge.failed{background:#ffebee;color:#b91c1c}
#state-title{font-size:18px;color:#003366;margin:0 0 8px}
#state-desc{color:#586675;font-size:14px;line-height:1.5}
</style>
</head>
<body>
<header>
<a href="#" id="back-btn" title="{{ __('back') }}">&#8592;</a>
<span class="title">{{ __('unlockTitle') }}</span>
</header>

<main aria-labelledby="page-title">
<div class="hero">
<div class="icon" aria-hidden="true">&#128275;</div>
<h1 id="page-title" tabindex="-1">{{ __('unlockHeroTitle') }}</h1>
<p>{{ __('unlockHeroBody') }}</p>
</div>

<div class="notice-warn" id="api-warn" role="alert"></div>
<div class="offline-banner" id="offline-banner" role="status">{{ __('offlineBody') }}</div>

<div id="state-loading" class="state" hidden aria-busy="true">
<div class="spinner" aria-hidden="true"></div>
<p style="margin-top:12px">{{ __('loading') }}...</p>
</div>

<div id="state-error" class="state" hidden role="alert">
<h3>{{ __('loadErrorTitle') }}</h3>
<p>{{ __('loadErrorBody') }}</p>
<button type="button" class="btn sec" onclick="window.load()" style="display:inline-block;max-width:180px;margin-top:12px">{{ __('retry') }}</button>
</div>

<div id="state-empty" class="state" hidden>
<h3>{{ __('unlockHeroTitle') }}</h3>
<p>{{ __('unlockHeroBody') }}</p>
<a href="#" id="empty-back" class="btn primary" style="display:inline-block;max-width:220px;margin-top:12px">{{ __('back') }}</a>
</div>

<div id="content" hidden>
<div class="card">
<span class="state-badge" id="state-badge"></span>
<h2 id="state-title"></h2>
<p id="state-desc" aria-live="polite"></p>
</div>

<div class="card" id="price-card" hidden>
<h2>{{ __('unlockCost') }}</h2>
<div class="price-big"><bdi><span class="cur" id="unlock-currency">ETB</span><span id="unlock-price">—</span></bdi></div>
<div class="price-sub">{{ __('unlockCostSub') }}</div>
</div>

<div class="card">
<h2>{{ __('unlockDetails') }}</h2>
<div id="unlock-rows"></div>
</div>
</div>
</main>

<div class="actions" id="actions" hidden>
<a href="#" id="cancel-btn" class="btn sec">{{ __('back') }}</a>
<button type="button" class="btn success" id="unlock-btn">{{ __('refresh') }}</button>
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

function getNeedId(){
  var parts=window.location.pathname.split('/').filter(Boolean);
  return parts.length>=2?parts[1]:'';
}
function getSubmissionId(){
  try{return new URLSearchParams(window.location.search).get('submission_id')||'';}catch(e){return '';}
}
function showState(name){
  $('state-loading').hidden=(name!=='loading');
  $('state-error').hidden=(name!=='error');
  $('state-empty').hidden=(name!=='empty');
  $('content').hidden=(name!=='content');
  $('actions').hidden=(name!=='content');
}
function fmtMinor(minor){
  if(minor==null)return '—';
  return (Number(minor)/100).toFixed(2);
}

function renderSubmission(s){
  var state=s.state||'unknown';

  var badge=$('state-badge');
  badge.textContent=state;
  badge.className='state-badge '+state;

  var meta=STATES[state]||STATES['unknown'];
  $('state-title').textContent=meta.title;
  $('state-desc').textContent=meta.desc;

  if(s.amount_minor!=null&&s.amount_minor>0){
    $('price-card').hidden=false;
    $('unlock-currency').textContent=s.currency||'ETB';
    $('unlock-price').textContent=fmtMinor(s.amount_minor);
  }else{
    $('price-card').hidden=true;
  }

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
