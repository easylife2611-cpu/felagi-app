<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>{{ __('needCreatedTitle') }} — {{ __('brand') }}</title>
<style>
*{box-sizing:border-box;margin:0;padding:0}
body{font-family:system-ui,-apple-system,sans-serif;background:#F4F6F8;color:#192431;line-height:1.5;min-height:100vh;padding-bottom:80px}
header{background:#003366;color:#fff;height:64px;padding:0 16px;display:flex;align-items:center;gap:12px;position:sticky;top:0;z-index:30}
header a{color:#fff;text-decoration:none;font-size:20px;padding:8px;border-radius:6px;line-height:1}
header .brand{font-size:18px;font-weight:600}
main{max-width:640px;margin:0 auto;padding:16px}
.hero{text-align:center;padding:40px 20px}
.hero .icon{width:80px;height:80px;border-radius:50%;background:#e8f5e9;color:#1b5e20;display:flex;align-items:center;justify-content:center;font-size:44px;margin:0 auto 20px;font-weight:300}
.hero h1{font-size:26px;font-weight:700;color:#1b5e20;margin-bottom:12px}
.hero p{color:#586675;font-size:15px;margin-bottom:24px;max-width:420px;margin-left:auto;margin-right:auto}
.card{background:#fff;border-radius:12px;padding:20px;margin-bottom:16px;box-shadow:0 1px 3px rgba(0,0,0,.08)}
.card h2{font-size:16px;font-weight:600;color:#003366;margin-bottom:14px}
.need-title{font-size:18px;font-weight:600;color:#192431;line-height:1.3;margin-bottom:8px}
.need-meta{font-size:13px;color:#586675;display:flex;flex-wrap:wrap;gap:12px}
.need-meta span{display:inline-flex;align-items:center;gap:4px}
.next-list{display:flex;flex-direction:column;gap:12px}
.next-item{display:flex;gap:12px;padding:12px;background:#f6f8fa;border-radius:8px;align-items:flex-start}
.next-item .num{width:28px;height:28px;border-radius:50%;background:#003366;color:#fff;display:flex;align-items:center;justify-content:center;font-weight:700;font-size:13px;flex:0 0 auto}
.next-item .body{flex:1;min-width:0}
.next-item .title{font-weight:600;font-size:14px;color:#192431;margin-bottom:2px}
.next-item .desc{font-size:12px;color:#586675;line-height:1.4}
.actions{display:flex;flex-direction:column;gap:10px;margin-top:24px}
.btn{padding:14px 20px;border-radius:10px;font-size:15px;font-weight:600;cursor:pointer;border:none;font-family:inherit;text-decoration:none;text-align:center;display:block;transition:background .15s}
.btn.primary{background:#003366;color:#fff}
.btn.primary:hover{background:#002a52}
.btn.sec{background:#eef1f4;color:#192431}
.btn.sec:hover{background:#e0e4e8}
.btn.ghost{background:transparent;color:#003366;border:1px solid #d0d7de}
.btn.ghost:hover{background:#f6f8fa}
.spinner{display:inline-block;width:20px;height:20px;border:3px solid #e0e0e0;border-top-color:#003366;border-radius:50%;animation:spin .8s linear infinite}
@keyframes spin{to{transform:rotate(360deg)}}
.state{padding:60px 20px;text-align:center;color:#586675}
.state h3{color:#003366;font-size:18px;margin:0 0 8px}
</style>
</head>
<body>
<header>
<span class="brand">{{ __('brand') }}</span>
</header>

<main>
<div id="state-loading" class="state" style="padding:80px 20px">
<div class="spinner"></div>
<p style="margin-top:12px">{{ __('loading') }}...</p>
</div>

<div id="state-error" class="state" hidden>
<h3>{{ __('loadErrorTitle') }}</h3>
<p>{{ __('loadErrorBody') }}</p>
<a href="/my/needs" class="btn sec" style="display:inline-block;max-width:200px;margin-top:16px">{{ __('backToMyNeeds') }}</a>
</div>

<div id="content" hidden>
<div class="hero">
<div class="icon">&#10003;</div>
<h1>{{ __('needCreatedTitle') }}</h1>
<p>{{ __('needCreatedBody') }}</p>
</div>

<div class="card" id="need-card" hidden>
<h2>{{ __('needDetails') }}</h2>
<div class="need-title" id="need-title"></div>
<div class="need-meta" id="need-meta"></div>
</div>

<div class="card">
<h2>{{ __('whatNext') }}</h2>
<div class="next-list">
<div class="next-item">
<div class="num">1</div>
<div class="body">
<div class="title">{{ __('nextStep1Title') }}</div>
<div class="desc">{{ __('nextStep1Desc') }}</div>
</div>
</div>
<div class="next-item">
<div class="num">2</div>
<div class="body">
<div class="title">{{ __('nextStep2Title') }}</div>
<div class="desc">{{ __('nextStep2Desc') }}</div>
</div>
</div>
<div class="next-item">
<div class="num">3</div>
<div class="body">
<div class="title">{{ __('nextStep3Title') }}</div>
<div class="desc">{{ __('nextStep3Desc') }}</div>
</div>
</div>
</div>
</div>

<div class="actions">
<a href="#" id="btn-view" class="btn primary">{{ __('viewNeed') }}</a>
<a href="#" id="btn-offers" class="btn sec">{{ __('viewReceivedOffers') }}</a>
<a href="/needs/new" class="btn ghost">{{ __('createAnother') }}</a>
</div>
</div>
</main>

<script>
(function(){
'use strict';
var LS_TOKEN='felagi_token';
function $(id){return document.getElementById(id);}
function getToken(){return 'session'; /* L305d */}
function esc(s){return String(s==null?'':s).replace(/[&<>"']/g,function(c){return {'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c];});}

function getNeedId(){
  var parts=window.location.pathname.split('/').filter(Boolean);
  return parts.length>=2?parts[1]:'';
}

function showOnly(name){
  ['state-loading','state-error','content'].forEach(function(id){
    var el=$(id); if(el)el.hidden=(id!==((name==='content')?'content':'state-'+name));
  });
}

function fmtDate(iso){
  if(!iso)return '';
  var d=new Date(iso);
  if(isNaN(d))return '';
  return d.toLocaleDateString();
}

function fmtBudget(n){
  var cur=n.currency||'ETB';
  var mn=n.budget_min!=null?Number(n.budget_min):null;
  var mx=n.budget_max!=null?Number(n.budget_max):null;
  if(mn==null&&mx==null)return '{{ __('negotiable') }}';
  if(mn!=null&&mx!=null&&mn!==mx)return cur+' '+mn+' - '+mx;
  return cur+' '+(mn!=null?mn:mx);
}

function loadNeed(){
  var nid=getNeedId();
  if(!nid){showOnly('error');return;}
  var token=getToken();
  /* L305d */

  fetch('/api/v1/needs/'+encodeURIComponent(nid),{
    credentials:'same-origin',headers:{'Accept':'application/json'}
  })
  .then(function(r){
    if(r.status===404)throw new Error('notfound');
    if(r.status===401)throw new Error('auth');
    if(!r.ok)throw new Error('HTTP '+r.status);
    return r.json();
  })
  .then(function(j){
    var need=(j&&j.data)?j.data:j;
    if(!need||!need.id){showOnly('error');return;}

    $('need-card').hidden=false;
    $('need-title').textContent=need.title||'';
    var meta=[];
    if(need.status)meta.push('<span>&#128203; '+esc(need.status)+'</span>');
    if(need.location_text)meta.push('<span>&#128205; '+esc(need.location_text)+'</span>');
    meta.push('<span>&#128176; '+esc(fmtBudget(need))+'</span>');
    if(need.created_at)meta.push('<span>&#128197; '+esc(fmtDate(need.created_at))+'</span>');
    $('need-meta').innerHTML=meta.join('');

    $('btn-view').href='/needs/'+encodeURIComponent(need.id);
    $('btn-offers').href='/needs/'+encodeURIComponent(need.id)+'/offers';

    showOnly('content');
  })
  .catch(function(e){
    var m=String(e.message||e);
    if(m==='auth'){localStorage.removeItem(LS_TOKEN);window.location.href='/';return;}
    showOnly('error');
  });
}

loadNeed();
})();
</script>
</body>
</html>
