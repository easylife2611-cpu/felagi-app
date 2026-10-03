<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>{{ __('screenS006') }} — {{ __('brand') }}</title>
<style>
*{box-sizing:border-box;margin:0;padding:0}
body{font-family:system-ui,-apple-system,sans-serif;background:#F4F6F8;color:#192431;line-height:1.5;min-height:100vh;padding-bottom:100px}
header{background:#003366;color:#fff;height:64px;padding:0 16px;display:flex;align-items:center;gap:12px;position:sticky;top:0;z-index:30}
header a{color:#fff;text-decoration:none;font-size:20px;padding:8px;border-radius:6px;line-height:1}
header .title{font-size:16px;font-weight:600;flex:1}
main{max-width:640px;margin:0 auto;padding:16px}
.notice{background:#e3f2fd;border:1px solid #bbdefb;color:#0d47a1;padding:12px 14px;border-radius:8px;font-size:13px;margin-bottom:16px;line-height:1.5}
.card{background:#fff;border-radius:12px;padding:18px;margin-bottom:16px;box-shadow:0 1px 3px rgba(0,0,0,.08)}
.card h2{font-size:16px;font-weight:600;color:#003366;margin-bottom:12px}
.need-cat{display:inline-block;padding:4px 12px;border-radius:999px;background:#e8eef4;color:#003366;font-size:12px;font-weight:600;margin-bottom:10px}
.need-title{font-size:20px;font-weight:700;color:#192431;line-height:1.3;margin-bottom:12px}
.need-desc{font-size:14px;color:#3a4a5a;line-height:1.6;white-space:pre-wrap;margin-bottom:14px}
.meta-grid{display:grid;grid-template-columns:1fr 1fr;gap:10px;margin-top:12px}
.meta-item{background:#f6f8fa;padding:10px 12px;border-radius:8px}
.meta-item .lbl{font-size:10px;color:#586675;text-transform:uppercase;letter-spacing:.5px;font-weight:600;margin-bottom:2px}
.meta-item .val{font-size:14px;font-weight:600;color:#192431;word-break:break-word}
.meta-item.full{grid-column:1/-1}
.meta-item .budget{color:#1b5e20}
.actions{position:fixed;bottom:0;left:0;right:0;background:#fff;border-top:1px solid #eef1f4;padding:12px 16px;display:flex;gap:10px;max-width:640px;margin:0 auto;z-index:20;box-shadow:0 -1px 3px rgba(0,0,0,.04)}
.btn{flex:1;padding:14px 20px;border-radius:10px;font-size:15px;font-weight:600;cursor:pointer;border:none;font-family:inherit;text-decoration:none;text-align:center;display:block;transition:background .15s}
.btn.primary{background:#003366;color:#fff}
.btn.primary:hover{background:#002a52}
.btn.sec{background:#eef1f4;color:#192431}
.btn.sec:hover{background:#e0e4e8}
.state{padding:80px 20px;text-align:center;color:#586675}
.state h3{color:#003366;font-size:18px;margin:0 0 8px}
.state p{margin:0 0 16px}
.draft-badge{background:#fff3e0;color:#bf360c;padding:4px 10px;border-radius:999px;font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.5px}

:focus-visible{outline:3px solid #1b5e20;outline-offset:2px;border-radius:6px}
#page-title:focus{outline:none}
.sr-only{position:absolute;width:1px;height:1px;padding:0;margin:-1px;overflow:hidden;clip:rect(0,0,0,0);white-space:nowrap;border:0}
</style>
</head>
<body>
<header>
<a href="#" id="back-btn" title="{{ __('back') }}">&#8592;</a>
<span class="title">{{ __('previewTitle') }}</span>
<span class="draft-badge">{{ __('draft') }}</span>
</header>

<main role="main" aria-labelledby="page-title">
<div class="notice">
<strong>{{ __('previewNoticeTitle') }}</strong><br>
{{ __('previewNoticeBody') }}
</div>

<div id="state-loading" class="state">
<p>{{ __('loading') }}...</p>
</div>

<div id="state-empty" class="state" hidden>
<h3>{{ __('noDraftTitle') }}</h3>
<p>{{ __('noDraftBody') }}</p>
<a href="/needs/new" class="btn primary" style="display:inline-block;max-width:220px;margin:0 auto">{{ __('createNeed') }}</a>
</div>

<div id="content" hidden>
<div class="card">
<span class="need-cat" id="need-cat"></span>
<div class="need-title" id="need-title"></div>
<div class="need-desc" id="need-desc"></div>
<div class="meta-grid" id="need-meta"></div>
</div>

<div class="card">
<h2 id="page-title" tabindex="-1">{{ __('howItLooks') }}</h2>
<p style="font-size:13px;color:#586675;line-height:1.6">{{ __('howItLooksBody') }}</p>
</div>
</div>
</main>

<div class="actions" id="actions" hidden>
<a href="#" id="btn-edit" class="btn sec">{{ __('backToEdit') }}</a>
<button type="button" id="btn-publish" class="btn primary">{{ __('publishNow') }}</button>
</div>

<div class="toast" id="toast" style="position:fixed;bottom:120px;left:50%;transform:translateX(-50%);background:#192431;color:#fff;padding:12px 20px;border-radius:8px;font-size:14px;z-index:40;opacity:0;pointer-events:none;transition:opacity .2s;max-width:90vw"></div>

<script>
// L342: locale-aware category name (respects app locale)
function felagiLocalizedName(obj) {
  if (!obj) return '';
  var am = document.documentElement.lang === 'am';
  return am
    ? (obj.name_am || obj.name_en || obj.slug || '')
    : (obj.name_en || obj.name_am || obj.slug || '');
}

(function(){
'use strict';
var csrf=(document.querySelector('meta[name="csrf-token"]')||{}).content||'';
var LS_TOKEN='felagi_token';
var DRAFT_KEY='felagi_draft_s005';
var draft=null;

function $(id){return document.getElementById(id);}
function getToken(){return 'session'; /* L305d */}
function esc(s){return String(s==null?'':s).replace(/[&<>"']/g,function(c){return {'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c];});}

function toast(msg,ms){ms=ms||2400;var el=$('toast');el.textContent=msg;el.style.opacity='1';setTimeout(function(){el.style.opacity='0';},ms);}

function showOnly(name){
  ['state-loading','state-empty','content'].forEach(function(id){
    var el=$(id); if(el)el.hidden=(id!==((name==='content')?'content':'state-'+name));
  });
  var a=$('actions'); if(a)a.hidden=(name!=='content');
}

function fmtBudget(d){
  var cur=d.currency||'ETB';
  var mn=d.budget_min!=null?Number(d.budget_min):null;
  var mx=d.budget_max!=null?Number(d.budget_max):null;
  if(mn==null&&mx==null)return '{{ __('negotiable') }}';
  if(mn!=null&&mx!=null&&mn!==mx)return cur+' '+mn+' - '+mx;
  return cur+' '+(mn!=null?mn:mx);
}

function fmtDate(iso){
  if(!iso)return '';
  var d=new Date(iso);
  if(isNaN(d))return '';
  return d.toLocaleDateString();
}

function loadDraft(){
  try{
    var raw=localStorage.getItem(DRAFT_KEY);
    if(!raw)return null;
    var parsed=JSON.parse(raw);
    return parsed&&parsed.data?parsed.data:null;
  }catch(e){return null;}
}

function render(){
  draft=loadDraft();
  if(!draft||!draft.title){
    showOnly('empty');
    return;
  }

  // Look up category name if possible (best-effort via API)
  var token=getToken();
  if(token&&draft.category_id){
    fetch('/api/v1/categories',{credentials:'same-origin',headers:{'Accept':'application/json'}})
      .then(function(r){return r.ok?r.json():null;})
      .then(function(j){
        if(j&&j.data){
          var c=j.data.find(function(x){return x.id===draft.category_id;});
          if(c)$('need-cat').textContent=felagiLocalizedName(c);
        }
      })
      .catch(function(){});
  }

  $('need-title').textContent=draft.title||'';
  $('need-desc').textContent=draft.description||'';
  $('need-cat').textContent=draft.category_id?'...':'';

  var meta='';
  meta+='<div class="meta-item"><div class="lbl">{{ __('budgetLabel') }}</div><div class="val budget">'+esc(fmtBudget(draft))+'</div></div>';
  if(draft.location_text)meta+='<div class="meta-item"><div class="lbl">{{ __('location') }}</div><div class="val">'+esc(draft.location_text)+'</div></div>';
  if(draft.quantity)meta+='<div class="meta-item"><div class="lbl">{{ __('quantity') }}</div><div class="val">'+esc(draft.quantity)+'</div></div>';
  if(draft.deadline_at)meta+='<div class="meta-item"><div class="lbl">{{ __('deadline') }}</div><div class="val">'+esc(fmtDate(draft.deadline_at))+'</div></div>';
  if(draft.offer_deadline_at)meta+='<div class="meta-item full"><div class="lbl">{{ __('offerDeadline') }}</div><div class="val">'+esc(fmtDate(draft.offer_deadline_at))+'</div></div>';
  $('need-meta').innerHTML=meta;

  showOnly('content');
}

function publish(){
  if(!draft)return;
  var token=getToken();
  /* L305d */

  var btn=$('btn-publish');
  btn.disabled=true;
  btn.textContent='{{ __('publishing') }}...';

  var payload={
    title:draft.title,
    description:draft.description,
    category_id:draft.category_id,
    location_text:draft.location_text||null,
    budget_min:draft.budget_min||null,
    budget_max:draft.budget_max||null,
    currency:draft.currency||'ETB',
    quantity:draft.quantity||null,
    deadline_at:draft.deadline_at||null,
    offer_deadline_at:draft.offer_deadline_at||null,
    telegram_publication_acknowledged:true
  };

  fetch('/api/v1/needs',{
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
      var needId=(res.body.data&&res.body.data.id)||(res.body.id);
      try{localStorage.removeItem(DRAFT_KEY);}catch(e){}
      toast('{{ __('needCreated') }}');
      setTimeout(function(){
        window.location.href=needId?('/needs/'+needId+'/created'):'/my/needs';
      },800);
      return;
    }
    if(res.s===422){
      toast('{{ __('validationFailed') }}');
      return;
    }
    if(res.s===401){
      localStorage.removeItem(LS_TOKEN);
      window.location.href='/';
      return;
    }
    toast('{{ __('saveFailed') }}');
  })
  .catch(function(){toast('{{ __('saveFailed') }}');})
  .then(function(){
    btn.disabled=false;
    btn.textContent='{{ __('publishNow') }}';
  });
}

$('btn-edit').href='/needs/new';
$('back-btn').href='/needs/new';
$('btn-publish').addEventListener('click',publish);

render();
})();
</script>
</body>
</html>
