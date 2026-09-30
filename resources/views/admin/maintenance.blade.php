@extends('layouts.admin')
@section('title', __('adminMaintenance'))
@section('page-title', __('adminMaintenance'))
@section('content')
<div class="pending">
<strong>{{ __('adminPendingTitle') }}</strong>
{{ __('adminPendingBody') }}
</div>
<div class="card">
<h2>{{ __('adminMaintenanceWindows') }}</h2>
<div class="empty">
<div class="ic">&#128736;</div>
<h3>{{ __('adminNoMaintenance') }}</h3>
<p>{{ __('adminMaintenanceIntro') }}</p>
<button class="btn primary" onclick="adminToast('{{ __('adminPendingBody') }}')">{{ __('adminScheduleMaintenance') }}</button>
</div>
</div>
@endsection
