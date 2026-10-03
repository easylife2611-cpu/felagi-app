@extends('layouts.admin')
@section('title', __('screenA016'))
@section('page-title', __('adminAudit'))
@section('content')
<div class="pending">
<strong>{{ __('adminPendingTitle') }}</strong>
{{ __('adminPendingBody') }}
</div>
<div class="card">
<h2>{{ __('adminAuditLog') }}</h2>
<table class="table">
<thead><tr><th>{{ __('adminWhen') }}</th><th>{{ __('adminWho') }}</th><th>{{ __('adminAction') }}</th><th>{{ __('adminTarget') }}</th></tr></thead>
<tbody>
<tr><td colspan="4" style="text-align:center;color:#586675;padding:24px">{{ __('adminNoAuditEntries') }}</td></tr>
</tbody>
</table>
</div>
@endsection
