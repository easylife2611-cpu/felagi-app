<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>{{ __('compareOffers') }} — {{ __('brand') }}</title>
<style>
*{box-sizing:border-box;margin:0;padding:0}
body{font-family:system-ui,-apple-system,sans-serif;background:#F4F6F8;color:#192431;line-height:1.5;min-height:100vh;padding-bottom:100px}
header{background:#003366;color:#fff;height:64px;padding:0 16px;display:flex;align-items:center;gap:12px;position:sticky;top:0;z-index:30}
header a{color:#fff;text-decoration:none;font-size:20px;padding:8px;border-radius:6px;line-height:1}
header a:hover{background:rgba(255,255,255,.12)}
header .title{font-size:16px;font-weight:600;flex:1;overflow:hidden;text-overflow:ellipsis;white-space:nowrap}
main{max-width:720px;margin:0 auto;padding:16px}
.intro{background:#e8eef4;border-radius:10px;padding:14px 16px;font-size:13px;color:#003366;margin-bottom:16px;line-height:1.5}
.intro strong{display:block;margin-bottom:4px}
.need-preview{background:#fff;border-radius:10px;padding:14px;margin-bottom:16px;box-shadow:0 1px 3px rgba(0,0,0,.08)}
.need-preview .lbl{font-size:11px;color:#586675;text-transform:uppercase;letter-spacing:.5px;font-weight:600;margin-bottom:4px}
.need-preview .title{font-size:15px;font-weight:600;color:#192431}
.need-preview .meta{font-size:12px;color:#586675;margin-top:4px}
.select-all{display:flex;justify-content:space-between;align-items:center;padding:12px 0;margin-bottom:8px;border-bottom:1px solid #eef1f4}
.select-all label{display:flex;align-items:center;gap:8px;font-size:14px;font-weight:600;cursor:pointer;color:#192431}
.select-all .sel-count{font-size:13px;color:#586675}
.offer-item{background:#fff;border-radius:10px;padding:14px;margin-bottom:10px;box-shadow:0 1px 3px rgba(0,0,0,.08);display:flex;gap:12px;cursor:pointer;transition:box-shadow .15s,border-color .15s;border:2px solid transparent}
.offer-item:hover{box-shadow:0 4px 12px rgba(0,0,0,.1)}
.offer-item.selected{border-color:#003366;background:#f6f8fa}
.offer-item.disabled{opacity:.5;cursor:not-allowed}
.offer-item .check{width:22px;height:22px;border:2px solid #d0d7de;border-radius:6px;flex:0 0 auto;display:flex;align-items:center;justify-content:center;color:#fff;font-size:14px;margin-top:2px;transition:all .15s}
.offer-item.selected .check{background:#003366;border-color:#003366}
.offer-item .body{flex:1;min-width:0}
.offer-item .price{font-size:17px;font-weight:700;color:#1b5e20;margin-bottom:4px}
.offer-item .price .cur{font-size:12px;font-weight:600;margin-right:2px}
.offer-item .provider{font-size:13px;font-weight:600;color:#192431;margin-bottom:2px}
.offer-item .meta{font-size:11px;color:#586675}
.offer-item .msg{font-size:12px;color:#586675;margin-top:6px;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden}
.state{padding:60px 20px;text-align:center;color:#586675}
.state h3{color:#003366;font-size:18px;margin:0 0 8px}
.state p{margin:0 0 16px}
.state .icon-big{font-size:48px;margin-bottom:12px;opacity:.4}
.btn{padding:12px 20px;border-radius:8px;font-size:15px;font-weight:600;cursor:pointer;border:none;font-family:inherit;text-decoration:none;text-align:center;display:inline-block;background:#003366;color:#fff;transition:background .15s}
.btn:hover:not(:disabled){background:#002a52}
.btn:disabled{opacity:.5;cursor:not-allowed}
.btn.sec{background:#eef1f4;color:#192431}
.btn.sec:hover{background:#e0e4e8}
.actions{position:fixed;bottom:0;left:0;right:0;background:#fff;border-top:1px solid #eef1f4;padding:12px 16px;display:flex;gap:10px;max-width:720px;margin:0 auto;z-index:20;box-shadow:0 -1px 3px rgba(0,0,0,.04)}
.actions .btn{flex:1}
.skeleton-card{background:#fff;border-radius:10px;padding:14px;margin-bottom:10px;box-shadow:0 1px 3px rgba(0,0,0,.08)}
.sk-line{height:12px;background:#eef1f4;border-radius:4px;margin-bottom:8px}
.sk-line.w40{width:40%}.sk-line.w90{width:90%}.sk-line.w70{width:70%}
.spinner{display:inline-block;width:14px;height:14px;border:2px solid rgba(255,255,255,.4);border-top-color:#fff;border-radius:50%;animation:spin .8s linear infinite;vertical-align:-2px;margin-right:6px}
@keyframes spin{to{transform:rotate(360deg)}}
.offline-banner{background:#fff8e1;border:1px solid #ffe082;color:#8a6d00;padding:10px 14px;border-radius:8px;font-size:13px;margin-bottom:16px;display:none}
.offline-banner.on{display:block}
.toast{position:fixed;bottom:120px;left:50%;transform:translateX(-50%);background:#192431;color:#fff;padding:12px 20px;border-radius:8px;font-size:14px;z-index:40;opacity:0;pointer-events:none;transition:opacity .2s;max-width:90vw;text-align:center}
.toast.on{opacity:1}
.notice{background:#fff8e1;border:1px solid #ffe082;color:#8a6d00;padding:14px 16px;border-radius:10px;font-size:13px;line-height:1.5;margin-bottom:16px}
.notice.warn{background:#ffebee;border-color:#ffcdd2;color:#b71c1c}
.notice.info{background:#e3f2fd;border-color:#bbdefb;color:#0d47a1}
</style>
</head>
<body>
<header>
<a href="#" id="back-btn" title="{{ __('back') }}">&#8592;</a>
<span class="title">{{ __('compareOffers') }}</span>
</header>

<main>
<div class="offline-banner" id="offline-banner">{{ __('offlineBody') }}</div>

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
<div class="icon-big">&#128203;</div>
<h3>{{ __('noEligibleOffersTitle') }}</h3>
<p>{{ __('noEligibleOffersBody') }}</p>
<a href="#" id="back-need-btn" class="btn sec">{{ __('backToNeed') }}</a>
</div>

<div id="state-error" class="state" hidden>
<h3>{{ __('loadErrorTitle') }}</h3>
<p>{{ __('loadErrorBody') }}</p>
<button type="button" class="btn" onclick="loadOffers()">{{ __('retry') }}</button>
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
</main>

<div class="actions" id="actions" hidden>
<a href="#" id="cancel-btn" class="btn sec">{{ __('cancel') }}</a>
<button type="button" class="btn" id="evaluate-btn" disabled>{{ __('evaluateOffers') }}</button>
</div>

<div class="toast" id="toast"></div>

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
function getToken(){return 'session'; /* L305d */}
function esc(s){return String(s==null?'':s).replace(/[&<>"']/g,function(c){return {'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c];});}

function getNeedId(){
  var parts=window.location.pathname.split('/').filter(Boolean);
  // /needs/{id}/compare
  return parts.length>=2?parts[1]:'';
}

function toast(msg,ms){ms=ms||2800;var el=$('toast');el.textContent=msg;el.className='toast on';setTimeout(function(){el.className='toast';},ms);}

function showOnly(name){
  ['state-loading','state-empty','state-error','content'].forEach(function(id){
    var el=$(id); if(el)el.hidden=(id!=='state-'+name);
  });
  var actions=$('actions');
  if(actions)actions.hidden=(name!=='content');
}

function fmtPrice(it){
  var cur=it.currency||'ETB';
  var p=it.offered_price!=null?Number(it.offered_price).toFixed(2):'-';
  return {cur:cur,price:p};
}

function eligible(off){
  return off.status==='PENDING';
}

function renderOffers(){
  var wrap=$('offers-list');
  wrap.innerHTML=offers.map(function(off){
    var isEligible=eligible(off);
    var isSelected=!!selected[off.id];
    var prov=off.provider||{};
    var pr=fmtPrice(off);
    var cls='offer-item'+(isSelected?' selected':'')+(isEligible?'':' disabled');
    return '<div class="'+cls+'" data-id="'+esc(off.id)+'" data-eligible="'+(isEligible?'1':'0')+'">'+
      '<div class="check">'+(isSelected?'&#10003;':'')+'</div>'+
      '<div class="body">'+
        '<div class="price"><span class="cur">'+esc(pr.cur)+'</span>'+esc(pr.price)+'</div>'+
        '<div class="provider">'+esc(prov.full_name||'{{ __('anonymous') }}')+'</div>'+
        '<div class="meta">'+(off.status||'')+(prov.rating_score?' · &#9733; '+Number(prov.rating_score).toFixed(1):'')+'</div>'+
        '<div class="msg">'+esc((off.proposal_message||'').slice(0,100))+'</div>'+
      '</div>'+
    '</div>';
  }).join('');

  Array.prototype.forEach.call(wrap.querySelectorAll('.offer-item'),function(el){
    if(el.dataset.eligible!=='1')return;
    el.addEventListener('click',function(){toggle(el.dataset.id);});
  });
}

function toggle(id){
  if(selected[id]){
    delete selected[id];
  }else{
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
  if(need.location_text)meta.push('&#128205; '+esc(need.location_text));
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

  var token=getToken();
  /* L305d */

  showOnly('loading');

  fetch('/api/v1/needs/'+encodeURIComponent(needId)+'/offers',{
    credentials:'same-origin',headers:{'Accept':'application/json'}
  })
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

    // Need meta
    var token2=getToken();
    if(token2){
      fetch('/api/v1/needs/'+encodeURIComponent(needId),{
        headers:{'Accept':'application/json'2}
      }).then(function(r){return r.ok?r.json():null;})
        .then(function(j2){
          var need=(j2&&j2.data)?j2.data:j2;
          if(need&&need.id)renderNeed(need);
        }).catch(function(){});
    }

    var eligibleOffers=offers.filter(eligible);
    if(eligibleOffers.length<MIN_SELECT){
      showOnly('empty');
      return;
    }

    renderOffers();
    updateSelection();
    showOnly('content');
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

  var token=getToken();
  /* L305d */

  submitting=true;
  var btn=$('evaluate-btn');
  btn.disabled=true;
  btn.innerHTML='<span class="spinner"></span>{{ __('evaluating') }}';

  fetch('/api/v1/needs/'+encodeURIComponent(needId)+'/comparisons',{
    method:'POST',
    headers:{
      'Accept':'application/json',
      'Content-Type':'application/json',
      'X-CSRF-TOKEN':csrf
    },
    body:JSON.stringify({offer_ids:ids})
  })
  .then(function(r){return r.json().then(function(j){return {s:r.status,b:j};});})
  .then(function(res){
    if(res.s===201||res.s===200){
      var cid=(res.body.data&&res.body.data.id)||(res.body.id);
      toast('{{ __('comparisonStarted') }}',2200);
      setTimeout(function(){
        window.location.href=cid?('/comparisons/'+cid):('/needs/'+needId);
      },900);
      return;
    }
    if(res.s===501){
      // AI not configured yet (WP-10)
      toast('{{ __('aiNotConfigured') }}',4200);
      return;
    }
    if(res.s===409){
      var code=(res.body.code||res.body.error||'').toString().toUpperCase();
      if(code.indexOf('PROCESSING')!==-1){
        toast('{{ __('comparisonProcessing') }}',3200);
      }else{
        toast('{{ __('stateConflict') }}',3200);
      }
      return;
    }
    if(res.s===422){
      var b=res.body||{};
      var c=(b.code||b.error||'').toString().toUpperCase();
      if(c.indexOf('NO_ELIGIBLE')!==-1){
        toast('{{ __('noEligibleOffersToast') }}',3200);
      }else if(b.errors){
        toast('{{ __('validationFailed') }}',3200);
      }else{
        toast('{{ __('comparisonFailed') }}',3200);
      }
      return;
    }
    if(res.s===403){toast('{{ __('notAllowed') }}',3200);return;}
    if(res.s===429){toast('{{ __('rateLimited') }}',3200);return;}
    toast('{{ __('comparisonFailed') }}',3200);
  })
  .catch(function(){
    toast('{{ __('comparisonFailed') }}',3200);
  })
  .then(function(){
    submitting=false;
    btn.disabled=Object.keys(selected).length<MIN_SELECT;
    btn.textContent='{{ __('evaluateOffers') }}';
  });
}

// Select all
$('select-all-chk').addEventListener('change',function(e){
  if(e.target.checked){
    selected={};
    offers.filter(eligible).slice(0,MAX_SELECT).forEach(function(o){selected[o.id]=true;});
  }else{
    selected={};
  }
  renderOffers();
  updateSelection();
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
