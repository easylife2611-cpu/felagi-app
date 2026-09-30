@extends('layouts.admin')
@section('title', __('adminSponsoredAds'))
@section('page-title', __('adminSponsoredAds'))
@section('content')
<div class="pending">
<strong>{{ __('adminPendingTitle') }}</strong>
{{ __('adminPendingBody') }}
</div>
<div class="card">
<h2>{{ __('adminAdSlots') }}</h2>
<div class="empty">
<div class="ic">&#128226;</div>
<h3>{{ __('adminNoSponsoredAds') }}</h3>
<p>{{ __('adminSponsoredAdsIntro') }}</p>
<button class="btn primary" onclick="adminToast('{{ __('adminPendingBody') }}')">{{ __('adminCreateSponsoredAd') }}</button>
</div>
</div>
@endsection
