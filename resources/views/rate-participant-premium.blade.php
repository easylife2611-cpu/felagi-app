<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>{{ __('screenS020') }} — {{ __('brand') }}</title>
<meta name="theme-color" content="#003366">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Ethiopic:wght@400;500;600;700;800;900&family=Inter:wght@400;600;700;800;900&display=swap" rel="stylesheet">
<style>
:root{--navy-950:#001a33;--navy-900:#002b52;--navy-800:#003366;--navy-700:#0b4d83;--navy-600:#1a6bb0;--orange-700:#b35c00;--orange-600:#e8851d;--orange-500:#FF9933;--orange-400:#ffb366;--orange-100:#fff2e0;--ink-900:#0d1a2b;--ink-500:#3c4d61;--ink-300:#5f7185;--ink-200:#8fa0b3;--line-100:#eef2f6;--line-200:#e3e9ef;--line-300:#d5dde6;--surface:#fff;--canvas:#f4f7fa;--canvas-2:#eef3f8;--success-600:#0e6b34;--success-500:#147a3d;--success-100:#e8f5ee;--warn-600:#a86500;--warn-100:#fff7e6;--danger-600:#a32e21;--danger-500:#c0392b;--danger-100:#fdecea;--info-600:#0056d6;--info-100:#e7f1ff;--r-xs:8px;--r-sm:12px;--r-md:16px;--r-lg:22px;--r-pill:999px;--sh-xs:0 1px 2px rgba(0,26,51,.04),0 1px 3px rgba(0,26,51,.06);--sh-sm:0 2px 6px rgba(0,26,51,.05),0 4px 12px rgba(0,26,51,.06);--sh-md:0 4px 12px rgba(0,26,51,.06),0 12px 28px rgba(0,26,51,.08);--sh-navy:0 12px 32px rgba(0,51,102,.28);--sh-orange:0 8px 20px rgba(255,153,51,.32);--f-am:'Noto Sans Ethiopic','Inter',system-ui,sans-serif;--f-en:'Inter','Noto Sans Ethiopic',system-ui,sans-serif;--ease:cubic-bezier(.16,.84,.44,1);--ease-out:cubic-bezier(.22,1,.36,1)}
*{box-sizing:border-box;margin:0;padding:0}
html,body{background:var(--canvas);color:var(--ink-900);font-family:var(--f-am);line-height:1.55;-webkit-font-smoothing:antialiased}
body{padding:0 0 40px;min-height:100vh}
a{color:inherit;text-decoration:none}
button{font-family:inherit;cursor:pointer;border:0;background:transparent;color:inherit}
textarea{font-family:inherit;font-size:inherit}
:focus-visible{outline:3px solid var(--orange-500);outline-offset:3px;border-radius:8px}
.sr-only{position:absolute;width:1px;height:1px;padding:0;margin:-1px;overflow:hidden;clip:rect(0,0,0,0);white-space:nowrap;border:0}
.sc{max-width:560px;margin:0 auto;min-height:100vh;display:flex;flex-direction:column}
.sh{flex:0 0 auto;color:#fff;padding:18px 22px 22px;position:relative;overflow:hidden;z-index:1}
.mesh{background:radial-gradient(ellipse at top right,rgba(255,153,51,.18),transparent 55%),radial-gradient(ellipse at bottom left,rgba(26,107,176,.35),transparent 60%),linear-gradient(140deg,var(--navy-950),var(--navy-800))}
.sh .tr{display:flex;align-items:center;gap:12px;position:relative;z-index:1}
.ib{width:40px;height:40px;border-radius:50%;display:inline-flex;align-items:center;justify-content:center;color:#fff;background:rgba(255,255,255,.10);border:1px solid rgba(255,255,255,.08);transition:all .2s var(--ease);flex:0 0 auto}
.ib:hover{background:rgba(255,255,255,.18)}
.ib svg{width:20px;height:20px;stroke:currentColor;fill:none;stroke-width:2;stroke-linecap:round;stroke-linejoin:round}
.sh .t{margin-top:12px;font-size:22px;font-weight:900;letter-spacing:-.03em;line-height:1.2;position:relative;z-index:1}
.sb{flex:1 1 auto;padding:20px 18px 40px;position:relative;z-index:1}
.cd{background:var(--surface);border:1px solid var(--line-100);border-radius:var(--r-lg);padding:22px;margin-bottom:14px;box-shadow:var(--sh-xs)}
.cd h2{font-size:12px;font-weight:900;color:var(--navy-800);text-transform:uppercase;letter-spacing:.08em;margin-bottom:14px;font-family:var(--f-en)}
.provider{display:flex;align-items:center;gap:14px;padding:14px;background:var(--canvas-2);border-radius:var(--r-md);border:1px solid var(--line-200)}
.av{width:56px;height:56px;border-radius:50%;background:linear-gradient(135deg,var(--navy-800),var(--navy-600));color:#fff;display:flex;align-items:center;justify-content:center;font-weight:900;font-size:22px;flex:0 0 auto;overflow:hidden;box-shadow:0 2px 8px rgba(0,51,102,.16)}
.av img{width:100%;height:100%;object-fit:cover}
.provider .name{font-weight:900;font-size:15px;color:var(--ink-900);letter-spacing:-.01em}
.provider .meta{font-size:12px;color:var(--orange-600);font-weight:700;margin-top:3px;font-family:var(--f-en)}
.stars{display:flex;gap:8px;justify-content:center;margin:16px 0 12px}
.star{font-size:48px;cursor:pointer;color:var(--line-300);transition:all .2s var(--ease-out);line-height:1;user-select:none;background:transparent;border:none;padding:0;font-family:inherit;filter:drop-shadow(0 2px 4px rgba(0,0,0,.04))}
.star.active,.star.hover{color:var(--orange-500);transform:scale(1.08);filter:drop-shadow(0 4px 10px rgba(255,153,51,.32))}
.score-label{text-align:center;font-size:14px;color:var(--ink-500);margin-bottom:6px;min-height:22px;font-weight:800;letter-spacing:-.005em}
.fl{position:relative;margin-bottom:6px}
.fl label{display:block;font-size:12px;font-weight:800;margin-bottom:8px;color:var(--ink-500);letter-spacing:-.005em}
.inp{width:100%;padding:13px 16px;border:1.5px solid var(--line-300);border-radius:var(--r-sm);background:var(--surface);font-size:14.5px;color:var(--ink-900);outline:none;transition:all .2s var(--ease);font-weight:500;resize:vertical;min-height:120px;line-height:1.6}
.inp:focus{border-color:var(--navy-800);box-shadow:0 0 0 4px rgba(0,51,102,.08)}
.inp::placeholder{color:var(--ink-200)}
.hint{font-size:11px;color:var(--ink-300);margin-top:6px;font-weight:700;font-family:var(--f-en)}
.actions{display:flex;flex-direction:column;gap:10px;margin-top:14px}
.bt{display:inline-flex;align-items:center;justify-content:center;gap:9px;height:52px;padding:0 24px;border-radius:var(--r-sm);font-weight:900;font-size:15px;line-height:1;letter-spacing:-.015em;transition:all .2s var(--ease);font-family:var(--f-am);text-align:center}
.bt:active{transform:scale(.975)}
.bt-primary{background:linear-gradient(135deg,var(--orange-500),var(--orange-600));color:#fff;box-shadow:var(--sh-orange)}
.bt-primary:hover:not(:disabled){transform:translateY(-1px);box-shadow:0 12px 28px rgba(255,153,51,.42)}
.bt-primary:disabled{opacity:.5;cursor:not-allowed}
.bt-ghost{background:var(--surface);color:var(--navy-800);border:1.5px solid var(--line-300)}
.bt-ghost:hover{background:var(--canvas-2);border-color:var(--navy-700)}
.state{padding:60px 24px;text-align:center;color:var(--ink-300)}
.state .ic{font-size:48px;margin-bottom:14px;opacity:.5}
.state h3{font-size:18px;font-weight:900;color:var(--navy-800);margin-bottom:8px}
.state p{font-size:13.5px;line-height:1.6;color:var(--ink-500);max-width:300px;margin:0 auto 16px}
.spinner{display:inline-block;width:24px;height:24px;border:3px solid var(--line-200);border-top-color:var(--navy-800);border-radius:50%;animation:spin .8s linear infinite}
@keyframes spin{to{transform:rotate(360deg)}}
.toast{position:fixed;bottom:100px;left:50%;transform:translateX(-50%) translateY(20px);background:var(--navy-900);color:#fff;padding:12px 20px;border-radius:var(--r-pill);font-size:13px;font-weight:700;opacity:0;pointer-events:none;transition:all .3s var(--ease-out);z-index:50;box-shadow:var(--sh-md);max-width:90vw;text-align:center}
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
    <h1 class="t" id="page-title" tabindex="-1" role="heading" aria-level="1">{{ __('rateTitle') }}</h1>
  </div>
</div>
<div class="sb">
  <div id="state-loading" class="state" style="padding:80px 24px">
    <div class="spinner"></div>
    <p style="margin-top:14px">{{ __('loading') }}...</p>
  </div>

  <div id="state-error" class="state" hidden>
    <div class="ic">⚠️</div>
    <h3>{{ __('cannotRateTitle') }}</h3>
    <p id="error-msg">{{ __('cannotRateBody') }}</p>
    <a href="/my/needs" class="bt bt-ghost" style="display:inline-flex;max-width:220px;margin:0 auto">{{ __('backToMyNeeds') }}</a>
  </div>

  <div id="content" hidden>
    <form id="rate-form">
      <div class="cd">
        <h2>{{ __('whoToRate') }}</h2>
        <div class="provider">
          <div class="av" id="to-avatar"></div>
          <div>
            <div class="name" id="to-name"></div>
            <div class="meta" id="to-meta"></div>
          </div>
        </div>
      </div>

      <div class="cd">
        <h2>{{ __('yourRating') }}</h2>
        <div class="stars" id="stars" data-score="0" role="radiogroup" aria-label="{{ __('yourRating') }}">
          <button type="button" class="star" data-value="1" role="radio" aria-label="{{ __('score1') }}">★</button>
          <button type="button" class="star" data-value="2" role="radio" aria-label="{{ __('score2') }}">★</button>
          <button type="button" class="star" data-value="3" role="radio" aria-label="{{ __('score3') }}">★</button>
          <button type="button" class="star" data-value="4" role="radio" aria-label="{{ __('score4') }}">★</button>
          <button type="button" class="star" data-value="5" role="radio" aria-label="{{ __('score5') }}">★</button>
        </div>
        <div class="score-label" id="score-label" aria-live="polite">{{ __('tapToRate') }}</div>
      </div>

      <div class="cd">
        <h2>{{ __('reviewOptional') }}</h2>
        <div class="fl">
          <label for="review">{{ __('reviewLabel') }}</label>
          <textarea id="review" class="inp" maxlength="10000" placeholder="{{ __('reviewPlaceholder') }}"></textarea>
          <div class="hint"><span id="review-count">0</span> / 10000</div>
        </div>
      </div>

      <div class="actions">
        <button type="submit" class="bt bt-primary" id="submit-btn" disabled>{{ __('submitRating') }}</button>
        <a href="/my/needs" class="bt bt-ghost">{{ __('skipForNow') }}</a>
      </div>
    </form>
  </div>
</div>
</div>
<div class="toast" id="toast" role="status" aria-live="polite"></div>
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
function getToken(){return 'session';}
function esc(s){return String(s==null?'':s).replace(/[&<>"']/g,function(c){return {'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c];});}
function getNeedId(){var parts=window.location.pathname.split('/').filter(Boolean);return parts.length>=2?parts[1]:'';}
function toast(msg,ms){ms=ms||2400;var el=$('toast');el.textContent=msg;el.className='toast on';setTimeout(function(){el.className='toast';},ms);}
function showOnly(name){
  ['state-loading','state-error','content'].forEach(function(id){var el=$(id);if(el)el.hidden=(id!==((name==='content')?'content':'state-'+name));});
}
function scoreLabelText(n){
  return ({1:'{{ __('score1') }}',2:'{{ __('score2') }}',3:'{{ __('score3') }}',4:'{{ __('score4') }}',5:'{{ __('score5') }}'})[n]||'{{ __('tapToRate') }}';
}
function setScore(n){
  score=n;
  $('stars').dataset.score=String(n);
  var stars=$('stars').querySelectorAll('.star');
  Array.prototype.forEach.call(stars,function(s){
    var v=parseInt(s.dataset.value,10);
    s.classList.toggle('active',v<=n);
    s.classList.remove('hover');
    s.setAttribute('aria-checked',v===n?'true':'false');
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
    Array.prototype.forEach.call($('stars').querySelectorAll('.star'),function(x){x.classList.remove('hover');});
  });
}
function renderToUser(user){
  if(!user||!user.id){return;}
  toUserId=user.id;
  var initial=String(user.full_name||'?').trim().charAt(0).toUpperCase();
  $('to-avatar').innerHTML=user.profile_photo_url?'<img src="'+esc(user.profile_photo_url)+'" alt="">':esc(initial);
  $('to-name').textContent=user.full_name||'{{ __('anonymous') }}';
  var meta=[];
  if(user.rating_score)meta.push('★ '+Number(user.rating_score).toFixed(1));
  if(user.rating_count)meta.push('('+user.rating_count+')');
  $('to-meta').innerHTML=meta.join(' ');
}
function loadNeed(){
  needId=getNeedId();
  if(!needId){showOnly('error');return;}
  fetch('/api/v1/auth/me', {credentials:'same-origin', headers:{'Accept':'application/json'}})
    .then(function(r){return r.ok?r.json():{data:null};})
    .then(function(me){if(me && me.data && me.data.id) meId = me.data.id;})
    .catch(function(){})
    .then(function(){ return fetch('/api/v1/needs/'+encodeURIComponent(needId),{credentials:'same-origin',headers:{'Accept':'application/json'}}); })
  .then(function(r){
    if(r.status===404)throw new Error('notfound');
    if(r.status===401)throw new Error('auth');
    if(!r.ok)throw new Error('HTTP '+r.status);
    return r.json();
  })
  .then(function(j){
    var need=(j&&j.data)?j.data:j;
    if(!need||!need.id){showOnly('error');return;}
    if(need.status!=='COMPLETED'){$('error-msg').textContent='{{ __('needNotCompleted') }}';showOnly('error');return;}
    $('back-btn').href='/needs/'+encodeURIComponent(need.id);
    if(need.is_owner){
      fetch('/api/v1/needs/'+encodeURIComponent(need.id)+'/offers',{credentials:'same-origin',headers:{'Accept':'application/json'}})
        .then(function(r){return r.ok?r.json():null;})
        .then(function(j2){
          var offers=(j2&&j2.data)?j2.data:[];
          var accepted=offers.find(function(o){return o.status==='ACCEPTED';});
          if(accepted&&accepted.provider){renderToUser(accepted.provider);showOnly('content');}
          else{$('error-msg').textContent='{{ __('noAcceptedOffer') }}';showOnly('error');}
        })
        .catch(function(){$('error-msg').textContent='{{ __('noAcceptedOffer') }}';showOnly('error');});
    }else{
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
  submitting=true;
  var btn=$('submit-btn');
  btn.disabled=true;
  btn.textContent='{{ __('submitting') }}...';
  var payload={to_user_id:toUserId,score:score};
  var review=$('review').value.trim();
  if(review)payload.review=review;
  fetch('/api/v1/needs/'+encodeURIComponent(needId)+'/ratings',{
    method:'POST',
    headers:{'Accept':'application/json','Content-Type':'application/json','X-CSRF-TOKEN':csrf},
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
      if(code.indexOf('DUPLICATE')!==-1||code.indexOf('EXISTS')!==-1){toast('{{ __('alreadyRated') }}');}
      else{toast('{{ __('ratingFailed') }}');}
      return;
    }
    if(res.s===403){toast('{{ __('notAllowed') }}');return;}
    if(res.s===401){localStorage.removeItem(LS_TOKEN);window.location.href='/';return;}
    toast('{{ __('ratingFailed') }}');
  })
  .catch(function(){toast('{{ __('ratingFailed') }}');})
  .then(function(){submitting=false;btn.disabled=score<1;btn.textContent='{{ __('submitRating') }}';});
}
$('rate-form').addEventListener('submit',submitRating);
$('review').addEventListener('input',function(){$('review-count').textContent=$('review').value.length;});
wireStars();
setScore(0);
loadNeed();
})();
</script>
</body>
</html>
