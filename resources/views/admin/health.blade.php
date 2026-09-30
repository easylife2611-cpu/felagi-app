@extends('layouts.admin')
@section('title', __('adminHealth'))
@section('page-title', __('adminHealth'))
@section('content')
<div class="pending">
<strong>{{ __('adminPendingTitle') }}</strong>
{{ __('adminPendingBody') }}
</div>
<div class="card">
<h2>{{ __('adminSystemHealth') }}</h2>
<table class="table">
<thead><tr><th>{{ __('adminComponent') }}</th><th>{{ __('adminStatus') }}</th><th>{{ __('adminLatency') }}</th></tr></thead>
<tbody>
<tr><td>Database</td><td><span class="badge ok">{{ __('adminCheckOk') }}</span></td><td>—</td></tr>
<tr><td>Cache</td><td><span class="badge ok">{{ __('adminCheckOk') }}</span></td><td>—</td></tr>
<tr><td>Queue</td><td><span class="badge warn">{{ __('adminCheckUnknown') }}</span></td><td>—</td></tr>
<tr><td>Storage</td><td><span class="badge ok">{{ __('adminCheckOk') }}</span></td><td>—</td></tr>
<tr><td>Telegram</td><td><span class="badge warn">{{ __('adminCheckUnknown') }}</span></td><td>—</td></tr>
</tbody>
</table>
</div>
@endsection
