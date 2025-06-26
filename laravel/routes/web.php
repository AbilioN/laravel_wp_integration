<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\WordPressController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Home page - mostra a mesma página inicial do WordPress
Route::get('/', [HomeController::class, 'index'])->name('home');

// WordPress Pages
Route::prefix('wordpress')->name('wordpress.')->group(function () {
    Route::get('/pages', [WordPressController::class, 'index'])->name('pages.index');
    
    // Página My Account específica (DEVE vir ANTES da rota genérica)
    Route::get('/pages/my-account', [WordPressController::class, 'showMyAccountPage'])->name('pages.my-account');
    
    // Rota genérica para outras páginas (DEVE vir DEPOIS)
    Route::get('/pages/{slug}', [WordPressController::class, 'show'])->name('pages.show');
    
    Route::get('/posts', [WordPressController::class, 'posts'])->name('posts.index');
    Route::get('/search', [WordPressController::class, 'search'])->name('search');
});

// WooCommerce My Account - redireciona para WordPress
Route::get('/my-account', [WordPressController::class, 'myAccount'])->name('my-account');

// WooCommerce My Account endpoints - redireciona para WordPress
Route::get('/my-account/{endpoint}', function ($endpoint) {
    $wordpressUrl = \App\Models\WordPressSettings::getWordPressUrl();
    return redirect($wordpressUrl . '/my-account/' . $endpoint);
})->where('endpoint', '.*')->name('my-account.endpoint');

// API para My Account
Route::get('/api/my-account', [WordPressController::class, 'myAccountApi'])->name('my-account.api');
