<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>{{ __('screenS014') }} — {{ __('brand') }}</title>
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
input{font-family:inherit}
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
.intro{background:linear-gradient(135deg,var(--info-100),#f0f7ff);border:1px solid rgba(13,110,253,.18);border-radius:var(--r-md);padding:14px 16px;font-size:13px;color:var(--navy-800);margin-bottom:16px;line-height:1.6;font-weight:600}
.intro strong{display:block;margin-bottom:4px;font-weight:900;letter-spacing:-.01em}
.need-preview{background:linear-gradient(135deg,var(--canvas-2),var(--surface));border:1px solid var(--line-200);border-radius:var(--r-md);padding:14px 16px;margin-bottom:16px}
.need-preview .lbl{font-size:10.5px;color:var(--ink-300);text-transform:uppercase;letter-spacing:.08em;font-weight:800;margin-bottom:5px;font-family:var(--f-en)}
.need-preview .title{font-size:15px;font-weight:900;color:var(--ink-900);letter-spacing:-.02em;line-height:1.3}
.need-preview .meta{font-size:11.5px;color:var(--ink-300);margin-top:5px;font-weight:600}
.select-all{display:flex;justify-content:space-between;align-items:center;padding:14px 16px;margin-bottom:10px;background:var(--surface);border:1px solid var(--line-100);border-radius:var(--r-md);box-shadow:var(--sh-xs)}
.select-all label{display:flex;align-items:center;gap:10px;font-size:13.5px;font-weight:800;cursor:pointer;color:var(--ink-900)}
.select-all input[type="checkbox"]{width:20px;height:20px;accent-color:var(--navy-800);cursor:pointer}
.select-all .sel-count{font-size:12px;color:var(--navy-800);font-weight:900;font-family:var(--f-en);background:var(--canvas-2);padding:5px 11px;border-radius:var(--r-pill)}
.lst{background:var(--surface);border:2px solid var(--line-100);border-radius:var(--r-lg);padding:16px;margin-bottom:10px;box-shadow:var(--sh-xs);display:flex;gap:12px;cursor:pointer;transition:all .25s var(--ease-out);position:relative;overflow:hidden}
.lst:hover{box-shadow:var(--sh-sm);transform:translateY(-1px)}
.lst.selected{border-color:var(--navy-800);background:linear-gradient(135deg,#f6faff,var(--surface));box-shadow:var(--sh-sm)}
.lst.selected::before{content:'';position:absolute;top:0;left:0;right:0;height:3px;background:linear-gradient(90deg,var(--orange-500),var(--navy-700))}
.lst.disabled{opacity:.5;cursor:not-allowed}
.lst .check{width:24px;height:24px;border:2px solid var(--line-300);border-radius:8px;flex:0 0 auto;display:flex;align-items:center;justify-content:center;color:#fff;font-size:14px;font-weight:900;margin-top:2px;transition:all .2s var(--ease)}
.lst.selected .check{background:linear-gradient(135deg,var(--navy-800),var(--navy-700));border-color:var(--navy-800);box-shadow:0 2px 6px rgba(0,51,102,.24)}
.lst .body{flex:1;min-width:0}
.lst .price{font-size:20px;font-weight:900;color:var(--success-600);margin-bottom:4px;font-family:var(--f-en);display:flex;align-items:baseline;gap:4px;letter-spacing:-.02em}
.lst .price .cur{font-size:11px;font-weight:800;color:var(--ink-300);letter-spacing:.06em}
.lst .provider{font-size:14px;font-weight:900;color:var(--ink-900);margin-bottom:3px;letter-spacing:-.01em}
.lst .meta{font-size:11px;color:var(--ink-300);font-weight:700;font-family:var(--f-en)}
.lst .msg{font-size:12.5px;color:var(--ink-500);margin-top:8px;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;line-height:1.55}
.state{padding:60px 24px;text-align:center;color:var(--ink-300)}
.state .ic{font-size:48px;margin-bottom:14px;opacity:.5}
.state h3{font-size:18px;font-weight:900;color:var(--navy-800);margin-bottom:8px}
.state p{font-size:13.5px;line-height:1.6;color:var(--ink-500);max-width:320px;margin:0 auto 16px}
.btn{display:inline-flex;align-items:center;justify-content:center;gap:9px;height:50px;padding:0 24px;border-radius:var(--r-sm);font-weight:900;font-size:14.5px;line-height:1;letter-spacing:-.015em;transition:all .2s var(--ease);font-family:var(--f-am);text-align:center}
.btn:active{transform:scale(.975)}
.btn-primary{background:linear-gradient(135deg,var(--orange-500),var(--orange-600));color:#fff;box-shadow:var(--sh-orange)}
.btn-primary:hover:not(:disabled){transform:translateY(-1px);box-shadow:0 12px 28px rgba(255,153,51,.42)}
.btn-primary:disabled{opacity:.5;cursor:not-allowed}
.btn-ghost{background:var(--surface);color:var(--navy-800);border:1.5px solid var(--line-300)}
.btn-ghost:hover{background:var(--canvas-2);border-color:var(--navy-700)}
.actions{position:fixed;bottom:0;left:0;right:0;background:rgba(255,255,255,.98);backdrop-filter:blur(12px);border-top:1px solid var(--line-200);padding:14px 18px;display:flex;gap:10px;max-width:720px;margin:0 auto;z-index:20;box-shadow:0 -4px 16px rgba(0,26,51,.06)}
.actions .btn{flex:1}
.skeleton-card{background:var(--surface);border:1px solid var(--line-100);border-radius:var(--r-lg);padding:16px;margin-bottom:10px}
.sk-line{height:12px;background:var(--line-100);border-radius:4px;margin-bottom:8px}
.sk-line.w40{width:40%}.sk-line.w70{width:70%}.sk-line.w90{width:90%}
.spinner{display:inline-block;width:16px;height:16px;border:2px solid rgba(255,255,255,.4);border-top-color:#fff;border-radius:50%;animation:spin .8s linear infinite}
@keyframes spin{to{transform:rotate(360deg)}}
.offline-banner{display:none;background:var(--warn-100);color:var(--warn-600);padding:10px 16px;border-radius:var(--r-sm);font-size:12.5px;font-weight:800;text-align:center;margin-bottom:14px}
.offline-banner.on{display:block}
.toast{position:fixed;bottom:120px;left:50%;transform:translateX(-50%) translateY(20px);background:var(--navy-900);color:#fff;padding:12px 20px;border-radius:var(--r-pill);font-size:13px;font-weight:700;opacity:0;pointer-events:none;transition:all .3s var(--ease-out);z-index:50;box-shadow:var(--sh-md);max-width:90vw;text-align:center}
.toast.on{opacity:1;transform:translateX(-50%) translateY(0)}
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
    <h1 class="t" id="page-title" tabindex="-1" role="heading" aria-level="1">{{ __('compareOffers') }}</h1>
  </div>
</div>
<div class="sb">
  <div class="offline-banner" id="offline-banner" role="status" aria-live="polite">{{ __('offlineBody') }}</div>
  <div class="intro" id="intro">
    <strong>{{ __('compareIntroTitle') }}</strong>
    {{ __('compareIntroBody') }}
  </div>

  <div id="state-loading">
    <div class="skeleton-card"><div class="sk-line w40"></div><div class="sk-line w90"></div></div>
    <div class="skeleton-card"><div class="sk-line w40"></div><div class="sk-line w90"></div></div>
    <div class="skeleton-card"><div class="sk-line w40"></div><div class="sk-line w90"></div></div>
  </div>

  <div id="state-empty" class="state" hidden>
    <div class="ic">📋</div>
    <h3>{{ __('noEligibleOffersTitle') }}</h3>
    <p>{{ __('noEligibleOffersBody') }}</p>
    <a href="#" id="back-need-btn" class="btn btn-ghost">{{ __('backToNeed') }}</a>
  </div>

  <div id="state-error" class="state" hidden>
    <div class="ic">⚠️</div>
    <h3>{{ __('loadErrorTitle') }}</h3>
    <p>{{ __('loadErrorBody') }}</p>
    <button type="button" class="btn btn-primary" onclick="loadOffers()">{{ __('retry') }}</button>
  </div>

  <div id="content" hidden>
    <div class="need-preview">
      <div class="lbl">{{ __('needDetails') }}</div>
      <div class="title" id="need-title"></div>
      <div class="meta" id="need-meta"></div>
    </div>

    <div class="select-all">
      <label>
        <input type="checkbox" id="select-all-chk">
        <span>{{ __('selectAll') }}</span>
      </label>
      <span class="sel-count" id="sel-count">0 / 0</span>
    </div>

    <div id="offers-list"></div>
  </div>
</div>

<div class="actions" id="actions" hidden>
  <a href="#" id="cancel-btn" class="btn btn-ghost">{{ __('cancel') }}</a>
  <button type="button" class="btn btn-primary" id="evaluate-btn" disabled>{{ __('evaluateOffers') }}</button>
</div>

<div class="toast" id="toast" role="status" aria-live="polite"></div>
</div>
<script>
(function(){
'use strict';
var csrf=(document.querySelector('meta[name="csrf-token"]')||{}).content||'';
var LS_TOKEN='felagi_token';
var needId='';
var offers=[];
var selected={};
var submitting=false;
var MIN_SELECT=2;
var MAX_SELECT=10;
function $(id){return document.getElementById(id);}
function getToken(){return 'session';}
function esc(s){return String(s==null?'':s).replace(/[&<>"']/g,function(c){return {'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c];});}
function getNeedId(){var parts=window.location.pathname.split('/').filter(Boolean);return parts.length>=2?parts[1]:'';}
function toast(msg,ms){ms=ms||2800;var el=$('toast');el.textContent=msg;el.className='toast on';setTimeout(function(){el.className='toast';},ms);}
function showOnly(name){
  ['state-loading','state-empty','state-error','content'].forEach(function(id){var el=$(id);if(el)el.hidden=(id!==((name==='content')?'content':'state-'+name));});
  var actions=$('actions');if(actions)actions.hidden=(name!=='content');
}
function fmtPrice(it){
  var cur=it.currency||'ETB';
  var p=it.offered_price!=null?Number(it.offered_price).toFixed(2):'-';
  return {cur:cur,price:p};
}
function eligible(off){return off.status==='PENDING';}
function renderOffers(){
  var wrap=$('offers-list');
  wrap.innerHTML=offers.map(function(off){
    var isEligible=eligible(off);
    var isSelected=!!selected[off.id];
    var prov=off.provider||{};
    var pr=fmtPrice(off);
    var cls='lst'+(isSelected?' selected':'')+(isEligible?'':' disabled');
    return '<div class="'+cls+'" data-id="'+esc(off.id)+'" data-eligible="'+(isEligible?'1':'0')+'" role="checkbox" aria-checked="'+(isSelected?'true':'false')+'" tabindex="'+(isEligible?'0':'-1')+'">'+
      '<div class="check">'+(isSelected?'✓':'')+'</div>'+
      '<div class="body">'+
        '<div class="price"><span class="cur">'+esc(pr.cur)+'</span>'+esc(pr.price)+'</div>'+
        '<div class="provider">'+esc(prov.full_name||'{{ __('anonymous') }}')+'</div>'+
        '<div class="meta">'+(off.status||'')+(prov.rating_score?' · ★ '+Number(prov.rating_score).toFixed(1):'')+'</div>'+
        '<div class="msg">'+esc((off.proposal_message||'').slice(0,100))+'</div>'+
      '</div>'+
    '</div>';
  }).join('');
  Array.prototype.forEach.call(wrap.querySelectorAll('.lst'),function(el){
    if(el.dataset.eligible!=='1')return;
    el.addEventListener('click',function(){toggle(el.dataset.id);});
    el.addEventListener('keydown',function(e){if(e.key===' '||e.key==='Enter'){e.preventDefault();toggle(el.dataset.id);}});
  });
}
function toggle(id){
  if(selected[id]){delete selected[id];}
  else{
    var count=Object.keys(selected).length;
    if(count>=MAX_SELECT){toast('{{ __('maxSelectReached') }}');return;}
    selected[id]=true;
  }
  renderOffers();
  updateSelection();
}
function updateSelection(){
  var count=Object.keys(selected).length;
  var eligibleCount=offers.filter(eligible).length;
  $('sel-count').textContent=count+' / '+eligibleCount;
  var btn=$('evaluate-btn');
  btn.disabled=count<MIN_SELECT;
  var chk=$('select-all-chk');
  var eligibleSelected=offers.filter(function(o){return eligible(o)&&selected[o.id];}).length;
  chk.checked=(eligibleCount>0&&eligibleSelected===eligibleCount);
}
function renderNeed(need){
  $('need-title').textContent=need.title||'';
  var meta=[];
  if(need.status)meta.push(need.status);
  if(need.location_text)meta.push('📍 '+esc(need.location_text));
  $('need-meta').innerHTML=meta.join(' · ');
  var backHref='/needs/'+encodeURIComponent(needId);
  $('back-btn').href=backHref;
  $('cancel-btn').href=backHref;
  var bb=$('back-need-btn');
  if(bb)bb.href=backHref;
}
function loadOffers(){
  needId=getNeedId();
  if(!needId){showOnly('error');return;}
  showOnly('loading');
  fetch('/api/v1/needs/'+encodeURIComponent(needId)+'/offers',{credentials:'same-origin',headers:{'Accept':'application/json'}})
  .then(function(r){
    if(r.status===404)throw new Error('notfound');
    if(r.status===401)throw new Error('auth');
    if(r.status===403)throw new Error('denied');
    if(r.status===429)throw new Error('rate-limited');
    if(!r.ok)throw new Error('HTTP '+r.status);
    return r.json();
  })
  .then(function(j){
    var data=(j&&j.data)?j.data:(Array.isArray(j)?j:[]);
    offers=Array.isArray(data)?data:[];
    fetch('/api/v1/needs/'+encodeURIComponent(needId),{headers:{'Accept':'application/json'}})
      .then(function(r){return r.ok?r.json():null;})
      .then(function(j2){var need=(j2&&j2.data)?j2.data:j2;if(need&&need.id)renderNeed(need);})
      .catch(function(){});
    var eligibleOffers=offers.filter(eligible);
    if(eligibleOffers.length<MIN_SELECT){showOnly('empty');return;}
    renderOffers();updateSelection();showOnly('content');
  })
  .catch(function(e){
    var m=String(e.message||e);
    if(m==='auth'){localStorage.removeItem(LS_TOKEN);window.location.href='/';return;}
    if(m==='denied'){showOnly('error');return;}
    showOnly('error');
  });
}
function submitComparison(){
  if(submitting)return;
  var ids=Object.keys(selected);
  if(ids.length<MIN_SELECT){toast('{{ __('minSelect2') }}');return;}
  submitting=true;
  var btn=$('evaluate-btn');
  btn.disabled=true;
  btn.innerHTML='<span class="spinner"></span>{{ __('evaluating') }}';
  fetch('/api/v1/needs/'+encodeURIComponent(needId)+'/comparisons',{
    method:'POST',
    headers:{'Accept':'application/json','Content-Type':'application/json','X-CSRF-TOKEN':csrf},
    body:JSON.stringify({offer_ids:ids})
  })
  .then(function(r){return r.json().then(function(j){return {s:r.status,b:j};});})
  .then(function(res){
    if(res.s===201||res.s===200){
      var cid=(res.body.data&&res.body.data.id)||(res.body.id);
      toast('{{ __('comparisonStarted') }}',2200);
      setTimeout(function(){window.location.href=cid?('/comparisons/'+cid):('/needs/'+needId);},900);
      return;
    }
    if(res.s===501){toast('{{ __('aiNotConfigured') }}',4200);return;}
    if(res.s===409){
      var code=(res.body.code||res.body.error||'').toString().toUpperCase();
      if(code.indexOf('PROCESSING')!==-1){toast('{{ __('comparisonProcessing') }}',3200);}
      else{toast('{{ __('stateConflict') }}',3200);}
      return;
    }
    if(res.s===422){
      var b=res.body||{};
      var c=(b.code||b.error||'').toString().toUpperCase();
      if(c.indexOf('NO_ELIGIBLE')!==-1){toast('{{ __('noEligibleOffersToast') }}',3200);}
      else if(b.errors){toast('{{ __('validationFailed') }}',3200);}
      else{toast('{{ __('comparisonFailed') }}',3200);}
      return;
    }
    if(res.s===403){toast('{{ __('notAllowed') }}',3200);return;}
    if(res.s===429){toast('{{ __('rateLimited') }}',3200);return;}
    toast('{{ __('comparisonFailed') }}',3200);
  })
  .catch(function(){toast('{{ __('comparisonFailed') }}',3200);})
  .then(function(){
    submitting=false;
    btn.disabled=Object.keys(selected).length<MIN_SELECT;
    btn.textContent='{{ __('evaluateOffers') }}';
  });
}
$('select-all-chk').addEventListener('change',function(e){
  if(e.target.checked){
    selected={};
    offers.filter(eligible).slice(0,MAX_SELECT).forEach(function(o){selected[o.id]=true;});
  }else{selected={};}
  renderOffers();updateSelection();
});
$('evaluate-btn').addEventListener('click',submitComparison);
window.addEventListener('offline',function(){$('offline-banner').className='offline-banner on';});
window.addEventListener('online',function(){$('offline-banner').className='offline-banner';loadOffers();});
if(navigator.onLine===false){$('offline-banner').className='offline-banner on';}
window.loadOffers=loadOffers;
loadOffers();
})();
</script>
</body>
</html>
