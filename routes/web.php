<?php

use Illuminate\Support\Facades\Route;
use MightyWarnersKochi\SitemapKit\Http\Controllers\MissingUrlLogController;
use MightyWarnersKochi\SitemapKit\Http\Controllers\SitemapController;
use MightyWarnersKochi\SitemapKit\Http\Controllers\UrlRedirectController;

Route::prefix('admin/sitemap')->name('sitemap.')->group(function () {
    Route::get('/', [SitemapController::class, 'index'])->name('index');
    Route::get('/generate', [SitemapController::class, 'generate'])->name('generate');
    Route::get('/edit', [SitemapController::class, 'edit'])->name('edit');
    Route::post('/update', [SitemapController::class, 'update'])->name('update');

    Route::prefix('redirects')->name('redirects.')->group(function () {
        Route::get('/', [UrlRedirectController::class, 'index'])->name('index');
        Route::post('/optimize-clear', [UrlRedirectController::class, 'optimizeClear'])->name('optimize-clear');
        Route::get('/create', [UrlRedirectController::class, 'create'])->name('create');
        Route::post('/', [UrlRedirectController::class, 'store'])->name('store');
        Route::get('/{redirect}/edit', [UrlRedirectController::class, 'edit'])->name('edit');
        Route::put('/{redirect}', [UrlRedirectController::class, 'update'])->name('update');
        Route::delete('/{redirect}', [UrlRedirectController::class, 'destroy'])->name('destroy');
    });

    Route::prefix('missing-urls')->name('missing-urls.')->group(function () {
        Route::get('/', [MissingUrlLogController::class, 'index'])->name('index');
        Route::post('/clear', [MissingUrlLogController::class, 'clear'])->name('clear');
        Route::get('/{missingUrlLog}/create-redirect', [MissingUrlLogController::class, 'promote'])->name('promote');
        Route::delete('/{missingUrlLog}', [MissingUrlLogController::class, 'destroy'])->name('destroy');
    });
});
