<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SeoScanController;
use App\Http\Controllers\SeoLandingController;
use Illuminate\Support\Facades\Route;

Route::redirect('/dashboard', '/scan/history')->name('dashboard');

// Public Scan / Results Routes (accessible by guests and logged-in users)
Route::get('/scan', [SeoScanController::class, 'create'])->name('scan.create');
Route::post('/scan', [SeoScanController::class, 'scan'])->middleware('throttle:10,1')->name('scan.submit');
Route::get('/results/{uuid}', [SeoScanController::class, 'results'])->name('scan.results');

// Auth Protected Routes
Route::middleware('auth')->group(function () {
    Route::get('/scan/history', [SeoScanController::class, 'history'])->name('scan.history');
    Route::delete('/scan/{uuid}', [SeoScanController::class, 'destroy'])->name('scan.delete');
    Route::get('/scan/{uuid}/status', [SeoScanController::class, 'status'])->name('scan.status');

    Route::get('/scan/{uuid}/export/pdf', [SeoScanController::class, 'exportPdf'])->name('scan.export.pdf');
    Route::get('/scan/{uuid}/export/csv', [SeoScanController::class, 'exportCsv'])->name('scan.export.csv');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Focused SEO Landing Page Routes
Route::get('/seo-tools', [SeoLandingController::class, 'hub'])->name('landing.hub');
Route::get('/seo-checker', [SeoLandingController::class, 'show'])->defaults('tool', 'seo-checker')->name('landing.seo-checker');
Route::get('/free-seo-checker', [SeoLandingController::class, 'show'])->defaults('tool', 'free-seo-checker')->name('landing.free-seo-checker');
Route::get('/website-seo-checker', [SeoLandingController::class, 'show'])->defaults('tool', 'website-seo-checker')->name('landing.website-seo-checker');
Route::get('/meta-tag-checker', [SeoLandingController::class, 'show'])->defaults('tool', 'meta-tag-checker')->name('landing.meta-tag-checker');
Route::get('/meta-description-checker', [SeoLandingController::class, 'show'])->defaults('tool', 'meta-description-checker')->name('landing.meta-description-checker');
Route::get('/title-tag-checker', [SeoLandingController::class, 'show'])->defaults('tool', 'title-tag-checker')->name('landing.title-tag-checker');
Route::get('/h1-checker', [SeoLandingController::class, 'show'])->defaults('tool', 'h1-checker')->name('landing.h1-checker');
Route::get('/broken-link-checker', [SeoLandingController::class, 'show'])->defaults('tool', 'broken-link-checker')->name('landing.broken-link-checker');
Route::get('/robots-txt-checker', [SeoLandingController::class, 'show'])->defaults('tool', 'robots-txt-checker')->name('landing.robots-txt-checker');
Route::get('/sitemap-checker', [SeoLandingController::class, 'show'])->defaults('tool', 'sitemap-checker')->name('landing.sitemap-checker');
Route::get('/schema-markup-checker', [SeoLandingController::class, 'show'])->defaults('tool', 'schema-markup-checker')->name('landing.schema-markup-checker');
Route::get('/open-graph-checker', [SeoLandingController::class, 'show'])->defaults('tool', 'open-graph-checker')->name('landing.open-graph-checker');
Route::get('/image-seo-checker', [SeoLandingController::class, 'show'])->defaults('tool', 'image-seo-checker')->name('landing.image-seo-checker');

// Legal Routes
Route::view('/legal', 'legal.index')->name('legal.index');
Route::view('/privacy-policy', 'legal.privacy')->name('legal.privacy');
Route::view('/terms-of-service', 'legal.terms')->name('legal.terms');
Route::view('/cookie-policy', 'legal.cookies')->name('legal.cookies');

Route::get('/', fn () => view('welcome'))->name('home');
require __DIR__.'/auth.php';
