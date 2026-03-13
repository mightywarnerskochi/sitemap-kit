<?php

use Illuminate\Support\Facades\Route;
use Dev1kochiCrypto\SitemapKit\Http\Controllers\SitemapController;

Route::prefix('admin/sitemap')->name('sitemap.')->group(function () {
    Route::get('/', [SitemapController::class, 'index'])->name('index');
    Route::get('/generate', [SitemapController::class, 'generate'])->name('generate');
    Route::get('/edit', [SitemapController::class, 'edit'])->name('edit');
    Route::post('/update', [SitemapController::class, 'update'])->name('update');
});
