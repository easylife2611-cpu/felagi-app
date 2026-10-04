<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>{{ __('screenS004') }} — {{ __('brand') }}</title>
<style>
body{font-family:system-ui,-apple-system,sans-serif;background:#F4F6F8;color:#192431;margin:0;padding-bottom:70px}
header{background:#003366;color:#fff;padding:16px 20px;display:flex;justify-content:space-between;align-items:center;position:sticky;top:0;z-index:30}
header .brand{font-size:20px;font-weight:700}
header a{color:#fff;text-decoration:none;font-size:14px}
main{padding:16px;max-width:1200px;margin:0 auto}
#keyword{width:100%;height:48px;padding:0 14px;border:1px solid #d0d7de;border-radius:10px;font-size:15px;box-sizing:border-box}
#keyword:focus{outline:none;border-color:#003366;box-shadow:0 0 0 3px rgba(0,51,102,.1)}
.chips{display:flex;gap:8px;overflow-x:auto;padding:12px 0;scrollbar-width:none}
.chips::-webkit-scrollbar{display:none}
.chip{flex:0 0 auto;padding:8px 16px;border:1px solid #d0d7de;border-radius:999px;background:#fff;cursor:pointer;font-size:14px;font-family:inherit;white-space:nowrap}
.chip.active{background:#003366;color:#fff;border-color:#003366}
.filters{display:flex;gap:12px;align-items:center;margin-bottom:16px;flex-wrap:wrap}
.filters select{padding:8px 12px;border:1px solid #d0d7de;border-radius:8px;font-size:14px;font-family:inherit}
.filters button{padding:8px 14px;background:transparent;border:1px solid #d0d7de;border-radius:8px;cursor:pointer;font-size:14px;font-family:inherit;color:#586675}
.feed{display:grid;grid-template-columns:1fr;gap:12px}
@media(min-width:640px){.feed{grid-template-columns:1fr 1fr}}
@media(min-width:1024px){.feed{grid-template-columns:1fr 1fr 1fr}}
.card{background:#fff;padding:16px;border-radius:10px;text-decoration:none;color:inherit;box-shadow:0 1px 3px rgba(0,0,0,.08);display:flex;flex-direction:column;gap:6px}
.card:hover{box-shadow:0 4px 12px rgba(0,0,0,.1)}
.card h3{margin:0;font-size:16px;font-weight:600}
.card p{margin:0;color:#586675;font-size:13px}
.card .budget{color:#1b5e20;font-weight:600;font-size:14px;margin-top:auto}
.adslot{margin:16px 0;padding:12px;background:#fafbfc;border:1px dashed #d0d7de;border-radius:8px;text-align:center;font-size:12px;color:#586675;min-height:60px}
.fab{position:fixed;right:20px;bottom:80px;width:56px;height:56px;border-radius:50%;background:#003366;color:#fff;display:flex;align-items:center;justify-content:center;font-size:28px;text-decoration:none;box-shadow:0 4px 12px rgba(0,51,102,.35);z-index:25}
.bn{position:fixed;bottom:0;left:0;right:0;height:64px;background:#fff;border-top:1px solid #eef1f4;display:flex;z-index:20}
.bn a{flex:1;display:flex;flex-direction:column;align-items:center;justify-content:center;text-decoration:none;color:#586675;font-size:11px;gap:2px}
.bn a.active{color:#003366;font-weight:600}
.bn .ic{font-size:20px;line-height:1}
@media(min-width:600px){
.bn{position:fixed;left:0;top:0;bottom:0;right:auto;width:88px;height:100vh;flex-direction:column;border-top:none;border-right:1px solid #eef1f4;padding:80px 0 16px;box-shadow:1px 0 3px rgba(0,0,0,.04);z-index:25;background:#fff}
.bn a{padding:14px 4px;font-size:10px;gap:4px}
body{padding-left:88px;padding-bottom:16px}
header{margin-left:-88px;padding-left:calc(88px + 16px)}
.fab{bottom:24px;right:24px}
}
@media(min-width:1200px){
.bn{width:240px;padding-top:96px}
.bn a{flex-direction:row;justify-content:flex-start;padding:14px 24px;font-size:14px;gap:14px}
.bn .ic{font-size:22px}
body{padding-left:240px}
header{margin-left:-240px;padding-left:calc(240px + 16px)}
}
.state{padding:60px 20px;text-align:center;color:#586675}
.state h3{color:#003366;font-size:18px;margin:0 0 8px}
.btn{padding:10px 20px;border-radius:8px;font-size:14px;font-weight:600;cursor:pointer;border:none;font-family:inherit;text-decoration:none;display:inline-block;background:#003366;color:#fff}
.skeleton{background:#fff;padding:16px;border-radius:10px;box-shadow:0 1px 3px rgba(0,0,0,.08)}
.skeleton div{height:12px;background:#eef1f4;border-radius:4px;margin-bottom:8px}
.skeleton div:nth-child(1){width:40%}
.skeleton div:nth-child(2){width:90%}
.skeleton div:nth-child(3){width:70%}

:focus-visible{outline:3px solid #1b5e20;outline-offset:2px;border-radius:6px}
#page-title:focus{outline:none}
.sr-only{position:absolute;width:1px;height:1px;padding:0;margin:-1px;overflow:hidden;clip:rect(0,0,0,0);white-space:nowrap;border:0}
</style>
</head>
<body>
<header>
<span class="brand">{{ __('brand') }}</span>
<a href="#" onclick="felagiLogout();return false;">{{ __('logout') }}</a>
<span class="lang-switch" style="margin-left:12px;font-size:13px">
  <a href="/lang/am" style="color:#fff;text-decoration:{{ app()->getLocale()==='am'?'underline':'none' }};margin-right:6px">አማ</a>
  <a href="/lang/en" style="color:#fff;text-decoration:{{ app()->getLocale()==='en'?'underline':'none' }}">EN</a>
</span>
</header>
<main role="main" aria-labelledby="page-title">
<h1 id="page-title" tabindex="-1" class="sr-only">{{ __('screenS004') }}</h1>
<input type="search" id="keyword" placeholder="{{ __('searchPlaceholder') }}" maxlength="255" autocomplete="off">
<div class="chips" id="chips"></div>
<div class="filters">
<label>{{ __('sortBy') }}:</label>
<select id="sort" aria-label="{{ __('sortBy') }}">
<option value="newest">{{ __('sortNewest') }}</option>
<option value="budget_low">{{ __('sortBudgetLow') }}</option>
<option value="budget_high">{{ __('sortBudgetHigh') }}</option>
<option value="deadline_soon">{{ __('sortDeadlineSoon') }}</option>
</select>
<button type="button" id="clear">{{ __('clearFilters') }}</button>
</div>
<div class="adslot" data-ad-slot="AD_BROWSE_INLINE_01"></div>
<div id="feed" class="feed">
<div class="skeleton"><div></div><div></div><div></div></div>
<div class="skeleton"><div></div><div></div><div></div></div>
<div class="skeleton"><div></div><div></div><div></div></div>
</div>
<div id="empty" class="state" hidden><h3>{{ __('noNeedsTitle') }}</h3><p>{{ __('noNeedsBody') }}</p></div>
<div id="error" class="state" hidden><h3>{{ __('loadErrorTitle') }}</h3><p>{{ __('loadErrorBody') }}</p><p><button class="btn" onclick="load()">{{ __('retry') }}</button></p></div>
<div class="adslot" data-ad-slot="AD_SEARCH_RESULTS_INLINE_01"></div>
</main>
<a href="/needs/new" class="fab" title="{{ __('createNeed') }}">+</a>
<nav class="bn">
<a href="/browse" class="active"><span class="ic">&#128269;</span><span>{{ __('navBrowse') }}</span></a>
<a href="/my/needs"><span class="ic">&#128203;</span><span>{{ __('navMyNeeds') }}</span></a>
<a href="/my/offers"><span class="ic">&#127991;</span><span>{{ __('navMyOffers') }}</span></a>
<a href="/notifications"><span class="ic">&#128276;</span><span>{{ __('navNotifications') }}</span></a>
<a href="/profile"><span class="ic">&#128100;</span><span>{{ __('navProfile') }}</span></a>
</nav>
<script>
// L342: locale-aware category name (respects app locale)
function felagiLocalizedName(obj) {
  if (!obj) return '';
  var am = document.documentElement.lang === 'am';
  return am
    ? (obj.name_am || obj.name_en || obj.slug || '')
    : (obj.name_en || obj.name_am || obj.slug || '');
}
var ALL_CATEGORIES_LABEL = '{{ __("allCategories") }}';


(function(){
'use strict';
var S={keyword:'',cat:null,sort:'newest',items:[]};
function $(id){return document.getElementById(id);}
function esc(s){return String(s==null?'':s).replace(/[&<>"']/g,function(c){return {'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c];});}
window.felagiLogout=function(){/* L305d cookie session */var csrf=(document.querySelector('meta[name="csrf-token"]')||{}).content||'';fetch('/api/v1/auth/logout',{method:'POST',credentials:'same-origin',headers:{'Accept':'application/json','X-CSRF-TOKEN':csrf}}).catch(function(){});setTimeout(function(){window.location.href='/';},200)};
function loadCats(){
  fetch('/api/v1/categories',{credentials:'same-origin',headers:{'Accept':'application/json'}})
    .then(function(r){return r.ok?r.json():{data:[]};})
    .then(function(j){
      var c=j.data||[];
      $('chips').innerHTML='<button class="chip active" data-id="">' + ALL_CATEGORIES_LABEL + '</button>'+c.map(function(x){return '<button class="chip" data-id="'+esc(x.id)+'">'+esc(felagiLocalizedName(x))+'</button>';}).join('');
      var btns=$('chips').querySelectorAll('.chip');
      Array.prototype.forEach.call(btns,function(b){
        b.onclick=function(){
          S.cat=b.dataset.id||null;
          Array.prototype.forEach.call(btns,function(x){x.classList.toggle('active',x===b);});
          load();
        };
      });
    })
    .catch(function(e){console.warn('[S004] cats',e);});
}
function load(){
  var q=new URLSearchParams();
  if(S.keyword)q.set('keyword',S.keyword);
  if(S.cat)q.set('category_id',S.cat);
  if(S.sort)q.set('sort',S.sort);
  var h={'Accept':'application/json'}/* L305d */;
  /* L305d: cookie auth — tok guard removed (L327) */
  fetch('/api/v1/needs?'+q.toString(),{credentials:'same-origin',headers:h})
    .then(function(r){
      if(r.status===429)throw new Error('rate-limited');
      if(!r.ok)throw new Error('HTTP '+r.status);
      return r.json();
    })
    .then(function(j){
      var items=j.data||[];
      $('error').hidden=true;
      if(!items.length){$('feed').innerHTML='';$('empty').hidden=false;return;}
      $('empty').hidden=true;
      $('feed').innerHTML=items.map(function(it){
        var cur=it.currency||'ETB';
        var bud=it.budget_min!=null?cur+' '+it.budget_min:(it.budget_max!=null?cur+' '+it.budget_max:'');
        return '<a class="card" href="/needs/'+esc(it.id)+'"><h3>'+esc(it.title)+'</h3><p>'+esc((it.description||'').slice(0,120))+'</p><div class="budget">'+esc(bud)+'</div></a>';
      }).join('');
    })
    .catch(function(e){console.warn('[S004] needs',e);$('feed').innerHTML='';$('error').hidden=false;});
}
var tm=null;
$('keyword').addEventListener('input',function(e){
  clearTimeout(tm);
  tm=setTimeout(function(){S.keyword=e.target.value.trim().slice(0,255);load();},300);
});
$('sort').addEventListener('change',function(e){S.sort=e.target.value;load();});
$('clear').addEventListener('click',function(){
  S.keyword='';S.cat=null;S.sort='newest';
  $('keyword').value='';$('sort').value='newest';
  var btns=$('chips').querySelectorAll('.chip');
  Array.prototype.forEach.call(btns,function(x,i){x.classList.toggle('active',i===0);});
  load();
});
loadCats();
load();
})();
</script>
</body>
</html>
