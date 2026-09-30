@extends('layouts.admin')
@section('title', __('adminDashboard'))
@section('page-title', __('adminDashboard'))
@section('content')
<div class="pending">
<strong>{{ __('adminPendingTitle') }}</strong>
{{ __('adminPendingBody') }}
</div>

<div class="stat-grid" id="stats">
<div class="stat info"><div class="lbl">{{ __('adminStatUsers') }}</div><div class="val" id="stat-users">—</div><div class="hint">{{ __('adminStatUsersHint') }}</div></div>
<div class="stat ok"><div class="lbl">{{ __('adminStatNeeds') }}</div><div class="val" id="stat-needs">—</div><div class="hint">{{ __('adminStatNeedsHint') }}</div></div>
<div class="stat warn"><div class="lbl">{{ __('adminStatOffers') }}</div><div class="val" id="stat-offers">—</div><div class="hint">{{ __('adminStatOffersHint') }}</div></div>
<div class="stat info"><div class="lbl">{{ __('adminStatReports') }}</div><div class="val" id="stat-reports">—</div><div class="hint">{{ __('adminStatReportsHint') }}</div></div>
</div>

<div class="card">
<h2>{{ __('adminRecentActivity') }}</h2>
<div class="empty">
<div class="ic">&#128202;</div>
<p>{{ __('adminNoActivity') }}</p>
</div>
</div>
@endsection
