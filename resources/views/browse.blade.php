<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>{{ __('screenS004') }} — {{ __('brand') }}</title>
<meta name="theme-color" content="#003366">
<style>
:root{--navy:#003366;--orange:#FF9933;--ink:#192431;--muted:#586675;--line:#dfe7ee;--surface:#fff;--soft:#f4f8fc;--radius-lg:22px;--radius-md:16px;--shadow-1:0 4px 14px rgba(0,51,102,.08);--shadow-2:0 12px 30px rgba(0,51,102,.14)}
*{box-sizing:border-box}
body{font-family:"Noto Sans Ethiopic",system-ui,-apple-system,"Segoe UI",sans-serif;background:#F4F6F8;color:var(--ink);margin:0;padding-bottom:78px;font-size:16px;line-height:1.6;-webkit-font-smoothing:antialiased;text-rendering:optimizeLegibility}
header{background:#003366;color:#fff;padding:14px 20px;display:flex;justify-content:space-between;align-items:center;position:sticky;top:0;z-index:30}
header .brand{display:flex;align-items:center;gap:10px;font-size:20px;font-weight:700}
header .brand img{width:112px;height:auto;display:block;max-height:30px}
header .brand img{width:96px;height:auto;display:block;filter:brightness(0) invert(1)}
header a{color:#fff;text-decoration:none;font-size:14px}
main{padding:20px 18px 128px;max-width:1200px;margin:0 auto}
.hero{background:radial-gradient(circle at 88% 18%,rgba(255,153,51,.34),transparent 30%),radial-gradient(circle at 12% 105%,rgba(64,170,220,.28),transparent 34%),linear-gradient(135deg,#003366 0%,#0b4d83 100%);color:#fff;border-radius:var(--radius-lg);padding:34px 26px;margin-bottom:34px;box-shadow:0 16px 34px rgba(0,51,102,.22)}
.hero{position:relative;overflow:hidden;isolation:isolate}
.hero:after{content:'✦';position:absolute;right:24px;top:18px;color:rgba(255,255,255,.16);font-size:92px;line-height:1;transform:rotate(18deg);z-index:-1}
.hero:before{content:'';position:absolute;right:-42px;bottom:-58px;width:190px;height:190px;border:1px solid rgba(255,255,255,.16);border-radius:50%;box-shadow:0 0 0 18px rgba(255,255,255,.04),0 0 0 36px rgba(255,255,255,.03);z-index:-1}
.hero .trust-row{display:flex;flex-wrap:wrap;gap:8px;margin-top:22px}.hero .trust-row span{display:inline-flex;align-items:center;gap:5px;border:1px solid rgba(255,255,255,.22);background:rgba(255,255,255,.09);border-radius:999px;padding:5px 8px;color:rgba(255,255,255,.88);font-size:11px;font-weight:600;backdrop-filter:blur(6px)}
.hero-visual{position:absolute;right:28px;bottom:40px;width:150px;height:118px;opacity:.86;pointer-events:none}.hero-visual i{position:absolute;display:block;border:1px solid rgba(255,255,255,.28);border-radius:50%}.hero-visual i:nth-child(1){width:118px;height:118px;right:0;bottom:0}.hero-visual i:nth-child(2){width:76px;height:76px;right:21px;bottom:21px;background:rgba(255,153,51,.22);border-color:rgba(255,153,51,.42)}.hero-visual i:nth-child(3){width:22px;height:22px;right:48px;bottom:48px;background:#FF9933;border:0;box-shadow:0 0 0 9px rgba(255,153,51,.18)}
.hero .eyebrow{font-size:12px;opacity:.78;margin-bottom:8px;letter-spacing:.02em;font-weight:600;text-transform:uppercase}
.hero h2{font-size:clamp(28px,5.5vw,38px);line-height:1.15;margin:0 0 16px;letter-spacing:-.03em;font-weight:800}
.hero p{max-width:520px;margin:0 0 24px;color:rgba(255,255,255,.78);font-size:15px;line-height:1.55;font-weight:400}
.hero-actions{display:flex;flex-wrap:wrap;gap:10px;align-items:center}
.hero .primary-cta{display:inline-flex;align-items:center;justify-content:center;min-height:48px;padding:0 24px;border-radius:12px;background:#FF9933;color:#003366;font-weight:800;font-size:15px;text-decoration:none;box-shadow:0 6px 14px rgba(255,153,51,.28);transition:transform .15s,box-shadow .15s}.hero .primary-cta:hover{transform:translateY(-1px);box-shadow:0 8px 18px rgba(255,153,51,.36)}
.hero .secondary-link{color:rgba(255,255,255,.65);font-size:13px;text-decoration:none;border-bottom:1px solid rgba(255,255,255,.25);padding-bottom:1px;transition:color .15s,border-color .15s}.hero .secondary-link:hover{color:rgba(255,255,255,.9);border-color:rgba(255,255,255,.5)}
.section-heading{display:flex;justify-content:space-between;align-items:end;gap:12px;margin:32px 0 12px}
.section-heading h2{font-size:22px;line-height:1.25;color:#003366;margin:0;font-weight:750}
.section-heading p{font-size:14px;color:#586675;margin:5px 0 0}
.ai-panel{display:flex;align-items:center;justify-content:space-between;gap:16px;background:linear-gradient(110deg,#fff,#f4f8fc);border:1px solid #cbdceb;border-radius:var(--radius-md);padding:22px;margin-bottom:30px;box-shadow:var(--shadow-1)}
.ai-panel .ai-mark{width:42px;height:42px;flex:0 0 42px;border-radius:12px;background:#eaf2fb;color:#003366;display:grid;place-items:center;font-weight:800}
.ai-panel h3{font-size:18px;margin:0 0 5px;color:#003366;font-weight:750}
.ai-panel p{font-size:14px;color:#586675;margin:0;line-height:1.55}
.ai-panel a{flex:0 0 auto;color:#003366;font-weight:700;font-size:13px;text-decoration:none}
.ai-panel .ai-prompt{display:flex;gap:6px;flex-wrap:wrap;margin-top:9px}.ai-panel .ai-prompt button{border:1px solid #cbdceb;background:#fff;color:#003366;border-radius:999px;padding:6px 10px;font:inherit;font-size:12px;cursor:pointer}.ai-panel .ai-prompt button:active{transform:scale(.97)}
#keyword{width:100%;height:54px;padding:0 16px;border:1px solid #c6d1dc;border-radius:14px;font-size:16px;box-sizing:border-box;background:#fff;box-shadow:0 3px 10px rgba(0,51,102,.05)}
#keyword:focus{outline:none;border-color:#003366;box-shadow:0 0 0 3px rgba(0,51,102,.1)}
.search-tools{display:flex;gap:8px;flex-wrap:wrap;margin:8px 0 2px}.search-tool{border:1px solid #d9e1e8;background:#fff;color:#003366;border-radius:999px;padding:7px 10px;font:inherit;font-size:12px;cursor:pointer}.search-tool:hover{background:#eaf2fb}.search-history{display:none;gap:7px;flex-wrap:wrap;margin:8px 0 4px}.search-history.show{display:flex}.search-history button{border:0;background:#eef4f8;color:#586675;border-radius:8px;padding:6px 9px;font:inherit;font-size:12px;cursor:pointer}
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
.card{position:relative;background:linear-gradient(180deg,#fff 0%,#fcfdff 100%);padding:22px;border-radius:20px;text-decoration:none;color:inherit;box-shadow:0 8px 24px rgba(0,51,102,.09),0 2px 4px rgba(0,0,0,.04);border:1px solid #e4edf4;border-left:4px solid #ff9933;display:flex;flex-direction:column;gap:14px;transition:transform .2s ease,box-shadow .2s ease,border-color .2s ease;min-height:300px;overflow:hidden}
.card:before{content:'';position:absolute;left:0;top:0;bottom:0;width:4px;background:linear-gradient(180deg,#FF9933,#003366);opacity:.8}.card:hover{border-color:#b9d0e2;box-shadow:0 16px 32px rgba(0,51,102,.15),0 3px 8px rgba(0,0,0,.05);transform:translateY(-3px)}
.card:hover{box-shadow:0 8px 22px rgba(0,51,102,.13);transform:translateY(-1px)}
.card-top,.card-meta,.card-tags{display:flex;align-items:center;gap:7px;flex-wrap:wrap}
.card-top{justify-content:space-between}
.card-badge,.card-tag{font-size:12px;border-radius:999px;padding:7px 11px;background:#e7f0f8;color:#003366;font-weight:800;letter-spacing:.01em}.card-tag.status{background:#edf8f0;color:#176b36}
.card-tag.urgent{background:#fff0e6;color:#a84c00}.card-tag.verified{background:#eaf7ef;color:#176b36}
.card-meta{font-size:13px;color:#586675;line-height:1.45}.card-meta span:before{content:'•';margin-right:7px;color:#aab6c2}.card-meta span:first-child:before{content:'';margin:0}
.card-actions{display:flex;justify-content:flex-end;gap:8px;color:#586675;font-size:12px}
.card h3{margin:0;font-size:21px;line-height:1.38;font-weight:850;color:#122a40;letter-spacing:-.015em}.card p{margin:0;color:#536879;font-size:15.5px;line-height:1.7;display:-webkit-box;-webkit-line-clamp:3;-webkit-box-orient:vertical;overflow:hidden}.card .budget{display:flex;flex-direction:column;gap:3px;color:#176b36;font-weight:900;font-size:23px;line-height:1.2;margin-top:2px;padding:11px 13px;background:#f1faf3;border:1px solid #d8efdd;border-radius:12px}.card .budget:before{content:'{{ app()->getLocale()==='am' ? 'በጀት' : 'Budget' }}';color:#547064;font-size:12px;letter-spacing:.08em;text-transform:uppercase;font-weight:850}.card .card-meta + .budget{margin-top:2px}
.card-actions{justify-content:space-between;align-items:center;border-top:1px solid #e8eef3;padding-top:14px;margin-top:auto}.card-actions .action-set{display:flex;gap:8px}.card-actions button{border:1px solid #cbdce8;background:#fff;color:#536879;border-radius:50%;width:40px;height:40px;font:inherit;cursor:pointer;box-shadow:0 3px 9px rgba(0,51,102,.08);transition:background .16s ease,transform .16s ease,box-shadow .16s ease}.card-actions button:hover,.card-actions button.active{background:#eaf2fb;color:#003366;border-color:#9fbfd5;transform:translateY(-2px);box-shadow:0 6px 14px rgba(0,51,102,.12)}.card-actions .match{color:#176b36;background:#edf8f0;border-radius:999px;padding:7px 10px;font-size:12px;font-weight:850}.card-cta{color:#003366;background:#eef5fa;border-radius:10px;padding:9px 11px;font-size:13px;font-weight:850;white-space:nowrap}
.adslot{margin:16px 0;padding:12px;background:#fafbfc;border:1px dashed #d0d7de;border-radius:8px;text-align:center;font-size:12px;color:#586675;min-height:60px}
.adslot:empty{display:none}
main{padding-bottom:calc(112px + env(safe-area-inset-bottom))}
@media(min-width:600px){
body{padding-left:88px;padding-bottom:16px}
header{margin-left:-88px;padding-left:calc(88px + 16px)}
}
@media(min-width:1200px){
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
@media(prefers-reduced-motion:reduce){*,*:before,*:after{animation-duration:.01ms!important;animation-iteration-count:1!important;scroll-behavior:auto!important;transition-duration:.01ms!important}}
#page-title:focus{outline:none}
.sr-only{position:absolute;width:1px;height:1px;padding:0;margin:-1px;overflow:hidden;clip:rect(0,0,0,0);white-space:nowrap;border:0}
</style>

@include('partials.felagi-polish')

<style>


/* ═══════════════════════════════════════════════════════════
   BROWSE PAGE — IA RESTRUCTURE (L350)
   ═══════════════════════════════════════════════════════════ */

/* ─── SEARCH (PRIMARY) ─────────────────────────────────── */
.search-primary{margin:0 0 24px}
.search-bar{position:relative;display:flex;align-items:center;background:#fff;border:2px solid #d9e1e8;border-radius:16px;box-shadow:0 8px 24px rgba(0,51,102,.08);transition:border-color .15s,box-shadow .15s}
.search-bar:focus-within{border-color:#003366;box-shadow:0 10px 28px rgba(0,51,102,.14)}
.search-icon{flex:0 0 24px;width:24px;height:24px;margin:0 0 0 20px;color:#586675;display:inline-flex;align-items:center;justify-content:center}
.search-icon svg{width:100%;height:100%}
.search-bar input{flex:1;min-width:0;height:56px;padding:0 12px;border:0;background:transparent;font:inherit;font-size:16px;color:#132238;outline:none}
.search-bar input::placeholder{color:#8fa0b3}
.search-clear{display:none;flex:0 0 36px;width:36px;height:36px;margin-right:12px;border:0;background:#eef4f8;color:#586675;border-radius:50%;font-size:18px;line-height:1;cursor:pointer;align-items:center;justify-content:center}
.search-clear.is-visible{display:inline-flex}

/* ─── SECTION HEADINGS ─────────────────────────────────── */
.section-heading{display:flex;justify-content:space-between;align-items:center;gap:12px;margin:28px 0 12px}
.section-heading h2{font-size:18px;line-height:1.3;color:#003366;margin:0;font-weight:750;letter-spacing:-.01em}
.section-heading p{font-size:14px;color:#586675;margin:4px 0 0}
.section-more{flex:0 0 auto;background:transparent;border:0;color:#003366;font-size:13px;font-weight:700;cursor:pointer;padding:4px 8px;border-radius:8px;transition:background .15s}
.section-more:hover{background:#eef4f8}

/* ─── CATEGORIES ───────────────────────────────────────── */
.categories-section .chips{display:flex;gap:8px;flex-wrap:wrap;padding:0;overflow:visible}
.categories-section .chip{flex:0 0 auto;padding:9px 14px;border:1px solid #d9e1e8;background:#fff;color:#132238;border-radius:12px;font:inherit;font-size:13px;font-weight:600;cursor:pointer;min-height:40px;transition:background .15s,border-color .15s,color .15s;box-shadow:0 1px 3px rgba(0,51,102,.04)}
.categories-section .chip:hover{border-color:#a8c2d9;background:#f6fafd}
.categories-section .chip.active{background:#003366;color:#fff;border-color:#003366;font-weight:700;box-shadow:0 3px 8px rgba(0,51,102,.18)}
.categories-section .chips[data-limited="true"] .chip:nth-child(n+6){display:none}
.categories-section .chips:not([data-limited="true"]) .chip{display:inline-block}

/* ─── QUICK FILTERS ────────────────────────────────────── */
.filters-section .filter-chips{display:flex;gap:8px;flex-wrap:wrap;padding:0}
.filter-chip{padding:8px 14px;border:1px solid #d9e1e8;background:#fff;color:#586675;border-radius:999px;font:inherit;font-size:13px;font-weight:600;cursor:pointer;min-height:36px;transition:all .15s}
.filter-chip:hover{border-color:#a8c2d9;color:#003366}
.filter-chip.active{background:#eaf2fb;color:#003366;border-color:#003366;font-weight:700}
.filter-chip.active::before{content:'✓ ';font-weight:800}

/* ─── AI PANEL (compact) ───────────────────────────────── */
.ai-panel--compact{padding:16px 18px;gap:12px}
.ai-panel--compact .ai-mark{width:36px;height:36px;flex:0 0 36px;font-size:13px;border-radius:10px}
.ai-panel--compact h3{font-size:15px;margin:0 0 3px}
.ai-panel--compact p{font-size:13px;margin:0}
.ai-panel--compact a{font-size:13px}
.ai-panel--compact .ai-prompt{margin-top:6px}

/* ─── BOTTOM SHEET ─────────────────────────────────────── */
.bottom-sheet{position:fixed;inset:0;z-index:100;display:flex;align-items:flex-end;justify-content:center}
.bottom-sheet[hidden]{display:none}
.bottom-sheet__overlay{position:absolute;inset:0;background:rgba(0,32,64,.42);animation:sheetFade .2s ease-out}
.bottom-sheet__panel{position:relative;width:100%;max-width:520px;background:#fff;border-radius:20px 20px 0 0;box-shadow:0 -10px 32px rgba(0,0,0,.18);animation:sheetUp .25s cubic-bezier(.16,.84,.44,1);padding-bottom:env(safe-area-inset-bottom,0)}
.bottom-sheet__header{display:flex;justify-content:space-between;align-items:center;padding:16px 20px;border-bottom:1px solid #eef2f6}
.bottom-sheet__header h3{font-size:16px;margin:0;color:#003366;font-weight:750}
.bottom-sheet__close{width:36px;height:36px;border:0;background:#f0f4f8;border-radius:50%;font-size:20px;color:#586675;cursor:pointer;line-height:1}
.bottom-sheet__close:hover{background:#e3e9ef}
.bottom-sheet__body{padding:20px}
.field-label{display:block;font-size:12px;text-transform:uppercase;letter-spacing:.04em;font-weight:700;color:#586675;margin-bottom:8px}
.field-select{width:100%;padding:12px 14px;border:1px solid #d0d7de;border-radius:12px;font:inherit;font-size:15px;background:#fff}
.field-select:focus{outline:2px solid #003366;border-color:#003366}
.bottom-sheet__footer{display:flex;gap:10px;padding:16px 20px 24px;border-top:1px solid #eef2f6}
.btn-secondary,.btn-primary{flex:1;height:48px;border-radius:12px;font:inherit;font-size:15px;font-weight:700;cursor:pointer;border:1px solid transparent}
.btn-secondary{background:#fff;color:#003366;border-color:#d0d7de}
.btn-secondary:hover{background:#f6fafd}
.btn-primary{background:#003366;color:#fff}
.btn-primary:hover{background:#00264d}

@keyframes sheetFade{from{opacity:0}to{opacity:1}}
@keyframes sheetUp{from{transform:translateY(100%)}to{transform:translateY(0)}}

@media (prefers-reduced-motion:reduce){
  .bottom-sheet__overlay,.bottom-sheet__panel{animation:none}
  .search-bar,.chip,.filter-chip,.section-more{transition:none}
}

</style>
</head>
<body>
<header>
<span class="brand"><img src="/assets/brand/felagi-lockup-on-dark.svg" alt="{{ __('brand') }}"></span>
<a href="#" onclick="felagiLogout();return false;">{{ __('logout') }}</a>
<span class="lang-switch" style="margin-left:12px;font-size:13px">
  <a href="/lang/am" style="color:#fff;text-decoration:{{ app()->getLocale()==='am'?'underline':'none' }};margin-right:6px">አማ</a>
  <a href="/lang/en" style="color:#fff;text-decoration:{{ app()->getLocale()==='en'?'underline':'none' }}">EN</a>
</span>
{{-- L350: Notifications top bar --}}
<a href="/notifications" class="felagi-topbar-alerts" aria-label="ማሳወቂያዎች">
<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M6 8a6 6 0 0 1 12 0c0 7 3 9 3 9H3s3-2 3-9"/><path d="M10.3 21a1.94 1.94 0 0 0 3.4 0"/></svg>
</a>
</header>
<main role="main" aria-labelledby="page-title">
<h1 id="page-title" tabindex="-1" class="sr-only">{{ __('screenS004') }}</h1>

{{-- ═══════════════════════════════════════════════════════════
     1. HERO
     ═══════════════════════════════════════════════════════════ --}}
<section class="hero" aria-labelledby="home-hero-title">
<div class="eyebrow">{{ __('brand') }}</div>
<h2 id="home-hero-title">{{ app()->getLocale()==='am' ? 'ዛሬ ምን ይፈልጋሉ?' : 'What do you need today?' }}</h2>
<p>{{ app()->getLocale()==='am' ? 'ፍላጎትዎን ይንገሩን — Felagi ተገቢ ቅናሾችን ያገኝልዎታል።' : 'Tell Felagi what you need — receive relevant offers.' }}</p>
<div class="hero-actions">
<a class="primary-cta" href="/needs/new">＋ {{ __('createNeed') }}</a>
<a class="secondary-link" href="#search-bar">{{ app()->getLocale()==='am' ? 'ፍላጎቶችን ይፈልጉ' : 'Search needs' }}</a>
</div>
<div class="hero-visual" aria-hidden="true"><i></i><i></i><i></i></div>
<div class="trust-row" aria-label="{{ app()->getLocale()==='am' ? 'የFelagi እምነት ምልክቶች' : 'Felagi trust signals' }}"><span>✓ {{ app()->getLocale()==='am' ? 'የተረጋገጡ' : 'Verified' }}</span><span>⚖ {{ app()->getLocale()==='am' ? 'ፍትሃዊ' : 'Fair' }}</span><span>✦ {{ app()->getLocale()==='am' ? 'AI ድጋፍ' : 'AI-assisted' }}</span></div>
</section>

{{-- ═══════════════════════════════════════════════════════════
     2. SEARCH (PRIMARY)
     ═══════════════════════════════════════════════════════════ --}}
<section class="search-primary" aria-labelledby="search-title">
<h2 id="search-title" class="sr-only">{{ app()->getLocale()==='am' ? 'ፍለጋ' : 'Search' }}</h2>
<div class="search-bar" id="search-bar" role="search">
<span class="search-icon" aria-hidden="true">
<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="7"/><path d="m20 20-3.5-3.5"/></svg>
</span>
<input type="search" id="keyword" placeholder="{{ app()->getLocale()==='am' ? 'ምን ይፈልጋሉ? ለምሳሌ፦ የጭነት መኪና፣ ሲሚንቶ…' : 'What do you need? e.g. a truck, cement…' }}" maxlength="255" autocomplete="off" aria-label="{{ app()->getLocale()==='am' ? 'ፍለጋ' : 'Search' }}">
<button type="button" class="search-clear" id="search-clear" aria-label="{{ app()->getLocale()==='am' ? 'አጽዳ' : 'Clear' }}">×</button>
</div>
<div class="search-tools" aria-label="{{ app()->getLocale()==='am' ? 'ፈጣን ፍለጋ' : 'Quick search' }}"><button type="button" class="search-tool" data-quick="{{ app()->getLocale()==='am' ? 'ትራንስፖርት' : 'Transport' }}">⌕ {{ app()->getLocale()==='am' ? 'ትራንስፖርት' : 'Transport' }}</button><button type="button" class="search-tool" data-quick="{{ app()->getLocale()==='am' ? 'ግንባታ' : 'Construction' }}">⌕ {{ app()->getLocale()==='am' ? 'ግንባታ' : 'Construction' }}</button><button type="button" class="search-tool" data-quick="{{ app()->getLocale()==='am' ? 'አይቲ' : 'IT' }}">⌕ {{ app()->getLocale()==='am' ? 'አይቲ' : 'IT' }}</button></div>
<div id="search-history" class="search-history" aria-live="polite"></div>
</section>

{{-- ═══════════════════════════════════════════════════════════
     3. POPULAR CATEGORIES
     ═══════════════════════════════════════════════════════════ --}}
<section class="categories-section" aria-labelledby="categories-title">
<div class="section-heading">
<h2 id="categories-title">{{ app()->getLocale()==='am' ? 'ተወዳጅ ምድቦች' : 'Popular Categories' }}</h2>
<button type="button" class="section-more" id="more-categories" aria-expanded="false">{{ app()->getLocale()==='am' ? 'ሁሉም' : 'More' }} →</button>
</div>
<div class="chips" id="chips" data-limited="true"></div>
</section>

{{-- ═══════════════════════════════════════════════════════════
     4. QUICK FILTERS
     ═══════════════════════════════════════════════════════════ --}}
<section class="filters-section" aria-labelledby="filters-title">
<div class="section-heading">
<h2 id="filters-title">{{ app()->getLocale()==='am' ? 'ፈጣን ማጣሪያዎች' : 'Quick Filters' }}</h2>
<button type="button" class="section-more" id="more-filters" aria-expanded="false">{{ app()->getLocale()==='am' ? 'ተጨማሪ' : 'More Filters' }}</button>
</div>
<div class="filter-chips" id="filter-chips" role="group" aria-label="{{ app()->getLocale()==='am' ? 'ማጣሪያዎች' : 'Filters' }}">
<button type="button" class="filter-chip active" data-filter="all" aria-pressed="true">{{ app()->getLocale()==='am' ? 'ሁሉም' : 'All' }}</button>
<button type="button" class="filter-chip" data-filter="verified" aria-pressed="false">{{ app()->getLocale()==='am' ? 'የተረጋገጡ' : 'Verified' }}</button>
<button type="button" class="filter-chip" data-filter="urgent" aria-pressed="false">{{ app()->getLocale()==='am' ? 'አስቸኳይ' : 'Urgent' }}</button>
<button type="button" class="filter-chip" data-filter="newest" aria-pressed="false">{{ app()->getLocale()==='am' ? 'አዲስ' : 'Newest' }}</button>
<button type="button" class="filter-chip" data-filter="budget_high" aria-pressed="false">{{ app()->getLocale()==='am' ? 'ከፍተኛ በጀት' : 'High Budget' }}</button>
</div>
</section>

{{-- ═══════════════════════════════════════════════════════════
     5. MARKETPLACE NEEDS
     ═══════════════════════════════════════════════════════════ --}}
<section class="needs-section" aria-labelledby="needs-title">
<div class="section-heading">
<div>
<h2 id="needs-title">{{ app()->getLocale()==='am' ? 'የገበያ ፍላጎቶች' : 'Marketplace needs' }}</h2>
<p>{{ app()->getLocale()==='am' ? 'አቅራቢዎች ለመስጠት የሚችሉትን ፍላጎቶች ይመልከቱ።' : 'Explore needs that providers can respond to.' }}</p>
</div>
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

{{-- ═══════════════════════════════════════════════════════════
     6. AI PANEL (compact, moved down)
     ═══════════════════════════════════════════════════════════ --}}
<section class="ai-panel ai-panel--compact" aria-labelledby="ai-help-title">
<span class="ai-mark" aria-hidden="true">AI</span>
<div>
<h3 id="ai-help-title">{{ app()->getLocale()==='am' ? 'Felagi AI ይረዳዎታል' : 'Felagi AI can help' }}</h3>
<p>{{ app()->getLocale()==='am' ? 'ፍላጎትዎን በተፈጥሯዊ ቋንቋ ይጻፉ።' : 'Describe your need naturally.' }}</p>
<div class="ai-prompt"><button type="button" data-prompt="{{ app()->getLocale()==='am' ? 'የጭነት መኪና እፈልጋለሁ' : 'I need a delivery truck' }}">{{ app()->getLocale()==='am' ? 'ምሳሌ ይሞክሩ' : 'Try an example' }}</button></div>
</div>
<a href="/needs/new">{{ app()->getLocale()==='am' ? 'ይጀምሩ' : 'Start' }} →</a>
</section>

{{-- Hidden controls for JS compatibility --}}
<div class="sr-only" aria-hidden="true">
<select id="sort" tabindex="-1">
<option value="newest">{{ __('sortNewest') }}</option>
<option value="budget_low">{{ __('sortBudgetLow') }}</option>
<option value="budget_high">{{ __('sortBudgetHigh') }}</option>
<option value="deadline_soon">{{ __('sortDeadlineSoon') }}</option>
</select>
<button type="button" id="clear" tabindex="-1">{{ __('clearFilters') }}</button>
</div>

{{-- Bottom Sheet: Advanced Filters --}}
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
</main>
{{-- Bottom Nav — L349 shared partial --}}
@include('partials.bottom-nav')

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
var S={keyword:'',cat:null,sort:'newest',filter:null,items:[]};
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
  if(S.filter&&S.filter!=='all')q.set('filter',S.filter);
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
        var category=it.category?(it.category.name_am||it.category.name_en||it.category.slug||''):'';
        var location=it.location_text||'';
        var deadline=it.offer_deadline_at||it.deadline_at||'';
        var posted=it.created_at?new Date(it.created_at).toLocaleDateString(document.documentElement.lang==='am'?'am-ET':'en-US',{month:'short',day:'numeric'}):'';
        var offers=it.offers_count!=null?String(it.offers_count):'';
        var tags='';
        if(it.verified||it.requester_verified)tags+='<span class="card-tag verified">✓ '+(document.documentElement.lang==='am'?'የተረጋገጠ':'Verified')+'</span>';
        if(it.urgent)tags+='<span class="card-tag urgent">'+(document.documentElement.lang==='am'?'አስቸኳይ':'Urgent')+'</span>';
        var key='felagi-bookmark-'+it.id, saved=localStorage.getItem(key)==='1';
        var match=it.ai_match_score!=null?String(it.ai_match_score)+'% '+(document.documentElement.lang==='am'?'ተመሳሳይነት':'match'):'';
        return '<a class="card" href="/needs/'+esc(it.id)+'"><div class="card-top"><span class="card-badge">'+esc(category|| (document.documentElement.lang==='am'?'ፍላጎት':'Need'))+'</span><span class="card-tag status">'+(document.documentElement.lang==='am'?'ንቁ':'Active')+'</span><div class="card-tags">'+tags+'</div></div><h3>'+esc(it.title)+'</h3><div class="budget">'+esc(bud)+'</div><div class="card-meta">'+(location?'<span>⌖ '+esc(location)+'</span>':'')+(posted?'<span>◷ '+esc(posted)+'</span>':'')+(offers?'<span>↗ '+esc(offers)+' '+(document.documentElement.lang==='am'?'አቅርቦቶች':'offers')+'</span>':'')+'</div>'+(deadline?'<div class="card-meta"><span>⌛ '+esc(deadline)+'</span></div>':'')+'<p>'+esc((it.description||'').slice(0,140))+'</p><div class="card-actions"><span class="match">'+esc(match)+'</span><span class="card-cta">'+(document.documentElement.lang==='am'?'ዝርዝሩን ይመልከቱ':'View need')+' →</span><span class="action-set"><button type="button" class="bookmark '+(saved?'active':'')+'" aria-label="'+(document.documentElement.lang==='am'?'አስቀምጥ':'Bookmark')+'" data-bookmark="'+esc(it.id)+'">'+(saved?'★':'☆')+'</button><button type="button" class="share" aria-label="'+(document.documentElement.lang==='am'?'አጋራ':'Share')+'" data-share="'+esc(it.id)+'">↗</button></span></div></a>';
      }).join('');
      Array.prototype.forEach.call(document.querySelectorAll('[data-bookmark]'),function(b){b.onclick=function(e){e.preventDefault();e.stopPropagation();var k='felagi-bookmark-'+b.dataset.bookmark,on=!b.classList.contains('active');localStorage.setItem(k,on?'1':'0');b.classList.toggle('active',on);b.textContent=on?'★':'☆';};});
      Array.prototype.forEach.call(document.querySelectorAll('[data-share]'),function(b){b.onclick=function(e){e.preventDefault();e.stopPropagation();var u=location.origin+'/needs/'+b.dataset.share;if(navigator.share){navigator.share({title:document.title,url:u}).catch(function(){});}else if(navigator.clipboard){navigator.clipboard.writeText(u);b.textContent='✓';setTimeout(function(){b.textContent='↗';},1200);}};});
    })
    .catch(function(e){console.warn('[S004] needs',e);$('feed').innerHTML='';$('error').hidden=false;});
}
var tm=null;
function renderHistory(){var h=$('search-history'),items=[];try{items=JSON.parse(localStorage.getItem('felagi-search-history')||'[]');}catch(e){};h.innerHTML=items.slice(0,4).map(function(x){return '<button type="button" data-history="'+esc(x)+'">◷ '+esc(x)+'</button>';}).join('');h.classList.toggle('show',items.length>0);Array.prototype.forEach.call(h.querySelectorAll('[data-history]'),function(b){b.onclick=function(){ $('keyword').value=b.dataset.history;S.keyword=b.dataset.history;load();};});}
function rememberSearch(v){if(!v)return;var a=[];try{a=JSON.parse(localStorage.getItem('felagi-search-history')||'[]');}catch(e){};a=[v].concat(a.filter(function(x){return x!==v;})).slice(0,4);localStorage.setItem('felagi-search-history',JSON.stringify(a));
/* ─── L350: IA restructure JS ─── */

// Search clear button visibility
(function(){
  var inp = $('keyword');
  var clearBtn = $('search-clear');
  function update(){ clearBtn.classList.toggle('is-visible', !!inp.value.trim()); }
  inp.addEventListener('input', update);
  clearBtn.addEventListener('click', function(){ inp.value=''; S.keyword=''; update(); load(); inp.focus(); });
  update();
})();

// Categories: "More" toggle
(function(){
  var chipsEl = $('chips');
  var btn = $('more-categories');
  if (!btn || !chipsEl) return;
  btn.addEventListener('click', function(){
    var limited = chipsEl.getAttribute('data-limited') === 'true';
    chipsEl.setAttribute('data-limited', limited ? 'false' : 'true');
    btn.setAttribute('aria-expanded', limited ? 'true' : 'false');
    btn.textContent = limited ? '← {{ app()->getLocale()==="am" ? "ያንስ" : "Less" }}' : '{{ app()->getLocale()==="am" ? "ሁሉም" : "More" }} →';
  });
})();

// Filter chips: toggle active, set filter state
(function(){
  var chips = document.querySelectorAll('.filter-chip');
  Array.prototype.forEach.call(chips, function(chip){
    chip.addEventListener('click', function(){
      var filter = chip.getAttribute('data-filter');
      if (filter === 'all') {
        Array.prototype.forEach.call(chips, function(c){ c.classList.remove('active'); c.setAttribute('aria-pressed','false'); });
        chip.classList.add('active'); chip.setAttribute('aria-pressed','true');
        S.filter = null;
      } else {
        document.querySelector('.filter-chip[data-filter="all"]').classList.remove('active');
        document.querySelector('.filter-chip[data-filter="all"]').setAttribute('aria-pressed','false');
        var wasActive = chip.classList.contains('active');
        chip.classList.toggle('active');
        chip.setAttribute('aria-pressed', wasActive ? 'false' : 'true');
        S.filter = wasActive ? null : filter;
      }
      load();
    });
  });
})();

// Bottom sheet toggle
(function(){
  var sheet = $('advanced-filters');
  var openBtn = $('more-filters');
  if (!sheet || !openBtn) return;
  function open(){ sheet.hidden = false; sheet.setAttribute('aria-hidden','false'); document.body.style.overflow='hidden'; }
  function close(){ sheet.hidden = true; sheet.setAttribute('aria-hidden','true'); document.body.style.overflow=''; }
  openBtn.addEventListener('click', open);
  Array.prototype.forEach.call(sheet.querySelectorAll('[data-sheet-close]'), function(el){ el.addEventListener('click', close); });
  document.addEventListener('keydown', function(e){ if (e.key === 'Escape' && !sheet.hidden) close(); });

  // Sync visible sort -> hidden sort
  var sortVisible = $('sort-visible');
  if (sortVisible) {
    sortVisible.value = S.sort || 'newest';
    sortVisible.addEventListener('change', function(){ S.sort = sortVisible.value; $('sort').value = sortVisible.value; load(); });
  }

  // Clear filters
  var clearVisible = $('clear-filters-visible');
  if (clearVisible) {
    clearVisible.addEventListener('click', function(){
      Array.prototype.forEach.call(document.querySelectorAll('.filter-chip'), function(c){ c.classList.remove('active'); c.setAttribute('aria-pressed','false'); });
      document.querySelector('.filter-chip[data-filter="all"]').classList.add('active');
      document.querySelector('.filter-chip[data-filter="all"]').setAttribute('aria-pressed','true');
      S.filter = null; S.cat = null; S.keyword = '';
      $('keyword').value = '';
      $('search-clear').classList.remove('is-visible');
      var firstChip = $('chips').querySelector('.chip');
      if (firstChip) { Array.prototype.forEach.call($('chips').querySelectorAll('.chip'), function(c,i){ c.classList.toggle('active', i===0); }); }
      load();
    });
  }
})();

renderHistory();}
$('keyword').addEventListener('input',function(e){
  clearTimeout(tm);
  tm=setTimeout(function(){S.keyword=e.target.value.trim().slice(0,255);rememberSearch(S.keyword);load();},300);
});
$('sort').addEventListener('change',function(e){S.sort=e.target.value;load();});
$('clear').addEventListener('click',function(){
  S.keyword='';S.cat=null;S.sort='newest';
  $('keyword').value='';$('sort').value='newest';
  var btns=$('chips').querySelectorAll('.chip');
  Array.prototype.forEach.call(btns,function(x,i){x.classList.toggle('active',i===0);});
  load();
});
Array.prototype.forEach.call(document.querySelectorAll('[data-prompt]'),function(b){b.onclick=function(){window.location.href='/needs/new?prompt='+encodeURIComponent(b.dataset.prompt);};});
Array.prototype.forEach.call(document.querySelectorAll('[data-quick]'),function(b){b.onclick=function(){$('keyword').value=b.dataset.quick;S.keyword=b.dataset.quick;rememberSearch(S.keyword);load();$('keyword').focus();};});
renderHistory();
loadCats();
load();
})();
</script>
</body>
</html>
