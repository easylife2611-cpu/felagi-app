@extends('layouts.admin')
@section('title', __('adminMonetization'))
@section('page-title', __('adminMonetization'))
@section('content')
<div class="pending">
<strong>{{ __('adminPendingTitle') }}</strong>
{{ __('adminPendingBody') }}
</div>
<div class="stat-grid">
<div class="stat info"><div class="lbl">{{ __('adminStatRevenue') }}</div><div class="val">—</div></div>
<div class="stat ok"><div class="lbl">{{ __('adminStatActiveBoosts') }}</div><div class="val">—</div></div>
<div class="stat warn"><div class="lbl">{{ __('adminStatUnlocks') }}</div><div class="val">—</div></div>
</div>
<div class="card">
<h2>{{ __('adminMonetizationIntro') }}</h2>
<div class="empty">
<div class="ic">&#128200;</div>
<p>{{ __('adminMonetizationDetail') }}</p>
</div>
</div>
@endsection
