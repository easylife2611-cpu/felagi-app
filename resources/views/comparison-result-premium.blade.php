<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>{{ __('screenS015') }} — {{ __('brand') }}</title>
<meta name="theme-color" content="#003366">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Ethiopic:wght@400;500;600;700;800;900&family=Inter:wght@400;600;700;800;900&display=swap" rel="stylesheet">
<style>
:root{--navy-950:#001a33;--navy-900:#002b52;--navy-800:#003366;--navy-700:#0b4d83;--navy-600:#1a6bb0;--orange-700:#b35c00;--orange-600:#e8851d;--orange-500:#FF9933;--orange-400:#ffb366;--orange-100:#fff2e0;--ink-900:#0d1a2b;--ink-500:#3c4d61;--ink-300:#5f7185;--ink-200:#8fa0b3;--line-100:#eef2f6;--line-200:#e3e9ef;--line-300:#d5dde6;--surface:#fff;--canvas:#f4f7fa;--canvas-2:#eef3f8;--success-600:#0e6b34;--success-500:#147a3d;--success-100:#e8f5ee;--warn-600:#a86500;--warn-100:#fff7e6;--danger-600:#a32e21;--danger-500:#c0392b;--danger-100:#fdecea;--info-600:#0056d6;--info-100:#e7f1ff;--r-xs:8px;--r-sm:12px;--r-md:16px;--r-lg:22px;--r-pill:999px;--sh-xs:0 1px 2px rgba(0,26,51,.04),0 1px 3px rgba(0,26,51,.06);--sh-sm:0 2px 6px rgba(0,26,51,.05),0 4px 12px rgba(0,26,51,.06);--sh-md:0 4px 12px rgba(0,26,51,.06),0 12px 28px rgba(0,26,51,.08);--sh-navy:0 12px 32px rgba(0,51,102,.28);--sh-orange:0 8px 20px rgba(255,153,51,.32);--f-am:'Noto Sans Ethiopic','Inter',system-ui,sans-serif;--f-en:'Inter','Noto Sans Ethiopic',system-ui,sans-serif;--ease:cubic-bezier(.16,.84,.44,1);--ease-out:cubic-bezier(.22,1,.36,1)}
*{box-sizing:border-box;margin:0;padding:0}
html,body{background:var(--canvas);color:var(--ink-900);font-family:var(--f-am);line-height:1.55;-webkit-font-smoothing:antialiased}
body{padding:0 0 60px;min-height:100vh}
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
.sh .t{margin-top:12px;font-size:22px;font-weight:900;letter-spacing:-.03em;line-height:1.2;position:relative;z-index:1;display:flex;align-items:center;gap:10px;flex-wrap:wrap}
.sh .ver{font-size:15px;opacity:.7;font-weight:800;font-family:var(--f-en);letter-spacing:.02em}
.sb{flex:1 1 auto;padding:20px 18px 40px;position:relative;z-index:1}
.cd{background:var(--surface);border:1px solid var(--line-100);border-radius:var(--r-lg);padding:20px;margin-bottom:14px;box-shadow:var(--sh-xs)}
.cd h2{font-size:12px;font-weight:900;color:var(--navy-800);text-transform:uppercase;letter-spacing:.08em;margin-bottom:14px;font-family:var(--f-en)}
.cd .hd{display:flex;justify-content:space-between;align-items:center;margin-bottom:14px;gap:10px;flex-wrap:wrap}
.cd .hd h2{margin-bottom:0}
.bg{display:inline-flex;align-items:center;gap:5px;padding:6px 12px;border-radius:var(--r-pill);font-size:10.5px;font-weight:800;letter-spacing:.05em;font-family:var(--f-en);text-transform:uppercase}
.bg::before{content:'●';font-size:6px}
.bg.pending{background:var(--warn-100);color:var(--warn-600)}
.bg.processing{background:var(--info-100);color:var(--info-600)}
.bg.completed{background:var(--success-100);color:var(--success-600)}
.bg.failed{background:var(--danger-100);color:var(--danger-600)}
.bg.cancelled{background:var(--line-100);color:var(--ink-500)}
.info-row{display:flex;justify-content:space-between;gap:12px;padding:11px 0;border-bottom:1px solid var(--line-100);font-size:13.5px}
.info-row:last-child{border-bottom:none}
.info-row .lbl{color:var(--ink-300);font-weight:800;font-size:11.5px;text-transform:uppercase;letter-spacing:.05em;font-family:var(--f-en)}
.info-row .val{font-weight:900;color:var(--ink-900);text-align:right;font-family:var(--f-en)}
.rank{display:flex;gap:14px;padding:16px;border-radius:var(--r-md);margin-bottom:10px;background:var(--canvas-2);border:1px solid var(--line-200);align-items:flex-start;transition:all .2s var(--ease)}
.rank:hover{transform:translateY(-1px);box-shadow:var(--sh-sm)}
.rank.top{background:linear-gradient(135deg,var(--success-100),#f0faf4);border:1px solid rgba(20,122,61,.24);box-shadow:0 4px 12px rgba(20,122,61,.08)}
.rank .pos{width:36px;height:36px;border-radius:50%;background:linear-gradient(135deg,var(--navy-800),var(--navy-600));color:#fff;display:flex;align-items:center;justify-content:center;font-weight:900;font-size:15px;flex:0 0 auto;font-family:var(--f-en);box-shadow:0 2px 8px rgba(0,51,102,.24)}
.rank.top .pos{background:linear-gradient(135deg,var(--success-500),var(--success-600));box-shadow:0 4px 12px rgba(20,122,61,.32);font-size:18px}
.rank.top .pos::before{content:'★'}
.rank.top .pos{font-size:0}
.rank.top .pos::before{font-size:18px}
.rank .body{flex:1;min-width:0}
.rank .name{font-weight:900;font-size:14.5px;margin-bottom:4px;color:var(--ink-900);letter-spacing:-.01em}
.rank .reason{font-size:12.5px;color:var(--ink-500);line-height:1.6}
.ai-note{border-radius:var(--r-md);padding:14px 16px;font-size:13px;line-height:1.6;margin-bottom:16px;font-weight:600}
.ai-note.processing{background:linear-gradient(135deg,var(--info-100),#f0f7ff);border:1px solid rgba(13,110,253,.18);color:var(--info-600)}
.ai-note.warn{background:linear-gradient(135deg,var(--warn-100),#fffbf0);border:1px solid rgba(168,101,0,.18);color:var(--warn-600)}
.ai-note strong{display:block;margin-bottom:4px;font-weight:900;letter-spacing:-.01em}
.actions{display:flex;gap:10px;flex-wrap:wrap;margin-top:16px}
.bt{display:inline-flex;align-items:center;justify-content:center;gap:9px;height:48px;padding:0 22px;border-radius:var(--r-sm);font-weight:900;font-size:14px;line-height:1;letter-spacing:-.015em;transition:all .2s var(--ease);font-family:var(--f-am);text-align:center;flex:1;min-width:140px}
.bt-n{background:linear-gradient(135deg,var(--navy-800),var(--navy-700));color:#fff}
.bt-n:hover{transform:translateY(-1px);box-shadow:var(--sh-navy)}
.bt-g{background:var(--surface);color:var(--navy-800);border:1.5px solid var(--line-300)}
.bt-g:hover{background:var(--canvas-2);border-color:var(--navy-700)}
.state{padding:60px 24px;text-align:center;color:var(--ink-300)}
.state .ic{font-size:48px;margin-bottom:14px;opacity:.5}
.state h3{font-size:18px;font-weight:900;color:var(--navy-800);margin-bottom:8px}
.state p{font-size:13.5px;line-height:1.6;color:var(--ink-500);max-width:300px;margin:0 auto 16px}
.spinner{display:inline-block;width:26px;height:26px;border:3px solid var(--line-200);border-top-color:var(--navy-800);border-radius:50%;animation:spin .8s linear infinite}
@keyframes spin{to{transform:rotate(360deg)}}
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
    <h1 class="t" id="page-title" tabindex="-1" role="heading" aria-level="1">
      <span>{{ __('comparisonResultTitle') }}</span>
      <span class="ver" id="version-number">#</span>
    </h1>
  </div>
</div>
<div class="sb">
  <div id="state-loading" class="state" style="padding:80px 24px">
    <div class="spinner"></div>
    <p style="margin-top:14px">{{ __('loading') }}...</p>
  </div>

  <div id="state-error" class="state" hidden>
    <div class="ic">⚠️</div>
    <h3>{{ __('loadErrorTitle') }}</h3>
    <p>{{ __('loadErrorBody') }}</p>
    <a href="/my/needs" class="bt bt-n" style="display:inline-flex;max-width:220px;margin:0 auto">{{ __('backToMyNeeds') }}</a>
  </div>

  <div id="content" hidden>
    <div class="cd">
      <div class="hd">
        <h2>{{ __('comparisonVersion') }}</h2>
        <span class="bg" id="status-pill"></span>
      </div>
      <div id="info-rows"></div>
    </div>

    <div id="ai-note" class="ai-note warn" hidden></div>

    <div class="cd" id="results-card" hidden>
      <h2>{{ __('rankingTitle') }}</h2>
      <div id="results-list"></div>
    </div>

    <div class="cd" id="offers-card" hidden>
      <h2>{{ __('includedOffers') }}</h2>
      <div id="offers-list"></div>
    </div>

    <div class="actions">
      <a href="#" id="need-btn" class="bt bt-g">{{ __('viewNeed') }}</a>
      <a href="#" id="history-btn" class="bt bt-n">{{ __('viewHistory') }}</a>
    </div>
  </div>
</div>
</div>
<script>
(function(){
'use strict';
var LS_TOKEN='felagi_token';
var compId='';
function $(id){return document.getElementById(id);}
function getToken(){return 'session';}
function esc(s){return String(s==null?'':s).replace(/[&<>"']/g,function(c){return {'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c];});}
function getCompId(){var parts=window.location.pathname.split('/').filter(Boolean);return parts.length>=2?parts[1]:'';}
function showOnly(name){['state-loading','state-error','content'].forEach(function(id){var el=$(id);if(el)el.hidden=(id!==((name==='content')?'content':'state-'+name));});}
function fmtDate(iso){if(!iso)return '—';var d=new Date(iso);if(isNaN(d))return '—';return d.toLocaleDateString()+' '+d.toLocaleTimeString([],{hour:'2-digit',minute:'2-digit'});}
function statusClass(s){return 'bg '+String(s||'').toLowerCase();}
function statusLabel(s){return ({'PENDING':'{{ __('compStatusPending') }}','PROCESSING':'{{ __('compStatusProcessing') }}','COMPLETED':'{{ __('compStatusCompleted') }}','FAILED':'{{ __('compStatusFailed') }}','CANCELLED':'{{ __('compStatusCancelled') }}'})[s]||s||'';}
function renderInfo(comp){
  var rows=[];
  rows.push(['{{ __('requestedAt') }}', fmtDate(comp.requested_at)]);
  if(comp.completed_at)rows.push(['{{ __('completedAt') }}', fmtDate(comp.completed_at)]);
  if(comp.included_offer_count!=null)rows.push(['{{ __('offerCount') }}', comp.included_offer_count]);
  $('info-rows').innerHTML=rows.map(function(r){
    return '<div class="info-row"><span class="lbl">'+esc(r[0])+'</span><span class="val">'+esc(r[1])+'</span></div>';
  }).join('');
}
function renderAiNote(comp){
  var note=$('ai-note');
  var s=comp.status;
  if(s==='PENDING'||s==='PROCESSING'){
    note.className='ai-note processing';
    note.innerHTML='<strong>{{ __('aiProcessingTitle') }}</strong>{{ __('aiProcessingBody') }}';
    note.hidden=false;
  }else if(s==='FAILED'){
    note.className='ai-note warn';
    note.innerHTML='<strong>{{ __('aiFailedTitle') }}</strong>{{ __('aiFailedBody') }}';
    note.hidden=false;
  }else if(s==='COMPLETED'&&(!comp.results||!comp.results.length)){
    note.className='ai-note warn';
    note.innerHTML='<strong>{{ __('aiNoResultsTitle') }}</strong>{{ __('aiNoResultsBody') }}';
    note.hidden=false;
  }else{
    note.hidden=true;
  }
}
function renderResults(comp){
  var results=comp.results||[];
  if(!results.length){$('results-card').hidden=true;return;}
  $('results-card').hidden=false;
  var sorted=results.slice().sort(function(a,b){return (a.rank||999)-(b.rank||999);});
  $('results-list').innerHTML=sorted.map(function(r,i){
    var cls='rank'+(i===0?' top':'');
    var offer=r.offer||{};
    var prov=offer.provider||{};
    var name=prov.full_name||'Offer #'+(r.offer_id||'').slice(0,6);
    var reason=r.reasoning||r.reason||'';
    return '<div class="'+cls+'"><div class="pos">'+(r.rank||i+1)+'</div><div class="body"><div class="name">'+esc(name)+'</div>'+(reason?'<div class="reason">'+esc(reason)+'</div>':'')+'</div></div>';
  }).join('');
}
function renderOffers(comp){
  var cos=comp.comparison_offers||comp.comparisonOffers||[];
  if(!cos.length){$('offers-card').hidden=true;return;}
  $('offers-card').hidden=false;
  $('offers-list').innerHTML=cos.map(function(co){
    var off=co.offer||{};
    var prov=off.provider||{};
    return '<div class="info-row"><span class="lbl">'+esc(prov.full_name||'—')+'</span><span class="val">'+esc((off.currency||'ETB')+' '+(off.offered_price||'—'))+'</span></div>';
  }).join('');
}
function loadComparison(){
  compId=getCompId();
  if(!compId){showOnly('error');return;}
  fetch('/api/v1/comparisons/'+encodeURIComponent(compId),{credentials:'same-origin',headers:{'Accept':'application/json'}})
  .then(function(r){
    if(r.status===404)throw new Error('notfound');
    if(r.status===401)throw new Error('auth');
    if(r.status===403)throw new Error('denied');
    if(!r.ok)throw new Error('HTTP '+r.status);
    return r.json();
  })
  .then(function(j){
    var comp=(j&&j.data)?j.data:j;
    if(!comp||!comp.id){showOnly('error');return;}
    $('version-number').textContent='#'+(comp.version_number||1);
    var sp=$('status-pill');
    sp.className=statusClass(comp.status);
    sp.textContent=statusLabel(comp.status);
    renderInfo(comp);
    renderAiNote(comp);
    renderResults(comp);
    renderOffers(comp);
    var needId=comp.need_id||(comp.need&&comp.need.id);
    if(needId){
      $('need-btn').href='/needs/'+encodeURIComponent(needId);
      $('history-btn').href='/needs/'+encodeURIComponent(needId)+'/comparisons';
      $('back-btn').href='/needs/'+encodeURIComponent(needId);
    }
    showOnly('content');
  })
  .catch(function(e){
    var m=String(e.message||e);
    if(m==='auth'){localStorage.removeItem(LS_TOKEN);window.location.href='/';return;}
    showOnly('error');
  });
}
loadComparison();
})();
</script>
</body>
</html>
