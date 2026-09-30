@extends('layouts.admin')
@section('title', __('adminRecovery'))
@section('page-title', __('adminRecovery'))
@section('content')
<div class="pending">
<strong>{{ __('adminPendingTitle') }}</strong>
{{ __('adminPendingBody') }}
</div>
<div class="card">
<h2>{{ __('adminRecoveryActions') }}</h2>
<div class="empty">
<div class="ic">&#9851;</div>
<h3>{{ __('adminNoRecoveryActions') }}</h3>
<p>{{ __('adminRecoveryIntro') }}</p>
</div>
</div>
@endsection
