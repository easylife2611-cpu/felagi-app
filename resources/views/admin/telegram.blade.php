@extends('layouts.admin')
@section('title', __('screenA002'))
@section('page-title', __('adminTelegram'))
@section('content')
<div class="pending">
<strong>{{ __('adminPendingTitle') }}</strong>
{{ __('adminPendingBody') }}
</div>
<div class="card">
<h2>{{ __('adminTelegramDestinations') }}</h2>
<div class="empty">
<div class="ic">&#128172;</div>
<h3>{{ __('adminNoDestinations') }}</h3>
<p>{{ __('adminTelegramIntro') }}</p>
<button class="btn primary" onclick="adminToast('{{ __('adminPendingBody') }}')">{{ __('adminAddDestination') }}</button>
</div>
</div>
@endsection
