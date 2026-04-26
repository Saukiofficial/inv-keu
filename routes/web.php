<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LaporanController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/laporan/keuangan/cetak', [LaporanController::class, 'cetak'])
    ->name('laporan.keuangan.cetak');