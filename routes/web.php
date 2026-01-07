<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\Auth\LoginController as AdminLoginController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\PackagingOptionController;
use App\Http\Controllers\Admin\DiscountController;
use App\Http\Controllers\Admin\SizeController;
use App\Http\Controllers\Admin\ColorController;
use App\Http\Controllers\Admin\BuySourceController;
use App\Http\Controllers\Admin\MenuItemController;
use App\Http\Controllers\Admin\BlogCategoryController;
use App\Http\Controllers\Admin\PostController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\ProductVariantController;
use App\Http\Controllers\Admin\ProductImageController;
use App\Http\Controllers\Admin\VideoController;
use App\Http\Controllers\Admin\AdminChatController;
use App\Http\Controllers\Admin\TodoController;
use App\Http\Controllers\Admin\SubscriptionController;
use App\Http\Controllers\Admin\GiftController;
use App\Http\Controllers\Admin\AddonController;
use App\Http\Controllers\Admin\RedeemCodeController;
use App\Http\Controllers\Admin\WalletController;
use App\Http\Controllers\Admin\AppLayoutController;
use App\Http\Controllers\Admin\AppTabController;
use App\Http\Controllers\Auth\LoginController as UserLoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\OtpLoginController;
use App\Http\Controllers\CheckoutController;

Route::get('/', function () {
    return "access denied";
})->name('home');


Route::prefix('checkout')->name('checkout.')->group(function () {
    Route::get('/success/{order}', [CheckoutController::class, 'success'])->name('success');
});

// Admin Login
Route::post('admin/logout', [AdminLoginController::class, 'logout'])->name('admin.logout');

// Admin Dashboard (Protected)
Route::prefix('admin')->name('admin.')->group(function () {
    Route::middleware(['auth:admin'])->group(function () {
        Route::get('dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
        Route::resource('categories', CategoryController::class)->except(['show']);
        Route::resource('products', ProductController::class)->except(['show']);


        Route::post('products/{product}/variants', [ProductVariantController::class, 'store'])
            ->name('products.variants.store');

        // GET /admin/variants/{variant}/edit
        // (Matches the 'edit' link on the product page)
        Route::get('variants/{variant}/edit', [ProductVariantController::class, 'edit'])
            ->name('variants.edit');
        
        // PUT /admin/variants/{variant}
        // (Matches the form action in the edit.blade.php file)
        Route::put('variants/{variant}', [ProductVariantController::class, 'update'])
            ->name('variants.update');

        // DELETE /admin/variants/{variant}
        // (Matches the 'delete' form on the product page)
        Route::delete('variants/{variant}', [ProductVariantController::class, 'destroy'])
            ->name('variants.destroy');
        
            // POST /admin/products/{product}/images
        // (This uploads images *for* a specific product)
        Route::post('products/{product}/images', [ProductImageController::class, 'store'])
            ->name('products.images.store');
        
        // DELETE /admin/images/{image}
        // (Deletes a specific image by its ID)
        Route::delete('images/{image}', [ProductImageController::class, 'destroy'])
            ->name('images.destroy');

        Route::resource('videos', VideoController::class)->except(['show']);
        Route::resource('sizes', SizeController::class)->except(['show']);
        Route::resource('colors', ColorController::class)->except(['show']);
        Route::resource('buy-sources', BuySourceController::class)->except(['show']);
        Route::resource('menu-items', MenuItemController::class)->except(['show']);
        Route::resource('users', UserController::class)->only(['index', 'show']);

        Route::get('orders', [OrderController::class, 'index'])->name('orders.index');
        Route::get('orders/{order}', [OrderController::class, 'show'])->name('orders.show');
        Route::put('orders/{order}', [OrderController::class, 'update'])->name('orders.update');

        Route::resource('packaging-options', PackagingOptionController::class)->except(['show']);
        Route::resource('discounts', DiscountController::class)->except(['show']);

        Route::resource('blog-categories', BlogCategoryController::class)->except(['show']);
        Route::resource('posts', PostController::class);

        Route::get('/chat/messages', [AdminChatController::class, 'fetchMessages'])->name('chat.fetch');
        Route::post('/chat/send', [AdminChatController::class, 'sendMessage'])->name('chat.send');

        Route::get('/chat/users', [AdminChatController::class, 'fetchUsers'])->name('chat.users');
        Route::post('/chat/status', [AdminChatController::class, 'toggleStatus'])->name('chat.status');

        Route::get('/todos', [TodoController::class, 'index'])->name('todos.index');
        Route::post('/todos', [TodoController::class, 'store'])->name('todos.store');
        Route::patch('/todos/{todo}', [TodoController::class, 'toggle'])->name('todos.toggle');
        Route::delete('/todos/{todo}', [TodoController::class, 'destroy'])->name('todos.destroy');
        Route::get('/todos/check-urgent', [TodoController::class, 'checkUrgent'])->name('todos.checkUrgent');

        Route::get('/profile', [App\Http\Controllers\Admin\ProfileController::class, 'edit'])->name('profile.edit');
        Route::put('/profile', [App\Http\Controllers\Admin\ProfileController::class, 'update'])->name('profile.update');

        Route::resource('subscriptions', SubscriptionController::class);
        Route::resource('gifts', GiftController::class);
        Route::resource('addons', AddonController::class);
        Route::resource('redeem-codes', RedeemCodeController::class);

        Route::post('products/fetch-itunes', [ProductController::class, 'fetchItunes'])->name('products.fetch-itunes');

        Route::get('wallets', [WalletController::class, 'index'])->name('wallets.index');
        Route::get('wallets/{wallet}', [WalletController::class, 'show'])->name('wallets.show');
        Route::post('wallets/{wallet}/update-balance', [WalletController::class, 'updateBalance'])->name('wallets.update-balance');

        // لیست تمام صفحات (تب‌ها)
        Route::get('layouts', [AppLayoutController::class, 'index'])->name('layouts.index');
        
        // صفحه ساز (Drag & Drop Builder) برای یک صفحه خاص
        Route::get('layouts/{page}/builder', [AppLayoutController::class, 'edit'])->name('layouts.builder');
        
        // ذخیره یک سکشن (بخش) جدید
        Route::post('layouts/{page}/sections', [AppLayoutController::class, 'storeSection'])->name('layouts.section.store');
        
        // تغییر ترتیب سکشن‌ها (AJAX)
        Route::post('layouts/reorder', [AppLayoutController::class, 'reorder'])->name('layouts.reorder');
        
        // حذف یک سکشن
        Route::delete('layouts/sections/{section}', [AppLayoutController::class, 'destroySection'])->name('layouts.section.delete');

        Route::get('layouts/create', [AppLayoutController::class, 'create'])->name('layouts.create');
        Route::post('layouts', [AppLayoutController::class, 'store'])->name('layouts.store');
        Route::post('layouts/{page}/save-all', [AppLayoutController::class, 'saveAll'])->name('layouts.save_all');
        Route::post('layouts/upload-image', [AppLayoutController::class, 'uploadImage'])->name('layouts.upload_image');
        Route::resource('app-tabs', AppTabController::class);
    });
});

Route::post('logout', [UserLoginController::class, 'logout'])->name('logout');

// --- User Auth Routes (OTP Passwordless) ---
Route::middleware('guest')->group(function () {
    // Step 1: Show mobile number form
    Route::get('login', [OtpLoginController::class, 'showLoginForm'])->name('login');
    // Step 1: Handle mobile number submission
    Route::post('login', [OtpLoginController::class, 'sendOtp'])->name('otp.send');
    
    // Step 2: Show verification code form
    Route::get('login/verify', [OtpLoginController::class, 'showVerifyForm'])->name('otp.verify.form');
    // Step 2: Handle code submission
    Route::post('login/verify', [OtpLoginController::class, 'verifyOtp'])->name('otp.verify');
});