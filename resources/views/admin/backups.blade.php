@extends('layouts.admin')
@section('title', __('adminBackups'))
@section('page-title', __('adminBackups'))
@section('content')
<div class="pending">
<strong>{{ __('adminPendingTitle') }}</strong>
{{ __('adminPendingBody') }}
</div>
<div class="card">
<h2>{{ __('adminBackupList') }}</h2>
<div class="empty">
<div class="ic">&#128190;</div>
<h3>{{ __('adminNoBackups') }}</h3>
<p>{{ __('adminBackupsIntro') }}</p>
<button class="btn primary" onclick="adminToast('{{ __('adminPendingBody') }}')">{{ __('adminCreateBackup') }}</button>
</div>
</div>
@endsection
