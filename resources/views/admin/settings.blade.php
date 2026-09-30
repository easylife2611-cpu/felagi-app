@extends('layouts.admin')
@section('title', __('adminSettings'))
@section('page-title', __('adminSettings'))
@section('content')
<div class="pending">
<strong>{{ __('adminPendingTitle') }}</strong>
{{ __('adminPendingBody') }}
</div>
<div class="card">
<h2>{{ __('adminPlatformSettings') }}</h2>
<table class="table">
<thead><tr><th>{{ __('adminSettingKey') }}</th><th>{{ __('adminSettingValue') }}</th><th>{{ __('adminActions') }}</th></tr></thead>
<tbody>
<tr><td>app.name</td><td>Felagi</td><td><button class="btn sm sec" onclick="adminToast('{{ __('adminPendingBody') }}')">{{ __('adminEdit') }}</button></td></tr>
<tr><td>app.locale_default</td><td>am</td><td><button class="btn sm sec" onclick="adminToast('{{ __('adminPendingBody') }}')">{{ __('adminEdit') }}</button></td></tr>
<tr><td>offer.free_limit</td><td>—</td><td><button class="btn sm sec" onclick="adminToast('{{ __('adminPendingBody') }}')">{{ __('adminEdit') }}</button></td></tr>
</tbody>
</table>
</div>
@endsection
