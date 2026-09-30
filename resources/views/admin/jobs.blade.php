@extends('layouts.admin')
@section('title', __('adminJobs'))
@section('page-title', __('adminJobs'))
@section('content')
<div class="pending">
<strong>{{ __('adminPendingTitle') }}</strong>
{{ __('adminPendingBody') }}
</div>
<div class="stat-grid">
<div class="stat info"><div class="lbl">{{ __('adminStatPending') }}</div><div class="val">—</div></div>
<div class="stat ok"><div class="lbl">{{ __('adminStatProcessed') }}</div><div class="val">—</div></div>
<div class="stat err"><div class="lbl">{{ __('adminStatFailed') }}</div><div class="val">—</div></div>
</div>
<div class="card">
<h2>{{ __('adminJobQueue') }}</h2>
<div class="empty">
<div class="ic">&#128337;</div>
<p>{{ __('adminJobsIntro') }}</p>
</div>
</div>
@endsection
