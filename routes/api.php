<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PanganController;
use App\Http\Controllers\SphereController;

// Debug endpoint harus didefinisikan sebelum route yang lebih umum
Route::get('/pangan/debug', [PanganController::class, 'debug']);
Route::get('/pangan', [PanganController::class, 'getData']);

// Sphere API routes
Route::post('/sphere/plus/{id}', [SphereController::class, 'plus']);