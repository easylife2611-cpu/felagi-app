@extends('layouts.admin')
@section('title', __('screenA007'))
@section('page-title', __('adminPayments'))
@section('content')
<div class="pending">
<strong>{{ __('adminPendingTitle') }}</strong>
{{ __('adminPendingBody') }}
</div>
<div class="stat-grid">
<div class="stat info"><div class="lbl">{{ __('adminStatTotalPayments') }}</div><div class="val">—</div></div>
<div class="stat ok"><div class="lbl">{{ __('adminStatSucceeded') }}</div><div class="val">—</div></div>
<div class="stat err"><div class="lbl">{{ __('adminStatFailed') }}</div><div class="val">—</div></div>
</div>
<div class="card">
<h2>{{ __('adminRecentPayments') }}</h2>
<div class="empty">
<div class="ic">&#128176;</div>
<p>{{ __('adminNoPayments') }}</p>
</div>
</div>
@endsection
