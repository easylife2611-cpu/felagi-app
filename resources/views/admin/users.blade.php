@extends('layouts.admin')
@section('title', __('screenA008'))
@section('page-title', __('adminUsers'))
@section('content')

<div class="pending">
<strong>{{ __('adminPendingTitle') }}</strong>
{{ __('adminUsersIntro') }}
</div>

<div class="stat-grid" id="users-stats">
<div class="stat info"><div class="lbl">{{ __('adminStatUsers') }}</div><div class="val" id="stat-total">—</div></div>
<div class="stat ok"><div class="lbl">{{ __('adminStatUsersActive') ?? 'Active' }}</div><div class="val" id="stat-active">—</div></div>
<div class="stat warn"><div class="lbl">{{ __('adminStatUsersNew') ?? 'New (30d)' }}</div><div class="val" id="stat-new">—</div></div>
<div class="stat err"><div class="lbl">{{ __('adminStatUsersBlocked') ?? 'Blocked' }}</div><div class="val" id="stat-blocked">—</div></div>
</div>

<div class="card">
<div style="display:flex;gap:12px;align-items:center;margin-bottom:12px;flex-wrap:wrap">
<h2 style="margin:0;flex:1">{{ __('adminUserList') }}</h2>
<input type="search" id="user-search" placeholder="{{ __('search') ?? 'Search…' }}" style="padding:8px 12px;border:1px solid #d8dee5;border-radius:6px;font-size:13px;min-width:220px">
<select id="user-role" style="padding:8px 12px;border:1px solid #d8dee5;border-radius:6px;font-size:13px">
<option value="">{{ __('adminAllRoles') ?? 'All roles' }}</option>
<option value="admin">Admin</option>
<option value="provider">Provider</option>
<option value="requester">Requester</option>
</select>
</div>
<div id="users-table"><div class="empty"><p>{{ __('adminLoading') }}</p></div></div>
</div>

<script>
(function(){
'use strict';
const $ = id => document.getElementById(id);
const csrf = document.querySelector('meta[name="csrf-token"]')?.content || '';
function esc(s){return String(s==null?'—':s).replace(/[&<>"']/g,c=>({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c]));}
function setVal(id,v){const el=$(id);if(el)el.textContent=v!=null?v:'—';}

async function loadStats(){
  try{
    const r = await fetch('/api/v1/admin/users',{headers:{'Accept':'application/json'},credentials:'same-origin'});
    if(!r.ok) return;
    const j = await r.json();
    const s = j?.data?.stats || j?.stats || {};
    setVal('stat-total', s.total);
    setVal('stat-active', s.active);
    setVal('stat-new', s.new_30d || s.new);
    setVal('stat-blocked', s.blocked);
  }catch(e){console.warn('stats',e);}
}

async function loadUsers(){
  const q = new URLSearchParams();
  const kw = $('user-search')?.value.trim();
  const role = $('user-role')?.value;
  if(kw) q.set('q', kw);
  if(role) q.set('role', role);
  try{
    const r = await fetch('/api/v1/admin/users?'+q.toString(),{headers:{'Accept':'application/json'},credentials:'same-origin'});
    if(!r.ok) throw new Error('HTTP '+r.status);
    const j = await r.json();
    const items = j?.data?.items || j?.data || [];
    if(!items.length){
      $('users-table').innerHTML = '<div class="empty"><div class="ic">&#128101;</div><h3>{{ __('adminNoUsers') }}</h3><p>{{ __('adminUsersIntro') }}</p></div>';
      return;
    }
    const rows = items.map(u => `<tr>
      <td><strong>${esc(u.full_name || u.name || '—')}</strong></td>
      <td>${esc(u.email || '—')}</td>
      <td>${esc(u.phone || '—')}</td>
      <td><span class="badge info">${esc(u.role || 'user')}</span></td>
      <td>${esc(u.status || 'active')}</td>
      <td>${esc(u.created_at ? new Date(u.created_at).toLocaleDateString() : '—')}</td>
    </tr>`).join('');
    $('users-table').innerHTML = `<table class="table"><thead><tr>
      <th>${esc('{{ __("name") ?? "Name" }}')}</th>
      <th>${esc('{{ __("email") ?? "Email" }}')}</th>
      <th>${esc('{{ __("phone") ?? "Phone" }}')}</th>
      <th>${esc('{{ __("role") ?? "Role" }}')}</th>
      <th>${esc('{{ __("status") ?? "Status" }}')}</th>
      <th>${esc('{{ __("created") ?? "Created" }}')}</th>
    </tr></thead><tbody>${rows}</tbody></table>`;
  }catch(e){console.warn('users',e);$('users-table').innerHTML='<div class="empty"><p>{{ __('loadErrorBody') }}</p></div>';}
}

let t=null;
$('user-search')?.addEventListener('input',()=>{clearTimeout(t);t=setTimeout(loadUsers,300);});
$('user-role')?.addEventListener('change',loadUsers);
loadStats();loadUsers();
})();
</script>
@endsection
