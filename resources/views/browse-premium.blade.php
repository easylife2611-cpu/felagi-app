<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="csrf-token" content="{{ csrf_token() }}">
<meta name="theme-color" content="#001a33">
<title>{{ __('screenS004') }} — {{ __('brand') }}</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Ethiopic:wght@400;500;600;700;800;900&family=Inter:wght@400;600;700;800&display=swap" rel="stylesheet">
<style>
:root{--navy-950:#001a33;--navy-900:#002b52;--navy-800:#003366;--navy-700:#0b4d83;--orange-700:#b35c00;--orange-600:#e8851d;--orange-500:#FF9933;--orange-400:#ffb366;--orange-100:#fff2e0;--ink-900:#0d1a2b;--ink-700:#132238;--ink-500:#3c4d61;--ink-300:#5f7185;--ink-200:#8fa0b3;--line-100:#eef2f6;--line-200:#e3e9ef;--line-300:#d5dde6;--surface:#fff;--canvas:#f4f7fa;--canvas-2:#eef3f8;--success-600:#0e6b34;--success-100:#e8f5ee;--danger-600:#a32e21;--danger-100:#fdecea;--info-600:#0056d6;--info-100:#e7f1ff;--warn-600:#a86500;--warn-100:#fff7e6;--r-sm:12px;--r-md:16px;--r-lg:22px;--r-pill:999px;--f-am:'Noto Sans Ethiopic','Inter',system-ui,sans-serif;--f-en:'Inter','Noto Sans Ethiopic',system-ui,sans-serif;--ease:cubic-bezier(.16,.84,.44,1);--ease-out:cubic-bezier(.22,1,.36,1)}
*{box-sizing:border-box;margin:0;padding:0}
html,body{background:var(--canvas);min-height:100vh;font-family:var(--f-am);color:var(--ink-900);line-height:1.55;-webkit-font-smoothing:antialiased}
body{padding:0 0 90px}
a{color:inherit;text-decoration:none}
button{font-family:inherit;cursor:pointer;border:0;background:transparent;color:inherit}
input,select{font-family:inherit;font-size:inherit}
:focus-visible{outline:3px solid var(--orange-500);outline-offset:2px;border-radius:6px}
.hidden{display:none!important}
.sr-only{position:absolute;width:1px;height:1px;padding:0;margin:-1px;overflow:hidden;clip:rect(0,0,0,0);white-space:nowrap;border:0}
.topbar{background:radial-gradient(ellipse at top right,rgba(255,153,51,.15),transparent 50%),linear-gradient(140deg,var(--navy-950),var(--navy-800));color:#fff;padding:20px 22px 22px;position:sticky;top:0;z-index:30;box-shadow:0 6px 24px rgba(0,20,40,.24)}
.topbar .tr{display:flex;align-items:center;justify-content:space-between;gap:12px}
.topbar .br{font-size:16px;font-weight:900;letter-spacing:-.03em;display:flex;align-items:center;gap:8px}
.topbar .br svg{flex-shrink:0}
.topbar .ha{display:flex;gap:6px;align-items:center}
.topbar .ib{width:36px;height:36px;border-radius:50%;display:inline-flex;align-items:center;justify-content:center;color:#fff;background:rgba(255,255,255,.1);border:1px solid rgba(255,255,255,.08);position:relative}
.topbar .ib:hover{background:rgba(255,255,255,.18)}
.topbar .ib svg{width:18px;height:18px;stroke:currentColor;fill:none;stroke-width:2;stroke-linecap:round;stroke-linejoin:round}
.topbar .ib .dot{position:absolute;top:7px;right:7px;width:8px;height:8px;background:var(--orange-500);border-radius:50%;border:2px solid var(--navy-800)}
.topbar .t{margin-top:16px;font-size:24px;font-weight:900;letter-spacing:-.03em;line-height:1.2}
.topbar .st{margin-top:4px;font-size:13px;opacity:.75;font-weight:500}
.topbar .lang a{font-size:12px;color:#fff;margin-left:8px;opacity:.7;text-decoration:none}
.topbar .lang a.is-active{opacity:1;text-decoration:underline;font-weight:800}
.wrap{max-width:640px;margin:0 auto;padding:22px 18px 30px}
.search-primary{margin-bottom:18px}
.search-bar{position:relative;display:flex;align-items:center;background:#fff;border:2px solid var(--line-200);border-radius:16px;box-shadow:0 4px 12px rgba(0,51,102,.06);transition:border-color .15s,box-shadow .15s}
.search-bar:focus-within{border-color:var(--navy-800);box-shadow:0 6px 16px rgba(0,51,102,.1)}
.search-icon{flex:0 0 20px;width:20px;height:20px;margin-left:16px;color:var(--ink-300)}
.search-icon svg{width:100%;height:100%;stroke:currentColor;fill:none;stroke-width:2;stroke-linecap:round;stroke-linejoin:round}
.search-bar input{flex:1;min-width:0;height:52px;padding:0 12px;border:0;background:transparent;font-size:15px;color:var(--ink-900);outline:none}
.search-bar input::placeholder{color:var(--ink-200)}
.search-clear{display:none;flex:0 0 32px;width:32px;height:32px;margin-right:10px;border-radius:50%;background:var(--canvas-2);color:var(--ink-500);font-size:18px;line-height:1;align-items:center;justify-content:center}
.search-clear.is-visible{display:inline-flex}
.search-tools{display:flex;gap:6px;flex-wrap:wrap;margin-top:10px}
.search-tool{border:1px solid var(--line-200);background:#fff;color:var(--navy-800);border-radius:var(--r-pill);padding:6px 10px;font-size:11.5px;font-weight:700}
.search-tool:hover{background:var(--canvas-2)}
.search-history{display:none;gap:6px;flex-wrap:wrap;margin-top:10px}
.search-history.show{display:flex}
.search-history button{border:0;background:var(--canvas-2);color:var(--ink-500);border-radius:8px;padding:5px 9px;font-size:11.5px}
.section-head{display:flex;justify-content:space-between;align-items:center;gap:12px;margin:18px 0 10px}
.section-head h2{font-size:12.5px;font-weight:900;color:var(--navy-800);text-transform:uppercase;letter-spacing:.1em;font-family:var(--f-en)}
.section-more{font-size:12px;color:var(--navy-800);font-weight:800;padding:5px 9px;border-radius:8px}
.section-more:hover{background:var(--canvas-2)}
.chips{display:flex;gap:8px;flex-wrap:wrap}
.chips[data-limited="true"] .chip:nth-child(n+6){display:none}
.chip{flex:0 0 auto;padding:8px 14px;border:1px solid var(--line-200);background:#fff;color:var(--ink-700);border-radius:var(--r-pill);font-size:13px;font-weight:700;transition:all .15s}
.chip:hover{border-color:var(--navy-700)}
.chip.active{background:var(--navy-800);color:#fff;border-color:var(--navy-800);box-shadow:0 4px 10px rgba(0,51,102,.2)}
.filter-chips{display:flex;gap:8px;flex-wrap:wrap}
.filter-chip{padding:7px 12px;border:1px solid var(--line-200);background:#fff;color:var(--ink-500);border-radius:var(--r-pill);font-size:12px;font-weight:700}
.filter-chip:hover{border-color:var(--navy-700);color:var(--navy-800)}
.filter-chip.active{background:var(--info-100);color:var(--navy-800);border-color:var(--navy-800);font-weight:800}
.filter-chip.active::before{content:'\2713 ';font-weight:800}
.feed{display:flex;flex-direction:column;gap:12px}
.lst{background:#fff;border:1px solid var(--line-200);border-left:4px solid var(--orange-500);border-radius:18px;padding:18px;display:flex;flex-direction:column;gap:10px;text-decoration:none;color:inherit;position:relative;transition:transform .2s,box-shadow .2s}
.lst:hover{transform:translateY(-2px);box-shadow:0 12px 24px rgba(0,51,102,.1)}
.lst .ribbon{position:absolute;top:-8px;right:12px;background:var(--orange-500);color:#fff;font-size:10px;font-weight:900;padding:3px 8px;border-radius:var(--r-pill)}
.lst .cat-strip{display:flex;gap:6px;flex-wrap:wrap}
.bg{display:inline-flex;align-items:center;font-size:10.5px;font-weight:800;padding:3px 8px;border-radius:var(--r-pill)}
.bg.info{background:var(--info-100);color:var(--info-600)}
.bg.ok{background:var(--success-100);color:var(--success-600)}
.bg.warn{background:var(--warn-100);color:var(--warn-600)}
.lst h4{font-size:17px;font-weight:900;color:var(--ink-900);letter-spacing:-.02em;line-height:1.3}
.budget{font-size:22px;font-weight:900;color:var(--success-600);letter-spacing:-.02em;font-family:var(--f-en)}
.lst .meta{display:flex;gap:10px;flex-wrap:wrap;font-size:11.5px;color:var(--ink-300)}
.lst .desc{font-size:13px;color:var(--ink-500);line-height:1.5;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden}
.lst .foot{display:flex;justify-content:space-between;align-items:center;border-top:1px solid var(--line-100);padding-top:10px;margin-top:2px}
.match{font-size:11.5px;font-weight:800;color:var(--success-600);background:var(--success-100);border-radius:var(--r-pill);padding:4px 9px}
.cta{font-size:12px;font-weight:800;color:var(--navy-800);background:var(--canvas-2);border-radius:8px;padding:6px 10px}
.state{padding:60px 20px;text-align:center;color:var(--ink-300)}
.state h3{font-size:17px;color:var(--navy-800);margin-bottom:6px;font-weight:900}
.state p{font-size:13.5px;margin-bottom:14px}
.skeleton{background:#fff;border:1px solid var(--line-200);border-radius:16px;padding:16px}
.skeleton div{height:12px;background:var(--canvas-2);border-radius:4px;margin-bottom:8px}
.skeleton div:nth-child(1){width:40%}
.skeleton div:nth-child(2){width:90%}
.skeleton div:nth-child(3){width:70%}
.ai-panel{display:flex;align-items:center;gap:12px;background:linear-gradient(110deg,#fff,var(--canvas));border:1px solid var(--line-200);border-radius:var(--r-md);padding:14px 16px;margin-top:18px}
.ai-mark{width:36px;height:36px;flex:0 0 36px;border-radius:10px;background:var(--info-100);color:var(--navy-800);display:grid;place-items:center;font-size:12px;font-weight:900;font-family:var(--f-en)}
.ai-panel h3{font-size:14px;font-weight:900;color:var(--navy-800);margin-bottom:2px}
.ai-panel p{font-size:12px;color:var(--ink-500);line-height:1.4}
.ai-panel a{color:var(--navy-800);font-weight:800;font-size:12.5px;flex-shrink:0}
.ai-prompt{display:flex;gap:6px;flex-wrap:wrap;margin-top:6px}
.ai-prompt button{border:1px solid var(--line-200);background:#fff;color:var(--navy-800);border-radius:var(--r-pill);padding:4px 8px;font-size:11px;font-weight:700}
.adslot{margin:10px 0;padding:12px;background:#fafbfc;border:1px dashed var(--line-300);border-radius:10px;text-align:center;font-size:11.5px;color:var(--ink-300);min-height:56px}
.adslot:empty{display:none}
.bottom-sheet{position:fixed;inset:0;z-index:100;display:flex;align-items:flex-end;justify-content:center}
.bottom-sheet[hidden]{display:none}
.bottom-sheet__overlay{position:absolute;inset:0;background:rgba(0,32,64,.42)}
.bottom-sheet__panel{position:relative;width:100%;max-width:520px;background:#fff;border-radius:20px 20px 0 0;padding:0 0 env(safe-area-inset-bottom,0);box-shadow:0 -10px 32px rgba(0,0,0,.18)}
.bottom-sheet__header{display:flex;justify-content:space-between;align-items:center;padding:16px 20px;border-bottom:1px solid var(--line-100)}
.bottom-sheet__header h3{font-size:15px;color:var(--navy-800);font-weight:900}
.bottom-sheet__close{width:34px;height:34px;border-radius:50%;background:var(--canvas-2);color:var(--ink-500);font-size:20px;line-height:1}
.bottom-sheet__body{padding:20px}
.field-label{display:block;font-size:11px;text-transform:uppercase;letter-spacing:.05em;font-weight:800;color:var(--ink-500);margin-bottom:8px;font-family:var(--f-en)}
.field-select{width:100%;padding:12px 14px;border:1px solid var(--line-300);border-radius:12px;font-size:14px;background:#fff}
.bottom-sheet__footer{display:flex;gap:10px;padding:16px 20px 24px;border-top:1px solid var(--line-100)}
.btn-secondary,.btn-primary{flex:1;height:46px;border-radius:12px;font-size:14px;font-weight:800;border:1px solid transparent}
.btn-secondary{background:#fff;color:var(--navy-800);border-color:var(--line-300)}
.btn-primary{background:var(--navy-800);color:#fff}
.btn-primary:hover{background:var(--navy-900)}
.btn{padding:10px 20px;border-radius:10px;font-size:14px;font-weight:800;border:none;background:var(--navy-800);color:#fff;display:inline-block}
@media (max-width:420px){body{padding:0 0 86px}.topbar{padding:18px 18px 20px}.topbar .t{font-size:21px}.wrap{padding:18px 14px 24px}.lst{padding:14px}.lst h4{font-size:15.5px}.budget{font-size:19px}}
</style>
@include('partials.felagi-polish')
</head>
<body>

<header class="topbar" role="banner">
  <div class="tr">
    <div class="br">
      <svg width="26" height="26" viewBox="0 0 32 32" fill="none" aria-hidden="true"><circle cx="16" cy="16" r="15" fill="url(#felagiGradB)"/><path d="M9 10 L9 22 M9 12 L18 12 M9 17 L16 17 M18 12 L18 22" stroke="#ffffff" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"/><circle cx="23" cy="10" r="3" fill="#FF9933"/><path d="M20 20 L25 25" stroke="#ffffff" stroke-width="2.2" stroke-linecap="round" opacity="0.6"/><defs><linearGradient id="felagiGradB" x1="0" y1="0" x2="32" y2="32"><stop offset="0" stop-color="#00264d"/><stop offset="1" stop-color="#0b4d83"/></linearGradient></defs></svg>
      <span>{{ __('brand') }}</span>
    </div>
    <div class="ha">
      <div class="lang">
        <a href="/lang/am" class="{{ app()->getLocale()==='am'?'is-active':'' }}">አማ</a>
        <a href="/lang/en" class="{{ app()->getLocale()==='en'?'is-active':'' }}">EN</a>
      </div>
      <a href="/notifications" class="ib" aria-label="{{ __('notifications') }}">
        <span class="dot" aria-hidden="true"></span>
        <svg viewBox="0 0 24 24"><path d="M18 8a6 6 0 0 0-12 0c0 7-3 9-3 9h18s-3-2-3-9M13.7 21a2 2 0 0 1-3.4 0"/></svg>
      </a>
    </div>
  </div>
  <div class="t" id="page-title" tabindex="-1">{{ __('screenS004') }}</div>
  <div class="st">{{ app()->getLocale()==='am' ? 'ተገቢ ፍላጎቶችን ያግኙ' : 'Find relevant needs' }}</div>
</header>

<main class="wrap" role="main" aria-labelledby="page-title">

  <section class="search-primary" aria-labelledby="search-title">
    <h2 id="search-title" class="sr-only">{{ __('search') }}</h2>
    <div class="search-bar" id="search-bar" role="search">
      <span class="search-icon" aria-hidden="true"><svg viewBox="0 0 24 24"><circle cx="11" cy="11" r="7"/><path d="m20 20-3.5-3.5"/></svg></span>
      <input type="search" id="keyword" placeholder="{{ __('searchPlaceholder') }}" maxlength="255" autocomplete="off" aria-label="{{ __('search') }}">
      <button type="button" class="search-clear" id="search-clear" aria-label="{{ app()->getLocale()==='am' ? 'አጽዳ' : 'Clear' }}">×</button>
    </div>
    <div class="search-tools" aria-label="{{ app()->getLocale()==='am' ? 'ፈጣን ፍለጋ' : 'Quick search' }}">
      <button type="button" class="search-tool" data-quick="{{ app()->getLocale()==='am' ? 'ትራንስፖርት' : 'Transport' }}">\u2315 {{ app()->getLocale()==='am' ? 'ትራንስፖርት' : 'Transport' }}</button>
      <button type="button" class="search-tool" data-quick="{{ app()->getLocale()==='am' ? 'ግንባታ' : 'Construction' }}">\u2315 {{ app()->getLocale()==='am' ? 'ግንባታ' : 'Construction' }}</button>
      <button type="button" class="search-tool" data-quick="{{ app()->getLocale()==='am' ? 'አይቲ' : 'IT' }}">\u2315 {{ app()->getLocale()==='am' ? 'አይቲ' : 'IT' }}</button>
    </div>
    <div id="search-history" class="search-history" aria-live="polite"></div>
  </section>

  <section class="categories-section" aria-labelledby="categories-title">
    <div class="section-head">
      <h2 id="categories-title">{{ app()->getLocale()==='am' ? 'ተወዳጅ ምድቦች' : 'Popular Categories' }}</h2>
      <button type="button" class="section-more" id="more-categories" aria-expanded="false">{{ app()->getLocale()==='am' ? 'ሁሉም' : 'More' }} →</button>
    </div>
    <div class="chips" id="chips" data-limited="true"></div>
  </section>

  <section class="filters-section" aria-labelledby="filters-title">
    <div class="section-head">
      <h2 id="filters-title">{{ app()->getLocale()==='am' ? 'ፈጣን ማጣሪያዎች' : 'Quick Filters' }}</h2>
      <button type="button" class="section-more" id="more-filters" aria-expanded="false">{{ app()->getLocale()==='am' ? 'ተጨማሪ' : 'More' }}</button>
    </div>
    <div class="filter-chips" id="filter-chips" role="group" aria-label="{{ app()->getLocale()==='am' ? 'ማጣሪያዎች' : 'Filters' }}">
      <button type="button" class="filter-chip active" data-filter="all" aria-pressed="true">{{ app()->getLocale()==='am' ? 'ሁሉም' : 'All' }}</button>
      <button type="button" class="filter-chip" data-filter="verified" aria-pressed="false">{{ app()->getLocale()==='am' ? 'የተረጋገጡ' : 'Verified' }}</button>
      <button type="button" class="filter-chip" data-filter="urgent" aria-pressed="false">{{ app()->getLocale()==='am' ? 'አስቸኳይ' : 'Urgent' }}</button>
      <button type="button" class="filter-chip" data-filter="newest" aria-pressed="false">{{ app()->getLocale()==='am' ? 'አዲስ' : 'Newest' }}</button>
      <button type="button" class="filter-chip" data-filter="budget_high" aria-pressed="false">{{ app()->getLocale()==='am' ? 'ከፍተኛ በጀት' : 'High Budget' }}</button>
    </div>
  </section>

  <section class="needs-section" aria-labelledby="needs-title">
    <div class="section-head">
      <h2 id="needs-title">{{ app()->getLocale()==='am' ? 'የገበያ ፍላጎቶች' : 'Marketplace Needs' }}</h2>
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
  </section>

  <section class="ai-panel" aria-labelledby="ai-help-title">
    <span class="ai-mark" aria-hidden="true">AI</span>
    <div style="flex:1;min-width:0">
      <h3 id="ai-help-title">{{ app()->getLocale()==='am' ? 'Felagi AI ይረዳዎታል' : 'Felagi AI can help' }}</h3>
      <p>{{ app()->getLocale()==='am' ? 'ፍላጎትዎን በተፈጥሯዊ ቋንቋ ይጻፉ።' : 'Describe your need naturally.' }}</p>
      <div class="ai-prompt"><button type="button" data-prompt="{{ app()->getLocale()==='am' ? 'የጭነት መኪና እፈልጋለሁ' : 'I need a delivery truck' }}">{{ app()->getLocale()==='am' ? 'ምሳሌ ይሞክሩ' : 'Try an example' }}</button></div>
    </div>
    <a href="/needs/new">{{ app()->getLocale()==='am' ? 'ይጀምሩ' : 'Start' }} →</a>
  </section>

  <div class="sr-only" aria-hidden="true">
    <select id="sort" tabindex="-1">
      <option value="newest">{{ __('sortNewest') }}</option>
      <option value="budget_low">{{ __('sortBudgetLow') }}</option>
      <option value="budget_high">{{ __('sortBudgetHigh') }}</option>
      <option value="deadline_soon">{{ __('sortDeadlineSoon') }}</option>
    </select>
    <button type="button" id="clear" tabindex="-1">{{ __('clearFilters') }}</button>
  </div>

</main>

<div class="bottom-sheet" id="advanced-filters" hidden aria-hidden="true">
  <div class="bottom-sheet__overlay" data-sheet-close></div>
  <div class="bottom-sheet__panel" role="dialog" aria-modal="true" aria-labelledby="sheet-title">
    <div class="bottom-sheet__header">
      <h3 id="sheet-title">{{ app()->getLocale()==='am' ? 'ተጨማሪ ማጣሪያዎች' : 'Advanced Filters' }}</h3>
      <button type="button" class="bottom-sheet__close" data-sheet-close aria-label="{{ app()->getLocale()==='am' ? 'ዝጋ' : 'Close' }}">×</button>
    </div>
    <div class="bottom-sheet__body">
      <label class="field-label" for="sort-visible">{{ __('sortBy') }}</label>
      <select id="sort-visible" data-sync="sort" class="field-select">
        <option value="newest">{{ __('sortNewest') }}</option>
        <option value="budget_low">{{ __('sortBudgetLow') }}</option>
        <option value="budget_high">{{ __('sortBudgetHigh') }}</option>
        <option value="deadline_soon">{{ __('sortDeadlineSoon') }}</option>
      </select>
    </div>
    <div class="bottom-sheet__footer">
      <button type="button" id="clear-filters-visible" class="btn-secondary">{{ __('clearFilters') }}</button>
      <button type="button" class="btn-primary" data-sheet-close>{{ app()->getLocale()==='am' ? 'ተግብር' : 'Apply' }}</button>
    </div>
  </div>
</div>

@include('partials.bottom-nav')

<script>
(function(){
'use strict';
var S={keyword:'',cat:null,sort:'newest',filter:null,items:[]};
function $(id){return document.getElementById(id);}
function esc(s){return String(s==null?'':s).replace(/[&<>"']/g,function(c){return {'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c];});}
var AM = document.documentElement.lang === 'am';
var ALL_CATEGORIES_LABEL = '{{ __("allCategories") }}';

function felagiLocalizedName(obj){
  if (!obj) return '';
  return AM ? (obj.name_am || obj.name_en || obj.slug || '') : (obj.name_en || obj.name_am || obj.slug || '');
}

function loadCats(){
  fetch('/api/v1/categories',{credentials:'same-origin',headers:{'Accept':'application/json'}})
    .then(function(r){return r.ok?r.json():{data:[]};})
    .then(function(j){
      var c=j.data||[];
      $('chips').innerHTML='<button class="chip active" data-id="">'+ALL_CATEGORIES_LABEL+'</button>'+c.map(function(x){return '<button class="chip" data-id="'+esc(x.id)+'">'+esc(felagiLocalizedName(x))+'</button>';}).join('');
      var btns=$('chips').querySelectorAll('.chip');
      Array.prototype.forEach.call(btns,function(b){
        b.onclick=function(){S.cat=b.dataset.id||null;Array.prototype.forEach.call(btns,function(x){x.classList.toggle('active',x===b);});load();};
      });
    })
    .catch(function(e){console.warn('[S004] cats',e);});
}

function load(){
  var q=new URLSearchParams();
  if(S.keyword)q.set('keyword',S.keyword);
  if(S.cat)q.set('category_id',S.cat);
  if(S.sort)q.set('sort',S.sort);
  if(S.filter&&S.filter!=='all')q.set('filter',S.filter);
  fetch('/api/v1/needs?'+q.toString(),{credentials:'same-origin',headers:{'Accept':'application/json'}})
    .then(function(r){if(r.status===429)throw new Error('rate-limited');if(!r.ok)throw new Error('HTTP '+r.status);return r.json();})
    .then(function(j){
      var items=j.data||[];
      $('error').hidden=true;
      if(!items.length){$('feed').innerHTML='';$('empty').hidden=false;return;}
      $('empty').hidden=true;
      $('feed').innerHTML=items.map(function(it){
        var cur=it.currency||'ETB';
        var bud=it.budget_min!=null?cur+' '+it.budget_min:(it.budget_max!=null?cur+' '+it.budget_max:'');
        var category=it.category?(it.category.name_am||it.category.name_en||it.category.slug||''):'';
        var location=it.location_text||'';
        var deadline=it.offer_deadline_at||it.deadline_at||'';
        var posted=it.created_at?new Date(it.created_at).toLocaleDateString(AM?'am-ET':'en-US',{month:'short',day:'numeric'}):'';
        var offers=it.offers_count!=null?String(it.offers_count):'';
        var tags='';
        if(it.verified||it.requester_verified)tags+='<span class="bg ok">\u2713 '+(AM?'የተረጋገጠ':'Verified')+'</span>';
        if(it.urgent)tags+='<span class="bg warn">'+(AM?'አስቸኳይ':'Urgent')+'</span>';
        var key='felagi-bookmark-'+it.id, saved=localStorage.getItem(key)==='1';
        var match=it.ai_match_score!=null?String(it.ai_match_score)+'% '+(AM?'ተመሳሳይነት':'match'):'';
        return '<a class="lst" href="/needs/'+esc(it.id)+'"><div class="cat-strip"><span class="bg info">'+esc(category||(AM?'ፍላጎት':'Need'))+'</span><span class="bg ok">'+(AM?'ንቁ':'Active')+'</span>'+tags+'</div><h4>'+esc(it.title)+'</h4><div class="budget">'+esc(bud)+'</div><div class="meta">'+(location?'<span>\u2316 '+esc(location)+'</span>':'')+(posted?'<span>\u25F7 '+esc(posted)+'</span>':'')+(offers?'<span>\u2197 '+esc(offers)+' '+(AM?'አቅርቦቶች':'offers')+'</span>':'')+'</div>'+(deadline?'<div class="meta"><span>\u231B '+esc(deadline)+'</span></div>':'')+'<p class="desc">'+esc((it.description||'').slice(0,140))+'</p><div class="foot"><span class="match">'+esc(match)+'</span><span class="cta">'+(AM?'ዝርዝሩን ይመልከቱ':'View need')+' \u2192</span></div></a>';
      }).join('');
    })
    .catch(function(e){console.warn('[S004] needs',e);$('feed').innerHTML='';$('error').hidden=false;});
}

var tm=null;
function renderHistory(){
  var h=$('search-history'),items=[];
  try{items=JSON.parse(localStorage.getItem('felagi-search-history')||'[]');}catch(e){}
  h.innerHTML=items.slice(0,4).map(function(x){return '<button type="button" data-history="'+esc(x)+'">\u25F7 '+esc(x)+'</button>';}).join('');
  h.classList.toggle('show',items.length>0);
  Array.prototype.forEach.call(h.querySelectorAll('[data-history]'),function(b){b.onclick=function(){$('keyword').value=b.dataset.history;S.keyword=b.dataset.history;load();};});
}
function rememberSearch(v){
  if(!v)return;
  var a=[];
  try{a=JSON.parse(localStorage.getItem('felagi-search-history')||'[]');}catch(e){}
  a=[v].concat(a.filter(function(x){return x!==v;})).slice(0,4);
  localStorage.setItem('felagi-search-history',JSON.stringify(a));
  renderHistory();
}

$('keyword').addEventListener('input',function(e){
  clearTimeout(tm);
  tm=setTimeout(function(){S.keyword=e.target.value.trim().slice(0,255);rememberSearch(S.keyword);load();},300);
});
$('sort').addEventListener('change',function(e){S.sort=e.target.value;load();});
$('clear').addEventListener('click',function(){
  S.keyword='';S.cat=null;S.sort='newest';S.filter=null;
  $('keyword').value='';$('sort').value='newest';
  Array.prototype.forEach.call($('chips').querySelectorAll('.chip'),function(x,i){x.classList.toggle('active',i===0);});
  load();
});
Array.prototype.forEach.call(document.querySelectorAll('[data-prompt]'),function(b){b.onclick=function(){window.location.href='/needs/new?prompt='+encodeURIComponent(b.dataset.prompt);};});
Array.prototype.forEach.call(document.querySelectorAll('[data-quick]'),function(b){b.onclick=function(){$('keyword').value=b.dataset.quick;S.keyword=b.dataset.quick;rememberSearch(S.keyword);load();$('keyword').focus();};});

(function(){
  var inp=$('keyword');var clearBtn=$('search-clear');
  function update(){clearBtn.classList.toggle('is-visible',!!inp.value.trim());}
  inp.addEventListener('input',update);
  clearBtn.addEventListener('click',function(){inp.value='';S.keyword='';update();load();inp.focus();});
  update();
})();

(function(){
  var chipsEl=$('chips');var btn=$('more-categories');
  if(!btn||!chipsEl)return;
  btn.addEventListener('click',function(){
    var limited=chipsEl.getAttribute('data-limited')==='true';
    chipsEl.setAttribute('data-limited',limited?'false':'true');
    btn.setAttribute('aria-expanded',limited?'true':'false');
    btn.textContent=limited?'← '+(AM?'ያንስ':'Less'):(AM?'ሁሉም':'More')+' →';
  });
})();

(function(){
  var chips=document.querySelectorAll('.filter-chip');
  Array.prototype.forEach.call(chips,function(chip){
    chip.addEventListener('click',function(){
      var filter=chip.getAttribute('data-filter');
      if(filter==='all'){
        Array.prototype.forEach.call(chips,function(c){c.classList.remove('active');c.setAttribute('aria-pressed','false');});
        chip.classList.add('active');chip.setAttribute('aria-pressed','true');
        S.filter=null;
      }else{
        document.querySelector('.filter-chip[data-filter="all"]').classList.remove('active');
        document.querySelector('.filter-chip[data-filter="all"]').setAttribute('aria-pressed','false');
        var wasActive=chip.classList.contains('active');
        chip.classList.toggle('active');
        chip.setAttribute('aria-pressed',wasActive?'false':'true');
        S.filter=wasActive?null:filter;
      }
      load();
    });
  });
})();

(function(){
  var sheet=$('advanced-filters');var openBtn=$('more-filters');
  if(!sheet||!openBtn)return;
  function open(){sheet.hidden=false;sheet.setAttribute('aria-hidden','false');document.body.style.overflow='hidden';}
  function close(){sheet.hidden=true;sheet.setAttribute('aria-hidden','true');document.body.style.overflow='';}
  openBtn.addEventListener('click',open);
  Array.prototype.forEach.call(sheet.querySelectorAll('[data-sheet-close]'),function(el){el.addEventListener('click',close);});
  document.addEventListener('keydown',function(e){if(e.key==='Escape'&&!sheet.hidden)close();});
  var sortVisible=$('sort-visible');
  if(sortVisible){
    sortVisible.value=S.sort||'newest';
    sortVisible.addEventListener('change',function(){S.sort=sortVisible.value;$('sort').value=sortVisible.value;load();});
  }
  var clearVisible=$('clear-filters-visible');
  if(clearVisible){
    clearVisible.addEventListener('click',function(){
      Array.prototype.forEach.call(document.querySelectorAll('.filter-chip'),function(c){c.classList.remove('active');c.setAttribute('aria-pressed','false');});
      document.querySelector('.filter-chip[data-filter="all"]').classList.add('active');
      document.querySelector('.filter-chip[data-filter="all"]').setAttribute('aria-pressed','true');
      S.filter=null;S.cat=null;S.keyword='';
      $('keyword').value='';
      $('search-clear').classList.remove('is-visible');
      Array.prototype.forEach.call($('chips').querySelectorAll('.chip'),function(c,i){c.classList.toggle('active',i===0);});
      load();
    });
  }
})();

renderHistory();
loadCats();
load();
})();
</script>
</body>
</html>
