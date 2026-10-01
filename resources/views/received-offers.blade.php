<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>{{ __('receivedOffersTitle') }} — {{ __('brand') }}</title>
<style>
*{box-sizing:border-box;margin:0;padding:0}
body{font-family:system-ui,-apple-system,sans-serif;background:#F4F6F8;color:#192431;line-height:1.5;min-height:100vh;padding-bottom:80px}
header{background:#003366;color:#fff;height:64px;padding:0 16px;display:flex;align-items:center;gap:12px;position:sticky;top:0;z-index:30}
header a{color:#fff;text-decoration:none;font-size:20px;padding:8px;border-radius:6px;line-height:1}
header a:hover{background:rgba(255,255,255,.12)}
header .title{font-size:16px;font-weight:600;flex:1;overflow:hidden;text-overflow:ellipsis;white-space:nowrap}
header .count{background:rgba(255,255,255,.18);padding:4px 10px;border-radius:999px;font-size:12px;font-weight:600}
main{max-width:960px;margin:0 auto;padding:16px}
.need-banner{background:#fff;border-radius:10px;padding:14px;margin-bottom:16px;box-shadow:0 1px 3px rgba(0,0,0,.08)}
.need-banner .lbl{font-size:11px;color:#586675;text-transform:uppercase;letter-spacing:.5px;font-weight:600;margin-bottom:4px}
.need-banner .title{font-size:15px;font-weight:600;color:#192431;line-height:1.3}
.need-banner .meta{font-size:12px;color:#586675;margin-top:4px}
.compare-bar{background:#e8eef4;border-radius:10px;padding:12px 14px;margin-bottom:16px;display:flex;justify-content:space-between;align-items:center;gap:12px;flex-wrap:wrap}
.compare-bar .info{font-size:13px;color:#003366;font-weight:600}
.compare-bar .btn-compare{padding:10px 18px;background:#003366;color:#fff;border-radius:8px;font-size:14px;font-weight:600;text-decoration:none;border:none;cursor:pointer;font-family:inherit}
.compare-bar .btn-compare:hover:not(:disabled){background:#002a52}
.compare-bar .btn-compare:disabled{opacity:.5;cursor:not-allowed}
.feed{display:grid;grid-template-columns:1fr;gap:12px}
@media(min-width:640px){.feed{grid-template-columns:1fr 1fr}}
.offer-card{background:#fff;border-radius:10px;padding:16px;box-shadow:0 1px 3px rgba(0,0,0,.08);display:flex;flex-direction:column;gap:10px;text-decoration:none;color:inherit;position:relative}
.offer-card:hover{box-shadow:0 4px 12px rgba(0,0,0,.1)}
.offer-card .top{display:flex;justify-content:space-between;align-items:flex-start;gap:8px}
.provider{display:flex;align-items:center;gap:10px;flex:1;min-width:0}
.avatar{width:40px;height:40px;border-radius:50%;background:#003366;color:#fff;display:flex;align-items:center;justify-content:center;font-size:16px;font-weight:700;flex:0 0 auto;overflow:hidden}
.avatar img{width:100%;height:100%;object-fit:cover}
.provider .name{font-weight:600;font-size:14px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap}
.provider .meta{font-size:11px;color:#586675}
.rating{color:#f5a623;font-weight:600}
.badge{display:inline-block;padding:3px 10px;border-radius:999px;font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.5px;flex:0 0 auto}
.badge.pending{background:#fff3e0;color:#e65100}
.badge.accepted{background:#e8f5e9;color:#1b5e20}
.badge.rejected{background:#f5f5f5;color:#616161}
.badge.withdrawn{background:#f5f5f5;color:#616161}
.offer-price{font-size:20px;font-weight:700;color:#1b5e20}
.offer-price .cur{font-size:14px;font-weight:600;margin-right:2px}
.offer-msg{font-size:13px;color:#3a4a5a;line-height:1.5;display:-webkit-box;-webkit-line-clamp:3;-webkit-box-orient:vertical;overflow:hidden}
.offer-meta{display:flex;flex-direction:column;gap:3px;font-size:12px;color:#586675}
.offer-meta .row{display:flex;gap:6px;align-items:center}
.offer-card .footer{margin-top:auto;padding-top:10px;border-top:1px solid #eef1f4;display:flex;justify-content:space-between;align-items:center;font-size:12px;color:#586675}
.view-link{color:#003366;font-weight:600;text-decoration:none}
.view-link:hover{text-decoration:underline}
.state{padding:60px 20px;text-align:center;color:#586675}
.state h3{color:#003366;font-size:18px;margin:0 0 8px}
.state p{margin:0 0 16px}
.btn{padding:12px 20px;border-radius:8px;font-size:15px;font-weight:600;cursor:pointer;border:none;font-family:inherit;text-decoration:none;text-align:center;display:inline-block;background:#003366;color:#fff;transition:background .15s}
.btn:hover:not(:disabled){background:#002a52}
.btn.sec{background:#eef1f4;color:#192431}
.btn.sec:hover{background:#e0e4e8}
.skeleton-card{background:#fff;border-radius:10px;padding:16px;box-shadow:0 1px 3px rgba(0,0,0,.08)}
.sk-line{height:12px;background:#eef1f4;border-radius:4px;margin-bottom:10px}
.sk-line.w40{width:40%}.sk-line.w70{width:70%}.sk-line.w90{width:90%}.sk-line.w100{width:100%}
.offline-banner{background:#fff8e1;border:1px solid #ffe082;color:#8a6d00;padding:10px 14px;border-radius:8px;font-size:13px;margin-bottom:16px;display:none}
.offline-banner.on{display:block}
.toast{position:fixed;bottom:100px;left:50%;transform:translateX(-50%);background:#192431;color:#fff;padding:12px 20px;border-radius:8px;font-size:14px;z-index:40;opacity:0;pointer-events:none;transition:opacity .2s;max-width:90vw}
.toast.on{opacity:1}
</style>
</head>
<body>
<header>
<a href="#" id="back-btn" title="{{ __('back') }}">&#8592;</a>
<span class="title">{{ __('receivedOffersTitle') }}</span>
<span class="count" id="offer-count" hidden>0</span>
</header>

<main>
<div class="offline-banner" id="offline-banner">{{ __('offlineBody') }}</div>

<div id="state-loading" class="feed">
<div class="skeleton-card"><div class="sk-line w40"></div><div class="sk-line w90"></div><div class="sk-line w70"></div><div class="sk-line w100"></div></div>
<div class="skeleton-card"><div class="sk-line w40"></div><div class="sk-line w90"></div><div class="sk-line w70"></div><div class="sk-line w100"></div></div>
<div class="skeleton-card"><div class="sk-line w40"></div><div class="sk-line w90"></div><div class="sk-line w70"></div><div class="sk-line w100"></div></div>
</div>

<div id="state-denied" class="state" hidden>
<h3>{{ __('accessDenied') }}</h3>
<p>{{ __('accessDeniedOffers') }}</p>
<a href="/my/needs" class="btn sec">{{ __('backToMyNeeds') }}</a>
</div>

<div id="state-error" class="state" hidden>
<h3>{{ __('loadErrorTitle') }}</h3>
<p>{{ __('loadErrorBody') }}</p>
<button type="button" class="btn" onclick="loadOffers()">{{ __('retry') }}</button>
</div>

<div id="state-empty" class="state" hidden>
<h3>{{ __('noOffersTitle') }}</h3>
<p>{{ __('noOffersBody') }}</p>
<a href="#" id="back-need-btn" class="btn sec">{{ __('backToNeed') }}</a>
</div>

<div id="content" hidden>

<div class="need-banner" id="need-banner" hidden>
<div class="lbl">{{ __('needDetails') }}</div>
<div class="title" id="need-title"></div>
<div class="meta" id="need-meta"></div>
</div>

<div class="compare-bar" id="compare-bar" hidden>
<span class="info" id="compare-info">{{ __('compareHint') }}</span>
<button type="button" class="btn-compare" id="btn-compare">{{ __('compareOffers') }}</button>
</div>

<div id="state-list" class="feed"></div>

</div>
</main>

<div class="toast" id="toast"></div>
<script>
(function(){
'use strict';
var csrf=(document.querySelector('meta[name="csrf-token"]')||{}).content||'';
var LS_TOKEN='felagi_token';
var needId='';
var offers=[];
var MIN_COMPARE=2;

function $(id){return document.getElementById(id);}
function getToken(){return localStorage.getItem(LS_TOKEN);}
function esc(s){return String(s==null?'':s).replace(/[&<>"']/g,function(c){return {'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c];});}

function getNeedId(){
  var parts=window.location.pathname.split('/').filter(Boolean);
  // /needs/{id}/offers
  return parts.length>=2?parts[1]:'';
}

function toast(msg,ms){
  ms=ms||2500;
  var el=$('toast');
  el.textContent=msg;
  el.className='toast on';
  setTimeout(function(){el.className='toast';},ms);
}

function showOnly(name){
  ['state-loading','state-denied','state-error','state-empty','content'].forEach(function(id){
    var el=$(id); if(el)el.hidden=(id!=='state-'+name);
  });
}

function fmtPrice(it){
  var cur=it.currency||'ETB';
  var price=it.offered_price!=null?Number(it.offered_price).toFixed(2):'-';
  return {cur:cur,price:price};
}

function fmtDate(iso){
  if(!iso)return '';
  var d=new Date(iso);
  if(isNaN(d))return '';
  return d.toLocaleDateString()+' '+d.toLocaleTimeString([],{hour:'2-digit',minute:'2-digit'});
}

function statusLabel(s){
  return ({
    'PENDING':'{{ __('offerStatusPending') }}',
    'ACCEPTED':'{{ __('offerStatusAccepted') }}',
    'REJECTED':'{{ __('offerStatusRejected') }}',
    'WITHDRAWN':'{{ __('offerStatusWithdrawn') }}'
  })[s]||s||'';
}

function renderNeedBanner(need){
  if(!need||!need.id){$('need-banner').hidden=true;return;}
  $('need-banner').hidden=false;
  $('need-title').textContent=need.title||'';
  var meta=[];
  var cat=need.category||{};
  var catName=cat.name_en||cat.name_am||cat.slug||'';
  if(catName)meta.push(catName);
  if(need.status)meta.push(need.status);
  if(need.location_text)meta.push('&#128205; '+esc(need.location_text));
  $('need-meta').innerHTML=meta.join(' · ');
  $('back-btn').href='/needs/'+encodeURIComponent(needId);
  var bb=$('back-need-btn');
  if(bb)bb.href='/needs/'+encodeURIComponent(needId);
}

function renderOfferCard(off){
  var prov=off.provider||{};
  var initial=String(prov.full_name||'?').trim().charAt(0).toUpperCase();
  var avatar=prov.profile_photo_url
    ? '<img src="'+esc(prov.profile_photo_url)+'" alt="">'
    : esc(initial);
  var rating=prov.rating_score
    ? '<span class="rating">&#9733; '+Number(prov.rating_score).toFixed(1)+'</span>'
    : '';
  var ratingCount=prov.rating_count?' ('+prov.rating_count+')':'';
  var pr=fmtPrice(off);
  var status=String(off.status||'PENDING').toLowerCase();
  var statusBadge='<span class="badge '+esc(status)+'">'+esc(statusLabel(off.status))+'</span>';
  var delivery=off.delivery_time_text?'&#9201; '+esc(off.delivery_time_text):'';
  var availability=off.availability_text?'&#128197; '+esc(off.availability_text):'';
  var created=off.created_at?fmtDate(off.created_at):'';
  var oid=encodeURIComponent(off.id);

  return '<a class="offer-card" href="/offers/'+oid+'">'+
    '<div class="top">'+
      '<div class="provider">'+
        '<div class="avatar">'+avatar+'</div>'+
        '<div style="min-width:0">'+
          '<div class="name">'+esc(prov.full_name||'{{ __('anonymous') }}')+'</div>'+
          '<div class="meta">'+rating+ratingCount+'</div>'+
        '</div>'+
      '</div>'+
      statusBadge+
    '</div>'+
    '<div class="offer-price"><span class="cur">'+esc(pr.cur)+'</span>'+esc(pr.price)+'</div>'+
    '<div class="offer-msg">'+esc(off.proposal_message||'')+'</div>'+
    ((delivery||availability)?
      '<div class="offer-meta">'+
        (delivery?'<div class="row">'+delivery+'</div>':'')+
        (availability?'<div class="row">'+availability+'</div>':'')+
      '</div>':'')+
    '<div class="footer">'+
      '<span>'+esc(created)+'</span>'+
      '<span class="view-link">{{ __('viewDetails') }} →</span>'+
    '</div>'+
  '</a>';
}

function renderList(){
  var wrap=$('state-list');
  if(!offers.length){wrap.innerHTML='';return;}
  wrap.innerHTML=offers.map(renderOfferCard).join('');
}

function updateCompareBar(){
  var bar=$('compare-bar');
  if(!bar)return;
  var pendingCount=offers.filter(function(o){return o.status==='PENDING';}).length;
  if(offers.length>=MIN_COMPARE&&pendingCount>=MIN_COMPARE){
    bar.hidden=false;
    $('compare-info').textContent='{{ __('compareHint') }} ('+pendingCount+')';
  }else{
    bar.hidden=true;
  }
}

function updateCount(){
  var el=$('offer-count');
  if(!el)return;
  if(offers.length){
    el.textContent=offers.length;
    el.hidden=false;
  }else{
    el.hidden=true;
  }
}

function loadOffers(){
  needId=getNeedId();
  if(!needId){showOnly('error');return;}

  var token=getToken();
  if(!token){window.location.href='/';return;}

  showOnly('loading');

  var h={'Accept':'application/json','Authorization':'Bearer '+token};
  fetch('/api/v1/needs/'+encodeURIComponent(needId)+'/offers',{headers:h})
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

      // Load need preview in parallel (best effort)
      var token2=getToken();
      if(token2){
        fetch('/api/v1/needs/'+encodeURIComponent(needId),{
          headers:{'Accept':'application/json','Authorization':'Bearer '+token2}
        }).then(function(r){return r.ok?r.json():null;})
          .then(function(j2){
            var need=(j2&&j2.data)?j2.data:j2;
            renderNeedBanner(need);
          }).catch(function(){});
      }else{
        $('back-btn').href='/needs/'+encodeURIComponent(needId);
      }

      if(!offers.length){
        showOnly('empty');
        updateCount();
        return;
      }
      renderList();
      updateCompareBar();
      updateCount();
      showOnly('content');
    })
    .catch(function(e){
      var m=String(e.message||e);
      if(m==='auth'){localStorage.removeItem(LS_TOKEN);window.location.href='/';return;}
      if(m==='denied'){showOnly('denied');return;}
      showOnly('error');
    });
}

window.loadOffers=loadOffers;

// Compare button (S014 entry)
var btnCompare=$('btn-compare');
if(btnCompare){
  btnCompare.addEventListener('click',function(){
    window.location.href='/needs/'+encodeURIComponent(needId)+'/compare';
  });
}

window.addEventListener('offline',function(){$('offline-banner').className='offline-banner on';});
window.addEventListener('online',function(){$('offline-banner').className='offline-banner';});

if(navigator.onLine===false){$('offline-banner').className='offline-banner on';}

loadOffers();
})();
</script>
</body>
</html>
