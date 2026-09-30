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
