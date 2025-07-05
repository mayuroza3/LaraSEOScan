<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SeoScanController;
use Illuminate\Support\Facades\Route;

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {


    Route::get('/scan', [SeoScanController::class, 'index'])->name('scan.form');
    Route::post('/scan', [SeoScanController::class, 'scan'])->name('scan.submit');
    Route::get('/results/{id}', [SeoScanController::class, 'results'])->name('scan.results');
    Route::get('/scan/history', [SeoScanController::class, 'history'])->name('scan.history');
    Route::delete('/scan/{id}', [SeoScanController::class, 'destroy'])->name('scan.delete');

    Route::get('/scan/{id}/export/pdf', [SeoScanController::class, 'exportPdf'])->name('scan.export.pdf');
    Route::get('/scan/{id}/export/csv', [SeoScanController::class, 'exportCsv'])->name('scan.export.csv');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
