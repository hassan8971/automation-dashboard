<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\v1\ProductController;
use App\Http\Controllers\Api\v1\CategoryController;
use App\Http\Controllers\Api\v1\AuthController;
use App\Http\Controllers\Api\v1\ProductReviewController;
use App\Http\Controllers\Api\v1\user\UserPanelController;
use App\Http\Controllers\Api\v1\CheckoutController;
use App\Http\Controllers\Api\v1\MenuController;
use App\Http\Controllers\Api\v1\BlogController;
use App\Http\Controllers\Api\v1\CartController;
use App\Http\Controllers\Api\v1\SubscriptionController;
use App\Http\Controllers\Api\v1\AddonController;
use App\Http\Controllers\Api\v1\WalletController;
use App\Http\Controllers\Api\v1\RedeemCodeController;

/*
|--------------------------------------------------------------------------
| API Routes (Version 1)
|--------------------------------------------------------------------------
*/

Route::prefix('v1')->group(function () {


Route::get('/products', [ProductController::class, 'index']);
Route::get('/products/{id}', [ProductController::class, 'show']);

Route::get('/categories', [CategoryController::class, 'index']);
Route::get('/categories/{slug}', [CategoryController::class, 'show'])->where('slug', '.*');

Route::get('/menus', [MenuController::class, 'index']);

Route::get('blog/posts', [BlogController::class, 'index']);
Route::get('blog/posts/{slug}', [BlogController::class, 'show']);
Route::get('blog/categories', [BlogController::class, 'categories']);

Route::post('/cart/check-discount', [CartController::class, 'checkDiscount']);

Route::get('/subscriptions', [SubscriptionController::class, 'index']);

Route::get('/addons', [AddonController::class, 'index']); // List


// این مسیر پیش‌فرض لاراول برای احراز هویت با Sanctum است
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

    Route::post('login/send-otp', [AuthController::class, 'sendOtp']);
    Route::post('login/verify-otp', [AuthController::class, 'verifyOtp']);


Route::middleware('auth:sanctum')->group(function () {
    
    Route::get('/user', [UserPanelController::class, 'getUser']);
    Route::post('logout', [AuthController::class, 'logout']);

    Route::get('/user/orders', [UserPanelController::class, 'getOrders']);
    Route::get('/user/orders/{orderId}', [UserPanelController::class, 'getOrderDetails']);

    Route::post('/user/profile', [UserPanelController::class, 'updateProfile']);

    Route::get('/user/addresses', [UserPanelController::class, 'getAddresses']);
    Route::post('/user/addresses', [UserPanelController::class, 'storeAddress']);
    Route::put('/user/addresses/{addressId}', [UserPanelController::class, 'updateAddress']);
    Route::delete('/user/addresses/{addressId}', [UserPanelController::class, 'destroyAddress']);

    Route::post('products/{product}/reviews', [ProductReviewController::class, 'store']);

    Route::post('checkout', [CheckoutController::class, 'store']);

    Route::get('/subscriptions/current', [SubscriptionController::class, 'current']);
    
    // Buy a sub
    Route::post('/subscriptions/purchase', [SubscriptionController::class, 'purchase']);

    Route::get('/addons/my', [AddonController::class, 'myAddons']); // Check ownership
    Route::post('/addons/purchase', [AddonController::class, 'purchase']); // Buy

    Route::get('/wallet', [WalletController::class, 'index']); // Check Balance
    Route::get('/wallet/history', [WalletController::class, 'history']); // Transactions
    Route::post('/wallet/deposit', [WalletController::class, 'deposit']); // Add Money (Mock)

    Route::post('/wallet/redeem', [RedeemCodeController::class, 'redeem']);

    Route::post('/products/{id}/install', [ProductController::class, 'install']);

    Route::post('/products/{id}/buy', [ProductController::class, 'buy']);

});

});