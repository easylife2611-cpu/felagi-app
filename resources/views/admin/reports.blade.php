@extends('layouts.admin')
@section('title', __('screenA021'))
@section('page-title', __('adminReports'))
@section('content')
<div class="pending">
<strong>{{ __('adminPendingTitle') }}</strong>
{{ __('adminPendingBody') }}
</div>
<div class="card">
<h2>{{ __('adminUserReports') }}</h2>
<table class="table">
<thead><tr><th>{{ __('adminWhen') }}</th><th>{{ __('adminType') }}</th><th>{{ __('adminTarget') }}</th><th>{{ __('adminStatus') }}</th></tr></thead>
<tbody>
<tr><td colspan="4" style="text-align:center;color:#586675;padding:24px">{{ __('adminNoReports') }}</td></tr>
</tbody>
</table>
</div>
@endsection
