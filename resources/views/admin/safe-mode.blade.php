@extends('layouts.admin')
@section('title', __('adminSafeMode'))
@section('page-title', __('adminSafeMode'))
@section('content')
<div class="pending">
<strong>{{ __('adminPendingTitle') }}</strong>
{{ __('adminPendingBody') }}
</div>
<div class="card">
<h2>{{ __('adminSafeModeStatus') }}</h2>
<div class="empty">
<div class="ic">&#128680;</div>
<h3>{{ __('adminSafeModeOff') }}</h3>
<p>{{ __('adminSafeModeIntro') }}</p>
<button class="btn primary" onclick="adminToast('{{ __('adminPendingBody') }}')">{{ __('adminEnableSafeMode') }}</button>
</div>
</div>
@endsection
