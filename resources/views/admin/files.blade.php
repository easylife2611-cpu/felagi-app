@extends('layouts.admin')
@section('title', __('screenA011'))
@section('page-title', __('adminFiles'))
@section('content')
<div class="pending">
<strong>{{ __('adminPendingTitle') }}</strong>
{{ __('adminPendingBody') }}
</div>
<div class="card">
<h2>{{ __('adminFileManager') }}</h2>
<div class="empty">
<div class="ic">&#128193;</div>
<h3>{{ __('adminNoFiles') }}</h3>
<p>{{ __('adminFilesIntro') }}</p>
</div>
</div>
@endsection
