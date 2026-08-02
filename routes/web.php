<?php

use App\Http\Controllers\GoogleController;
use Illuminate\Support\Facades\Route;

Route::get('/auth/google', [GoogleController::class, 'redirect']);
Route::get('/auth/google/callback', [GoogleController::class, 'callback']);
Route::post('/logout', [GoogleController::class, 'logout']);

Route::get('{any?}', function () {
    return view('application');
})->where('any', '.*');
