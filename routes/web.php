<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FacebookPageController;

Route::get('/', function () {
    return view('welcome');
})->name('welcome');

Route::post('/cadastrar-facebook', [FacebookPageController::class, 'store']);


Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');
});
