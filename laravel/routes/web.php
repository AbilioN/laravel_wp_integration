<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\WordPressController;

Route::get('/', function () {
    return view('welcome');
});

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
    });
});
