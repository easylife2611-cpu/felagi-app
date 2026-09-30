@extends('layouts.admin')
@section('title', __('adminUsers'))
@section('page-title', __('adminUsers'))
@section('content')
<div class="pending">
<strong>{{ __('adminPendingTitle') }}</strong>
{{ __('adminPendingBody') }}
</div>
<div class="card">
<h2>{{ __('adminUserList') }}</h2>
<div class="empty">
<div class="ic">&#128101;</div>
<h3>{{ __('adminNoUsers') }}</h3>
<p>{{ __('adminUsersIntro') }}</p>
</div>
</div>
@endsection
