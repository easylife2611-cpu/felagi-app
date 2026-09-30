@extends('layouts.admin')
@section('title', __('adminMarketplace'))
@section('page-title', __('adminMarketplace'))
@section('content')
<div class="pending">
<strong>{{ __('adminPendingTitle') }}</strong>
{{ __('adminPendingBody') }}
</div>
<div class="stat-grid">
<div class="stat info"><div class="lbl">{{ __('adminStatNeeds') }}</div><div class="val">—</div></div>
<div class="stat info"><div class="lbl">{{ __('adminStatOffers') }}</div><div class="val">—</div></div>
<div class="stat info"><div class="lbl">{{ __('adminStatAwards') }}</div><div class="val">—</div></div>
</div>
<div class="card">
<h2>{{ __('adminRecentNeeds') }}</h2>
<div class="empty">
<div class="ic">&#128722;</div>
<p>{{ __('adminMarketplaceIntro') }}</p>
</div>
</div>
@endsection
