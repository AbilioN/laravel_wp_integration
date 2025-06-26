<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\WordPressController;
use App\Http\Controllers\HomeController;

// Página inicial - mostra o conteúdo do WordPress
Route::get('/', [HomeController::class, 'index'])->name('home');

// Rotas do WordPress
Route::prefix('wordpress')->name('wordpress.')->group(function () {
    // Páginas
    Route::get('/pages', [WordPressController::class, 'index'])->name('pages.index');
    Route::get('/pages/search', [WordPressController::class, 'search'])->name('pages.search');
    Route::get('/pages/{slug}', [WordPressController::class, 'show'])->name('pages.show');
    Route::get('/pages/id/{id}', [WordPressController::class, 'showById'])->name('pages.showById');
    
    // Posts
    Route::get('/posts', [WordPressController::class, 'posts'])->name('posts.index');
    
    // API
    Route::prefix('api')->name('api.')->group(function () {
        Route::get('/pages', [WordPressController::class, 'apiPages'])->name('pages');
        Route::get('/pages/{slug}', [WordPressController::class, 'apiPage'])->name('page');
        Route::get('/navbar', [WordPressController::class, 'apiNavbar'])->name('navbar');
        Route::post('/navbar/clear-cache', [WordPressController::class, 'clearNavbarCache'])->name('navbar.clear-cache');
        Route::get('/home', [HomeController::class, 'apiHome'])->name('home');
    });
});
