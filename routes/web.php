<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SeoScanController;
use Illuminate\Support\Facades\Route;

Route::redirect('/dashboard', '/scan/history')->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/scan', [SeoScanController::class, 'create'])->name('scan.create');
    Route::post('/scan', [SeoScanController::class, 'scan'])->name('scan.submit');
    Route::get('/results/{uuid}', [SeoScanController::class, 'results'])->name('scan.results');
    Route::get('/scan/history', [SeoScanController::class, 'history'])->name('scan.history');
    Route::delete('/scan/{uuid}', [SeoScanController::class, 'destroy'])->name('scan.delete');
    Route::get('/scan/{uuid}/status', [SeoScanController::class, 'status'])->name('scan.status');

    Route::get('/scan/{uuid}/export/pdf', [SeoScanController::class, 'exportPdf'])->name('scan.export.pdf');
    Route::get('/scan/{uuid}/export/csv', [SeoScanController::class, 'exportCsv'])->name('scan.export.csv');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::get('/', fn () => view('welcome'))->name('home');
require __DIR__.'/auth.php';
