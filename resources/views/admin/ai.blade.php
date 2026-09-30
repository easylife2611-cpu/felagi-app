@extends('layouts.admin')
@section('title', __('adminAI'))
@section('page-title', __('adminAI'))
@section('content')
<div class="pending">
<strong>{{ __('adminPendingTitle') }}</strong>
{{ __('adminPendingBody') }}
</div>
<div class="card">
<h2>{{ __('adminAIConfig') }}</h2>
<div class="empty">
<div class="ic">&#129302;</div>
<h3>{{ __('adminAINotConfigured') }}</h3>
<p>{{ __('adminAIProvider') }}</p>
<button class="btn primary" onclick="adminToast('{{ __('adminPendingBody') }}')">{{ __('adminConfigureProvider') }}</button>
</div>
</div>
<div class="card">
<h2>{{ __('adminAIRecentComparisons') }}</h2>
<div class="empty">
<div class="ic">&#128202;</div>
<p>{{ __('adminNoComparisons') }}</p>
</div>
</div>
@endsection
