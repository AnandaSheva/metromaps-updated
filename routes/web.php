<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MapController;
use App\Http\Controllers\CCTVController;
use App\Http\Controllers\SphereController;
use App\Http\Controllers\PanganController;
use App\Http\Controllers\WelcomeController;

Route::get('/', [WelcomeController::class, 'index'])->name('welcome');
Route::post('/set-session', [WelcomeController::class, 'setSession'])->name('welcome.setSession');
Route::get('/clear-session', function() {
    session()->forget('visited_welcome');
    return redirect('/');
})->name('clear.session');

Route::get('/map', [MapController::class, 'index'])->name('map.index');
Route::post('/cctv/view/{id}', [CCTVController::class, 'view'])->name('cctv.view');
Route::get('/sphere/view/{slug}', [SphereController::class, 'view']);
Route::get('/sphere/plus/{id}', [SphereController::class, 'plus']);
Route::get('/api/pangan', [PanganController::class, 'getData']);