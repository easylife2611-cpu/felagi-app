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
