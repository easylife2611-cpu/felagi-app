<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>{{ __('rateTitle') }} — {{ __('brand') }}</title>
<style>
*{box-sizing:border-box;margin:0;padding:0}
body{font-family:system-ui,-apple-system,sans-serif;background:#F4F6F8;color:#192431;line-height:1.5;min-height:100vh;padding-bottom:80px}
header{background:#003366;color:#fff;height:64px;padding:0 16px;display:flex;align-items:center;gap:12px;position:sticky;top:0;z-index:30}
header a{color:#fff;text-decoration:none;font-size:20px;padding:8px;border-radius:6px;line-height:1}
header .title{font-size:16px;font-weight:600;flex:1}
main{max-width:560px;margin:0 auto;padding:16px}
.card{background:#fff;border-radius:12px;padding:22px;margin-bottom:16px;box-shadow:0 1px 3px rgba(0,0,0,.08)}
.card h2{font-size:16px;font-weight:600;color:#003366;margin-bottom:14px}
.provider{display:flex;align-items:center;gap:14px;padding:14px;background:#f6f8fa;border-radius:10px;margin-bottom:16px}
.avatar{width:52px;height:52px;border-radius:50%;background:#003366;color:#fff;display:flex;align-items:center;justify-content:center;font-size:22px;font-weight:700;flex:0 0 auto;overflow:hidden}
.avatar img{width:100%;height:100%;object-fit:cover}
.provider .name{font-weight:600;font-size:15px}
.provider .meta{font-size:12px;color:#586675}
.stars{display:flex;gap:6px;justify-content:center;margin:16px 0 12px}
.star{font-size:44px;cursor:pointer;color:#d0d7de;transition:color .15s,transform .1s;line-height:1;user-select:none;background:transparent;border:none;padding:0;font-family:inherit}
.star.active,.star.hover{color:#f5a623;transform:scale(1.05)}
.score-label{text-align:center;font-size:14px;color:#586675;margin-bottom:16px;min-height:20px;font-weight:600}
label{display:block;font-size:14px;font-weight:600;margin-bottom:8px;color:#192431}
textarea{width:100%;padding:12px 14px;border:1px solid #d0d7de;border-radius:8px;font-family:inherit;font-size:15px;resize:vertical;min-height:120px;line-height:1.5}
textarea:focus{outline:none;border-color:#003366;box-shadow:0 0 0 3px rgba(0,51,102,.1)}
.hint{font-size:12px;color:#586675;margin-top:4px}
.actions{display:flex;flex-direction:column;gap:10px;margin-top:8px}
.btn{padding:14px 20px;border-radius:10px;font-size:15px;font-weight:600;cursor:pointer;border:none;font-family:inherit;text-decoration:none;text-align:center;display:block;transition:background .15s}
.btn.primary{background:#003366;color:#fff}
.btn.primary:hover:not(:disabled){background:#002a52}
.btn.primary:disabled{opacity:.5;cursor:not-allowed}
.btn.sec{background:#eef1f4;color:#192431}
.btn.sec:hover{background:#e0e4e8}
.state{padding:60px 20px;text-align:center;color:#586675}
.state h3{color:#003366;font-size:18px;margin:0 0 8px}
.state p{margin:0 0 16px}
.state .icon-big{font-size:48px;margin-bottom:12px;opacity:.4}
.spinner{display:inline-block;width:20px;height:20px;border:3px solid #e0e0e0;border-top-color:#003366;border-radius:50%;animation:spin .8s linear infinite}
@keyframes spin{to{transform:rotate(360deg)}}
.toast{position:fixed;bottom:100px;left:50%;transform:translateX(-50%);background:#192431;color:#fff;padding:12px 20px;border-radius:8px;font-size:14px;z-index:40;opacity:0;pointer-events:none;transition:opacity .2s;max-width:90vw;text-align:center}
.toast.on{opacity:1}
</style>
</head>
<body>
<header>
<a href="#" id="back-btn" title="{{ __('back') }}">&#8592;</a>
<span class="title">{{ __('rateTitle') }}</span>
</header>

<main>
<div id="state-loading" class="state" style="padding:80px 20px">
<div class="spinner"></div>
<p style="margin-top:12px">{{ __('loading') }}...</p>
</div>

<div id="state-error" class="state" hidden>
<div class="icon-big">&#9888;</div>
<h3>{{ __('cannotRateTitle') }}</h3>
<p id="error-msg">{{ __('cannotRateBody') }}</p>
<a href="/my/needs" class="btn sec" style="display:inline-block;max-width:220px;margin:0 auto">{{ __('backToMyNeeds') }}</a>
</div>

<div id="content" hidden>
<form id="rate-form">
<div class="card">
<h2>{{ __('whoToRate') }}</h2>
<div class="provider">
<div class="avatar" id="to-avatar"></div>
<div>
<div class="name" id="to-name"></div>
<div class="meta" id="to-meta"></div>
</div>
</div>
</div>

<div class="card">
<h2>{{ __('yourRating') }}</h2>
<div class="stars" id="stars" data-score="0">
<button type="button" class="star" data-value="1">&#9733;</button>
<button type="button" class="star" data-value="2">&#9733;</button>
<button type="button" class="star" data-value="3">&#9733;</button>
<button type="button" class="star" data-value="4">&#9733;</button>
<button type="button" class="star" data-value="5">&#9733;</button>
</div>
<div class="score-label" id="score-label">{{ __('tapToRate') }}</div>
</div>

<div class="card">
<h2>{{ __('reviewOptional') }}</h2>
<label for="review">{{ __('reviewLabel') }}</label>
<textarea id="review" maxlength="10000" placeholder="{{ __('reviewPlaceholder') }}"></textarea>
<div class="hint"><span id="review-count">0</span> / 10000</div>
</div>

<div class="actions">
<button type="submit" class="btn primary" id="submit-btn" disabled>{{ __('submitRating') }}</button>
<a href="/my/needs" class="btn sec">{{ __('skipForNow') }}</a>
</div>
</form>
</div>
</main>

<div class="toast" id="toast"></div>

<script>
(function(){
'use strict';
var csrf=(document.querySelector('meta[name="csrf-token"]')||{}).content||'';
var LS_TOKEN='felagi_token';
var needId='';
var meId=null;
var toUserId=null;
var score=0;
var submitting=false;

function $(id){return document.getElementById(id);}
function getToken(){return 'session'; /* L305d */}
function esc(s){return String(s==null?'':s).replace(/[&<>"']/g,function(c){return {'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c];});}

function getNeedId(){
  var parts=window.location.pathname.split('/').filter(Boolean);
  return parts.length>=2?parts[1]:'';
}

function toast(msg,ms){ms=ms||2400;var el=$('toast');el.textContent=msg;el.className='toast on';setTimeout(function(){el.className='toast';},ms);}

function showOnly(name){
  ['state-loading','state-error','content'].forEach(function(id){
    var el=$(id); if(el)el.hidden=(id!==((name==='content')?'content':'state-'+name));
  });
}

function scoreLabelText(n){
  return ({
    1:'{{ __('score1') }}',
    2:'{{ __('score2') }}',
    3:'{{ __('score3') }}',
    4:'{{ __('score4') }}',
    5:'{{ __('score5') }}'
  })[n]||'{{ __('tapToRate') }}';
}

function setScore(n){
  score=n;
  $('stars').dataset.score=String(n);
  var stars=$('stars').querySelectorAll('.star');
  Array.prototype.forEach.call(stars,function(s){
    var v=parseInt(s.dataset.value,10);
    s.classList.toggle('active',v<=n);
    s.classList.remove('hover');
  });
  $('score-label').textContent=scoreLabelText(n);
  $('submit-btn').disabled=(n<1);
}

function wireStars(){
  var stars=$('stars').querySelectorAll('.star');
  Array.prototype.forEach.call(stars,function(s){
    var v=parseInt(s.dataset.value,10);
    s.addEventListener('click',function(){setScore(v);});
    s.addEventListener('mouseenter',function(){
      Array.prototype.forEach.call(stars,function(x){
        var xv=parseInt(x.dataset.value,10);
        x.classList.toggle('hover',xv<=v);
      });
    });
  });
  $('stars').addEventListener('mouseleave',function(){
    Array.prototype.forEach.call($('stars').querySelectorAll('.star'),function(x){
      x.classList.remove('hover');
    });
  });
}

function renderToUser(user){
  if(!user||!user.id){return;}
  toUserId=user.id;
  var initial=String(user.full_name||'?').trim().charAt(0).toUpperCase();
  $('to-avatar').innerHTML=user.profile_photo_url
    ? '<img src="'+esc(user.profile_photo_url)+'" alt="">'
    : esc(initial);
  $('to-name').textContent=user.full_name||'{{ __('anonymous') }}';
  var meta=[];
  if(user.rating_score)meta.push('&#9733; '+Number(user.rating_score).toFixed(1));
  if(user.rating_count)meta.push('('+user.rating_count+')');
  $('to-meta').innerHTML=meta.join(' ');
}

function loadNeed(){
  needId=getNeedId();
  if(!needId){showOnly('error');return;}
  var token=getToken();
  /* L305d */

  // L332 — fetch current user from server (cookie session); localStorage 'felagi_user' is dead
  fetch('/api/v1/auth/me', {credentials:'same-origin', headers:{'Accept':'application/json'}})
    .then(function(r){return r.ok?r.json():{data:null};})
    .then(function(me){
      if(me && me.data && me.data.id) meId = me.data.id;
    })
    .catch(function(){})
    .then(function(){ return fetch('/api/v1/needs/'+encodeURIComponent(needId),{
      credentials:'same-origin',headers:{'Accept':'application/json'}
    }); })
  .then(function(r){
    if(r.status===404)throw new Error('notfound');
    if(r.status===401)throw new Error('auth');
    if(!r.ok)throw new Error('HTTP '+r.status);
    return r.json();
  })
  .then(function(j){
    var need=(j&&j.data)?j.data:j;
    if(!need||!need.id){showOnly('error');return;}

    if(need.status!=='COMPLETED'){
      $('error-msg').textContent='{{ __('needNotCompleted') }}';
      showOnly('error');
      return;
    }

    $('back-btn').href='/needs/'+encodeURIComponent(need.id);

    // Determine who to rate — the other participant
    var other=null;
    if(need.is_owner){
      // Owner rates the accepted provider
      var prov=need.requester||{};
      // We don't have provider data here directly; use S010 API to find accepted offer
      fetch('/api/v1/needs/'+encodeURIComponent(need.id)+'/offers',{
        credentials:'same-origin',headers:{'Accept':'application/json'}
      }).then(function(r){return r.ok?r.json():null;})
        .then(function(j2){
          var offers=(j2&&j2.data)?j2.data:[];
          var accepted=offers.find(function(o){return o.status==='ACCEPTED';});
          if(accepted&&accepted.provider){
            renderToUser(accepted.provider);
            showOnly('content');
          }else{
            $('error-msg').textContent='{{ __('noAcceptedOffer') }}';
            showOnly('error');
          }
        })
        .catch(function(){$('error-msg').textContent='{{ __('noAcceptedOffer') }}';showOnly('error');});
    }else{
      // Provider rates the owner
      renderToUser(need.requester);
      showOnly('content');
    }
  })
  .catch(function(e){
    var m=String(e.message||e);
    if(m==='auth'){localStorage.removeItem(LS_TOKEN);window.location.href='/';return;}
    showOnly('error');
  });
}

function submitRating(e){
  e.preventDefault();
  if(submitting)return;
  if(score<1){toast('{{ __('selectScore') }}');return;}
  if(!toUserId){toast('{{ __('cannotRateBody') }}');return;}

  var token=getToken();
  /* L305d */

  submitting=true;
  var btn=$('submit-btn');
  btn.disabled=true;
  btn.textContent='{{ __('submitting') }}...';

  var payload={to_user_id:toUserId,score:score};
  var review=$('review').value.trim();
  if(review)payload.review=review;

  fetch('/api/v1/needs/'+encodeURIComponent(needId)+'/ratings',{
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
      toast('{{ __('ratingSubmitted') }}');
      setTimeout(function(){window.location.href='/my/needs';},900);
      return;
    }
    if(res.s===422){
      var b=res.body||{};
      var code=(b.code||b.error||'').toString().toUpperCase();
      if(code.indexOf('DUPLICATE')!==-1||code.indexOf('EXISTS')!==-1){
        toast('{{ __('alreadyRated') }}');
      }else{
        toast('{{ __('ratingFailed') }}');
      }
      return;
    }
    if(res.s===403){toast('{{ __('notAllowed') }}');return;}
    if(res.s===401){localStorage.removeItem(LS_TOKEN);window.location.href='/';return;}
    toast('{{ __('ratingFailed') }}');
  })
  .catch(function(){toast('{{ __('ratingFailed') }}');})
  .then(function(){
    submitting=false;
    btn.disabled=score<1;
    btn.textContent='{{ __('submitRating') }}';
  });
}

$('rate-form').addEventListener('submit',submitRating);
$('review').addEventListener('input',function(){
  $('review-count').textContent=$('review').value.length;
});

wireStars();
setScore(0);
loadNeed();
})();
</script>
</body>
</html>
