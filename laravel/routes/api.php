<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\WordPressApiController;
use App\Http\Controllers\Api\WooCommerceApiController;
use App\Http\Controllers\Api\AuthApiController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Public API Routes
Route::prefix('v1')->group(function () {
    
    // WordPress Content API
    Route::prefix('wordpress')->group(function () {
        Route::get('/pages', [WordPressApiController::class, 'pages']);
        Route::get('/pages/{slug}', [WordPressApiController::class, 'page']);
        Route::get('/posts', [WordPressApiController::class, 'posts']);
        Route::get('/posts/{slug}', [WordPressApiController::class, 'post']);
        Route::get('/search', [WordPressApiController::class, 'search']);
        Route::get('/menu/{location}', [WordPressApiController::class, 'menu']);
        Route::get('/categories', [WordPressApiController::class, 'categories']);
        Route::get('/tags', [WordPressApiController::class, 'tags']);
    });

    // WooCommerce API
    Route::prefix('woocommerce')->group(function () {
        // Products
        Route::get('/products', [WooCommerceApiController::class, 'products']);
        Route::get('/products/{id}', [WooCommerceApiController::class, 'product']);
        Route::get('/products/category/{category}', [WooCommerceApiController::class, 'productsByCategory']);
        Route::get('/products/search', [WooCommerceApiController::class, 'searchProducts']);
        
        // Categories
        Route::get('/categories', [WooCommerceApiController::class, 'categories']);
        Route::get('/categories/{id}', [WooCommerceApiController::class, 'category']);
        
        // Orders (require authentication)
        Route::middleware('auth:sanctum')->group(function () {
            Route::get('/orders', [WooCommerceApiController::class, 'orders']);
            Route::get('/orders/{id}', [WooCommerceApiController::class, 'order']);
            Route::post('/orders', [WooCommerceApiController::class, 'createOrder']);
            Route::put('/orders/{id}', [WooCommerceApiController::class, 'updateOrder']);
        });
        
        // Cart (session based)
        Route::get('/cart', [WooCommerceApiController::class, 'getCart']);
        Route::post('/cart/add', [WooCommerceApiController::class, 'addToCart']);
        Route::put('/cart/update', [WooCommerceApiController::class, 'updateCart']);
        Route::delete('/cart/remove/{item_key}', [WooCommerceApiController::class, 'removeFromCart']);
        Route::post('/cart/clear', [WooCommerceApiController::class, 'clearCart']);
        
        // Checkout
        Route::post('/checkout', [WooCommerceApiController::class, 'checkout']);
        Route::get('/checkout/validate', [WooCommerceApiController::class, 'validateCheckout']);
    });

    // Authentication
    Route::prefix('auth')->group(function () {
        Route::post('/login', [AuthApiController::class, 'login']);
        Route::post('/register', [AuthApiController::class, 'register']);
        Route::post('/logout', [AuthApiController::class, 'logout'])->middleware('auth:sanctum');
        Route::get('/user', [AuthApiController::class, 'user'])->middleware('auth:sanctum');
        Route::post('/refresh', [AuthApiController::class, 'refresh'])->middleware('auth:sanctum');
    });

    // User Profile (require authentication)
    Route::middleware('auth:sanctum')->prefix('user')->group(function () {
        Route::get('/profile', [AuthApiController::class, 'profile']);
        Route::put('/profile', [AuthApiController::class, 'updateProfile']);
        Route::get('/orders', [WooCommerceApiController::class, 'userOrders']);
        Route::get('/addresses', [WooCommerceApiController::class, 'userAddresses']);
        Route::post('/addresses', [WooCommerceApiController::class, 'createAddress']);
        Route::put('/addresses/{id}', [WooCommerceApiController::class, 'updateAddress']);
        Route::delete('/addresses/{id}', [WooCommerceApiController::class, 'deleteAddress']);
    });
});

// Webhook endpoints for WordPress/WooCommerce
Route::prefix('webhooks')->group(function () {
    Route::post('/wordpress/post-updated', [WordPressApiController::class, 'handlePostUpdate']);
    Route::post('/woocommerce/order-created', [WooCommerceApiController::class, 'handleOrderCreated']);
    Route::post('/woocommerce/order-updated', [WooCommerceApiController::class, 'handleOrderUpdated']);
    Route::post('/woocommerce/product-updated', [WooCommerceApiController::class, 'handleProductUpdate']);
}); 