@extends('layouts.admin')
@section('title', __('adminFeatures'))
@section('page-title', __('adminFeatures'))
@section('content')
<div class="pending">
<strong>{{ __('adminPendingTitle') }}</strong>
{{ __('adminPendingBody') }}
</div>
<div class="card">
<h2>{{ __('adminFeatureFlags') }}</h2>
<table class="table">
<thead><tr><th>{{ __('adminFeature') }}</th><th>{{ __('adminStatus') }}</th><th>{{ __('adminActions') }}</th></tr></thead>
<tbody>
<tr><td>{{ __('adminFeatureOidc') }}</td><td><span class="badge err">{{ __('adminOff') }}</span></td><td><button class="btn sm sec" onclick="adminToast('{{ __('adminPendingBody') }}')">{{ __('adminToggle') }}</button></td></tr>
<tr><td>{{ __('adminFeatureWidget') }}</td><td><span class="badge ok">{{ __('adminOn') }}</span></td><td><button class="btn sm sec" onclick="adminToast('{{ __('adminPendingBody') }}')">{{ __('adminToggle') }}</button></td></tr>
<tr><td>{{ __('adminFeatureAI') }}</td><td><span class="badge warn">{{ __('adminPendingLabel') }}</span></td><td><button class="btn sm sec" onclick="adminToast('{{ __('adminPendingBody') }}')">{{ __('adminToggle') }}</button></td></tr>
<tr><td>{{ __('adminFeatureAds') }}</td><td><span class="badge warn">{{ __('adminPendingLabel') }}</span></td><td><button class="btn sm sec" onclick="adminToast('{{ __('adminPendingBody') }}')">{{ __('adminToggle') }}</button></td></tr>
</tbody>
</table>
</div>
@endsection
