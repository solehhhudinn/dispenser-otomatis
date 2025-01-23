<?php

use App\Http\Controllers\WaterLevelController;
use Illuminate\Support\Facades\Route;

// Halaman utama
Route::get('/', function () {
    return view('welcome');
});

// Rute untuk memperbarui status pompa
Route::get('/api/update-status', [WaterLevelController::class, 'updateStatus'])->name('api.updateStatus');

// Menampilkan status pompa
Route::get('/pump-status', [WaterLevelController::class, 'showPumpStatus'])->name('pump.status');


