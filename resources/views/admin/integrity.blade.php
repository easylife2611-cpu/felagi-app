@extends('layouts.admin')
@section('title', __('adminIntegrity'))
@section('page-title', __('adminIntegrity'))
@section('content')
<div class="pending">
<strong>{{ __('adminPendingTitle') }}</strong>
{{ __('adminPendingBody') }}
</div>
<div class="card">
<h2>{{ __('adminIntegrityChecks') }}</h2>
<table class="table">
<thead><tr><th>{{ __('adminCheck') }}</th><th>{{ __('adminStatus') }}</th></tr></thead>
<tbody>
<tr><td>{{ __('adminCheckForeignKeys') }}</td><td><span class="badge warn">{{ __('adminCheckUnknown') }}</span></td></tr>
<tr><td>{{ __('adminCheckOrphanRecords') }}</td><td><span class="badge warn">{{ __('adminCheckUnknown') }}</span></td></tr>
<tr><td>{{ __('adminCheckChecksums') }}</td><td><span class="badge warn">{{ __('adminCheckUnknown') }}</span></td></tr>
</tbody>
</table>
</div>
@endsection
