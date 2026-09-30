@extends('layouts.admin')
@section('title', __('adminNotifications'))
@section('page-title', __('adminNotifications'))
@section('content')
<div class="pending">
<strong>{{ __('adminPendingTitle') }}</strong>
{{ __('adminPendingBody') }}
</div>
<div class="card">
<h2>{{ __('adminNotificationTemplates') }}</h2>
<div class="empty">
<div class="ic">&#128276;</div>
<h3>{{ __('adminNoTemplates') }}</h3>
<p>{{ __('adminNotificationsIntro') }}</p>
</div>
</div>
@endsection
