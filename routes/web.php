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
