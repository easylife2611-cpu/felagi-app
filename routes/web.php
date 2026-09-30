<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/profile', function () {
    return view('profile');
});

Route::get('/browse', function () {
    return view('browse');
});

Route::get('/needs/new', function () {
    return view('create-need');
});

Route::get('/needs/{id}', function ($id) {
    return view('show-need');
});

Route::get('/my/needs', function () {
    return view('my-needs');
});

Route::get('/needs/{id}/offers/new', function ($id) {
    return view('submit-offer');
});

Route::get('/needs/{id}/offers', function ($id) {
    return view('received-offers');
});

Route::get('/offers/{id}', function ($id) {
    return view('offer-detail');
});

Route::get('/my/offers', function () {
    return view('my-offers');
});

Route::get('/notifications', function () {
    return view('notifications');
});

Route::get('/offers/{id}/messages', function ($id) {
    return view('offer-messages');
});

Route::get('/needs/{id}/compare', function ($id) {
    return view('compare-offers');
});

Route::get('/needs/{id}/created', function ($id) {
    return view('need-created');
});

Route::get('/needs/new/public-preview', function () {
    return view('need-preview');
});

Route::get('/needs/{id}/rating', function ($id) {
    return view('rate-participant');
});

Route::get('/comparisons/{id}', function ($id) {
    return view('comparison-result');
});

Route::get('/needs/{id}/comparisons', function ($id) {
    return view('comparison-history');
});

Route::get('/needs/{id}/boost', function ($id) {
    return view('boost-need');
});

Route::get('/support/report', function () {
    return view('report-support');
});

Route::get('/needs/{id}/publications', function ($id) {
    return view('telegram-publications');
});

Route::get('/needs/{id}/offers/unlock', function ($id) {
    return view('offer-unlock');
});

// ─── Admin Routes (A001-A023) ───
Route::prefix('admin')->group(function () {
    Route::get('/dashboard',                  fn() => view('admin.dashboard'));
    Route::get('/telegram',                   fn() => view('admin.telegram'));
    Route::get('/health',                     fn() => view('admin.health'));
    Route::get('/features',                   fn() => view('admin.features'));
    Route::get('/marketplace',                fn() => view('admin.marketplace'));
    Route::get('/ai',                         fn() => view('admin.ai'));
    Route::get('/payments',                   fn() => view('admin.payments'));
    Route::get('/users',                      fn() => view('admin.users'));
    Route::get('/content',                    fn() => view('admin.content'));
    Route::get('/notifications',              fn() => view('admin.notifications'));
    Route::get('/files',                      fn() => view('admin.files'));
    Route::get('/jobs',                       fn() => view('admin.jobs'));
    Route::get('/backups',                    fn() => view('admin.backups'));
    Route::get('/integrity',                  fn() => view('admin.integrity'));
    Route::get('/security',                   fn() => view('admin.security'));
    Route::get('/audit',                      fn() => view('admin.audit'));
    Route::get('/settings',                   fn() => view('admin.settings'));
    Route::get('/recovery',                   fn() => view('admin.recovery'));
    Route::get('/safe-mode',                  fn() => view('admin.safe-mode'));
    Route::get('/monetization',               fn() => view('admin.monetization'));
    Route::get('/maintenance',                fn() => view('admin.maintenance'));
    Route::get('/reports',                    fn() => view('admin.reports'));
    Route::get('/monetization/sponsored-ads', fn() => view('admin.sponsored-ads'));
});
