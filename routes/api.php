<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PanganController;

// Debug endpoint harus didefinisikan sebelum route yang lebih umum
Route::get('/pangan/debug', [PanganController::class, 'debug']);
Route::get('/pangan', [PanganController::class, 'getData']);