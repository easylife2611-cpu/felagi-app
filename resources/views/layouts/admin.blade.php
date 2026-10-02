<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>@yield('title', __('adminPanel')) — {{ __('brand') }}</title>
<style>
*{box-sizing:border-box;margin:0;padding:0}
body{font-family:system-ui,-apple-system,sans-serif;background:#F4F6F8;color:#192431;line-height:1.5;min-height:100vh}
.admin-shell{display:flex;min-height:100vh}
.sidebar{width:240px;background:#0a2540;color:#e3e8ed;position:fixed;top:0;bottom:0;left:0;overflow-y:auto;z-index:20;transition:transform .2s}
.sidebar .brand{padding:18px 20px;font-size:17px;font-weight:700;color:#fff;border-bottom:1px solid rgba(255,255,255,.08);display:flex;align-items:center;gap:8px}
.sidebar .brand .dot{width:8px;height:8px;background:#26c281;border-radius:50%}
.sidebar nav{padding:12px 0}
.sidebar nav .group{padding:10px 20px 4px;font-size:10px;text-transform:uppercase;letter-spacing:.8px;color:#94a3b8;font-weight:700}
.sidebar nav a{display:flex;align-items:center;gap:10px;padding:9px 20px;color:#b8c5d1;text-decoration:none;font-size:13px;transition:background .12s,color .12s;border-left:3px solid transparent}
.sidebar nav a:hover{background:rgba(255,255,255,.05);color:#fff}
.sidebar nav a.active{background:rgba(38,194,129,.1);color:#fff;border-left-color:#26c281}
.sidebar nav a .ic{width:16px;text-align:center;font-size:14px;opacity:.85}
.content{flex:1;margin-left:240px;display:flex;flex-direction:column;min-height:100vh}
.topbar{background:#fff;border-bottom:1px solid #e5eaef;padding:14px 24px;display:flex;justify-content:space-between;align-items:center;position:sticky;top:0;z-index:15;box-shadow:0 1px 2px rgba(0,0,0,.02)}
.topbar .page-title{font-size:16px;font-weight:600;color:#0a2540}
.topbar .right{display:flex;gap:12px;align-items:center;font-size:13px;color:#5a738e}
.topbar .right .who{display:flex;gap:6px;align-items:center}
.topbar .btn-logout{padding:7px 14px;background:#eef2f6;color:#192431;border:1px solid #d8dee5;border-radius:6px;cursor:pointer;font-size:12px;font-weight:600;font-family:inherit}
.topbar .btn-logout:hover{background:#e0e6ec}
main{padding:24px;flex:1;max-width:1400px}
.hamburger{display:none;background:transparent;border:none;font-size:20px;cursor:pointer;padding:4px 8px}
.card{background:#fff;border-radius:10px;padding:20px;margin-bottom:16px;box-shadow:0 1px 3px rgba(0,0,0,.06);border:1px solid #eef2f6}
.card h2{font-size:15px;font-weight:600;color:#0a2540;margin-bottom:12px}
.card h3{font-size:13px;font-weight:600;color:#0a2540;margin-bottom:8px;margin-top:16px}
.card h3:first-child{margin-top:0}
.card p{color:#586675;font-size:14px;line-height:1.6;margin-bottom:10px}
.pending{background:#fff8e1;border:1px solid #ffe082;color:#8a6d00;padding:12px 14px;border-radius:8px;font-size:13px;line-height:1.5;margin-bottom:14px}
.pending strong{display:block;margin-bottom:3px}
.stat-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(180px,1fr));gap:12px;margin-bottom:14px}
.stat{background:#fff;border:1px solid #eef2f6;border-radius:10px;padding:16px}
.stat .lbl{font-size:11px;color:#586675;text-transform:uppercase;letter-spacing:.5px;font-weight:600;margin-bottom:6px}
.stat .val{font-size:24px;font-weight:700;color:#0a2540;line-height:1.1}
.stat .hint{font-size:11px;color:#586675;margin-top:4px}
.stat.ok{border-left:3px solid #26c281}
.stat.warn{border-left:3px solid #f5a623}
.stat.err{border-left:3px solid #e74c3c}
.stat.info{border-left:3px solid #3498db}
.skeleton{background:#fff;border-radius:10px;padding:16px;margin-bottom:10px;box-shadow:0 1px 3px rgba(0,0,0,.06)}
.sk-line{height:12px;background:#eef2f6;border-radius:4px;margin-bottom:8px}
.sk-line.w40{width:40%}.sk-line.w70{width:70%}.sk-line.w90{width:90%}.sk-line.w100{width:100%}
.empty{padding:60px 20px;text-align:center;color:#586675}
.empty .ic{font-size:48px;opacity:.4;margin-bottom:12px}
.empty h3{color:#0a2540;font-size:16px;margin-bottom:8px}
.empty p{margin-bottom:16px}
.btn{padding:10px 18px;border-radius:8px;font-size:13px;font-weight:600;cursor:pointer;border:none;font-family:inherit;text-decoration:none;text-align:center;display:inline-block;transition:background .15s}
.btn.primary{background:#003366;color:#fff}
.btn.primary:hover:not(:disabled){background:#002a52}
.btn.sec{background:#eef2f6;color:#192431}
.btn.sec:hover:not(:disabled){background:#e0e6ec}
.btn.sm{padding:6px 12px;font-size:12px;border-radius:6px}
.btn:disabled{opacity:.5;cursor:not-allowed}
.badge{display:inline-block;padding:3px 10px;border-radius:999px;font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.5px}
.badge.ok{background:#e8f5e9;color:#1b5e20}
.badge.warn{background:#fff3e0;color:#e65100}
.badge.err{background:#ffebee;color:#b71c1c}
.badge.info{background:#e3f2fd;color:#0d47a1}
.table{width:100%;border-collapse:collapse;font-size:13px}
.table th{text-align:left;padding:10px 12px;border-bottom:2px solid #eef2f6;font-weight:600;color:#5a738e;font-size:11px;text-transform:uppercase;letter-spacing:.5px}
.table td{padding:11px 12px;border-bottom:1px solid #eef2f6;color:#192431}
.table tr:hover{background:#fafbfc}
.toast{position:fixed;bottom:24px;right:24px;background:#0a2540;color:#fff;padding:12px 20px;border-radius:8px;font-size:13px;z-index:100;opacity:0;pointer-events:none;transition:opacity .2s,transform .2s;transform:translateY(10px);max-width:340px}
.toast.on{opacity:1;transform:translateY(0)}
@media(max-width:900px){
  .sidebar{transform:translateX(-100%)}
  .sidebar.open{transform:translateX(0)}
  .content{margin-left:0}
  .hamburger{display:block}
}
</style>
@stack('head')
</head>
<body>
<div class="admin-shell">
  <aside class="sidebar" id="sidebar">
    <div class="brand"><span class="dot"></span> {{ __('adminPanel') }}</div>
    <nav>
      <div class="group">{{ __('adminGroupOverview') }}</div>
      <a href="/admin/dashboard" data-key="A001"><span class="ic">&#127968;</span>{{ __('adminDashboard') }}</a>
      <a href="/admin/health" data-key="A003"><span class="ic">&#128153;</span>{{ __('adminHealth') }}</a>
      <a href="/admin/recovery" data-key="A018"><span class="ic">&#9851;</span>{{ __('adminRecovery') }}</a>

      <div class="group">{{ __('adminGroupMarketplace') }}</div>
      <a href="/admin/marketplace" data-key="A005"><span class="ic">&#128722;</span>{{ __('adminMarketplace') }}</a>
      <a href="/admin/users" data-key="A008"><span class="ic">&#128101;</span>{{ __('adminUsers') }}</a>
      <a href="/admin/content" data-key="A009"><span class="ic">&#128196;</span>{{ __('adminContent') }}</a>
      <a href="/admin/reports" data-key="A022"><span class="ic">&#128681;</span>{{ __('adminReports') }}</a>

      <div class="group">{{ __('adminGroupDistribution') }}</div>
      <a href="/admin/telegram" data-key="A002"><span class="ic">&#128172;</span>{{ __('adminTelegram') }}</a>
      <a href="/admin/notifications" data-key="A010"><span class="ic">&#128276;</span>{{ __('adminNotifications') }}</a>
      <a href="/admin/ai" data-key="A006"><span class="ic">&#129302;</span>{{ __('adminAI') }}</a>

      <div class="group">{{ __('adminGroupPayments') }}</div>
      <a href="/admin/payments" data-key="A007"><span class="ic">&#128176;</span>{{ __('adminPayments') }}</a>
      <a href="/admin/monetization" data-key="A020"><span class="ic">&#128200;</span>{{ __('adminMonetization') }}</a>

      <div class="group">{{ __('adminGroupOps') }}</div>
      <a href="/admin/features" data-key="A004"><span class="ic">&#128295;</span>{{ __('adminFeatures') }}</a>
      <a href="/admin/settings" data-key="A017"><span class="ic">&#9881;</span>{{ __('adminSettings') }}</a>
      <a href="/admin/jobs" data-key="A012"><span class="ic">&#128337;</span>{{ __('adminJobs') }}</a>
      <a href="/admin/files" data-key="A011"><span class="ic">&#128193;</span>{{ __('adminFiles') }}</a>
      <a href="/admin/backups" data-key="A013"><span class="ic">&#128190;</span>{{ __('adminBackups') }}</a>
      <a href="/admin/maintenance" data-key="A021"><span class="ic">&#128736;</span>{{ __('adminMaintenance') }}</a>

      <div class="group">{{ __('adminGroupSecurity') }}</div>
      <a href="/admin/security" data-key="A015"><span class="ic">&#128274;</span>{{ __('adminSecurity') }}</a>
      <a href="/admin/audit" data-key="A016"><span class="ic">&#128220;</span>{{ __('adminAudit') }}</a>
      <a href="/admin/integrity" data-key="A014"><span class="ic">&#128269;</span>{{ __('adminIntegrity') }}</a>
      <a href="/admin/safe-mode" data-key="A019"><span class="ic">&#128680;</span>{{ __('adminSafeMode') }}</a>
    </nav>
  </aside>

  <div class="content">
    <header class="topbar">
      <div style="display:flex;align-items:center;gap:12px">
        <button class="hamburger" onclick="document.getElementById('sidebar').classList.toggle('open')">&#9776;</button>
        <span class="page-title">@yield('page-title', __('adminPanel'))</span>
      </div>
      <div class="right">
        <span class="who" id="admin-who">
          &#128100; {{ auth()->user()->full_name ?? '—' }}
        </span>
        <form method="POST" action="{{ route('admin.logout') }}" style="margin:0">
          @csrf
          <button type="submit" class="btn-logout">{{ __('logout') }}</button>
        </form>
      </div>
    </header>

    <main>
      @yield('content')
    </main>
  </div>
</div>

<div class="toast" id="toast"></div>

<script>
(function(){
'use strict';
var LS_TOKEN='felagi_token';
var csrf=(document.querySelector('meta[name="csrf-token"]')||{}).content||'';

var path=window.location.pathname;
Array.prototype.forEach.call(document.querySelectorAll('.sidebar nav a'),function(a){
  if(a.getAttribute('href')===path)a.classList.add('active');
});

// Admin auth is server-side session-based (form POST + CSRF).
// Logout uses POST /admin/logout with CSRF token.

window.adminToast=function(msg,ms){
  ms=ms||2400;
  var el=document.getElementById('toast');
  el.textContent=msg;
  el.className='toast on';
  setTimeout(function(){el.className='toast';},ms);
};

window.adminToken=function(){return localStorage.getItem(LS_TOKEN);};
window.adminCsrf=csrf;
})();
</script>
@stack('scripts')
</body>
</html>
