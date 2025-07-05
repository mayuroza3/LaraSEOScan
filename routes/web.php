<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SeoScanController;

Route::get('/', [SeoScanController::class, 'index'])->name('scan.form');

Route::post('/scan', [SeoScanController::class, 'scan'])->name('scan.submit');

Route::get('/results/{id}', [SeoScanController::class, 'results'])->name('scan.results');