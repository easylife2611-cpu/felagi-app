@extends('layouts.admin')
@section('title', __('adminContent'))
@section('page-title', __('adminContent'))
@section('content')
<div class="pending">
<strong>{{ __('adminPendingTitle') }}</strong>
{{ __('adminPendingBody') }}
</div>
<div class="card">
<h2>{{ __('adminContentItems') }}</h2>
<div class="empty">
<div class="ic">&#128196;</div>
<h3>{{ __('adminNoContent') }}</h3>
<p>{{ __('adminContentIntro') }}</p>
</div>
</div>
@endsection
