@extends('layouts.admin')
@section('title', __('adminSecurity'))
@section('page-title', __('adminSecurity'))
@section('content')
<div class="pending">
<strong>{{ __('adminPendingTitle') }}</strong>
{{ __('adminPendingBody') }}
</div>
<div class="card">
<h2>{{ __('adminSecurityOverview') }}</h2>
<div class="empty">
<div class="ic">&#128274;</div>
<p>{{ __('adminSecurityIntro') }}</p>
</div>
</div>
<div class="card">
<h2>{{ __('adminRecentSessions') }}</h2>
<div class="empty">
<div class="ic">&#128100;</div>
<p>{{ __('adminNoSessions') }}</p>
</div>
</div>
@endsection
