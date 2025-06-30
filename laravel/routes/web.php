<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\WordPressController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CartController;

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

// Autenticação
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Dashboard (protegido)
Route::get('/dashboard', function () {
    if (!AuthController::isLoggedIn()) {
        return redirect('/login');
    }
    $user = AuthController::getCurrentUser();
    return view('dashboard', compact('user'));
})->name('dashboard');

// Produtos
Route::get('/products', [ProductController::class, 'index'])->name('products.index');
Route::get('/products/{slug}', [ProductController::class, 'show'])->name('products.show');

// Carrinho
Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
Route::post('/cart/add', [CartController::class, 'add'])->name('cart.add');
Route::post('/cart/update', [CartController::class, 'update'])->name('cart.update');
Route::delete('/cart/remove/{id}', [CartController::class, 'remove'])->name('cart.remove');
Route::post('/cart/clear', [CartController::class, 'clear'])->name('cart.clear');
Route::post('/cart/checkout', [CartController::class, 'checkout'])->name('cart.checkout');

// API Routes
Route::prefix('api')->name('api.')->group(function () {
    Route::get('/products', [ProductController::class, 'apiIndex'])->name('products.index');
    Route::get('/products/{id}', [ProductController::class, 'apiShow'])->name('products.show');
    Route::get('/cart', [CartController::class, 'apiIndex'])->name('cart.index');
    Route::post('/cart/add', [CartController::class, 'apiAdd'])->name('cart.add');
});

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

// Autenticação WordPress (legado)
Route::post('/wordpress/login', [WordPressController::class, 'processWordPressLogin'])->name('wordpress.login');
Route::post('/wordpress/logout', [WordPressController::class, 'processWordPressLogout'])->name('wordpress.logout');

// WooCommerce My Account - redireciona para WordPress
Route::get('/my-account', [WordPressController::class, 'myAccount'])->name('my-account');

// WooCommerce My Account endpoints - redireciona para WordPress
Route::get('/my-account/{endpoint}', function ($endpoint) {
    $wordpressUrl = \App\Models\WordPressSettings::getWordPressUrl();
    return redirect($wordpressUrl . '/my-account/' . $endpoint);
})->where('endpoint', '.*')->name('my-account.endpoint');

// API para My Account
Route::get('/api/my-account', [WordPressController::class, 'myAccountApi'])->name('my-account.api');

// PWA Routes
Route::prefix('pwa')->name('pwa.')->group(function () {
    Route::get('/status', [App\Http\Controllers\PWAController::class, 'status'])->name('status');
    Route::post('/subscribe', [App\Http\Controllers\PWAController::class, 'subscribe'])->name('subscribe');
    Route::post('/send-notification', [App\Http\Controllers\PWAController::class, 'sendNotification'])->name('send-notification');
    Route::get('/manifest', [App\Http\Controllers\PWAController::class, 'manifest'])->name('manifest');
    Route::post('/clear-cache', [App\Http\Controllers\PWAController::class, 'clearCache'])->name('clear-cache');
});
