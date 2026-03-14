<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AdminAuthController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\CategoryController;
<<<<<<< HEAD
use App\Http\Controllers\Admin\OrderController;
=======
use App\Http\Controllers\Admin\SubCategoryController;
use App\Http\Controllers\Admin\ChildCategoryController;
use App\Http\Controllers\Admin\ProductController;
>>>>>>> 568a5524ccccd0390f1bf0c6b8855268daf336c5
use App\Http\Controllers\FrontendController;

Route::get('/', [FrontendController::class, 'index'])->name('home');

// Frontend Static Pages
Route::get('/about', [FrontendController::class, 'about'])->name('about');
Route::get('/contact', [FrontendController::class, 'contact'])->name('contact');
Route::get('/cart', [FrontendController::class, 'cart'])->name('cart');
Route::get('/checkout', [FrontendController::class, 'checkout'])->name('checkout');
Route::get('/wishlist', [FrontendController::class, 'wishlist'])->name('wishlist');
Route::get('/search', [FrontendController::class, 'search'])->name('search');

// Policy Pages
Route::get('/privacy-policy', [FrontendController::class, 'privacyPolicy'])->name('privacy-policy');
Route::get('/terms-conditions', [FrontendController::class, 'termsConditions'])->name('terms');
Route::get('/cancellation', [FrontendController::class, 'cancellation'])->name('cancellation');
Route::get('/exchange-policy', [FrontendController::class, 'exchangePolicy'])->name('exchange-policy');
Route::get('/shipping-policy', [FrontendController::class, 'shippingPolicy'])->name('shipping-policy');
Route::get('/fabric-care', [FrontendController::class, 'fabricCare'])->name('fabric-care');

// User Account Pages
Route::get('/login', [FrontendController::class, 'userLogin'])->name('login');
Route::post('/login', [FrontendController::class, 'userLogin'])->name('login.submit');
Route::post('/register', [FrontendController::class, 'userLogin'])->name('register');
Route::get('/my-account', [FrontendController::class, 'myAccount'])->name('my-account');
Route::get('/my-addresses', [FrontendController::class, 'myAddresses'])->name('my-addresses');
Route::get('/my-orders', [FrontendController::class, 'myOrders'])->name('my-orders');
Route::get('/my-profile', [FrontendController::class, 'myProfile'])->name('my-profile');
Route::get('/my-reviews', [FrontendController::class, 'myReviews'])->name('my-reviews');
Route::get('/order-confirmation', [FrontendController::class, 'orderConfirmation'])->name('order-confirmation');
Route::get('/order-detail', [FrontendController::class, 'orderDetail'])->name('order-detail');

// Dynamic Products/Categories
Route::get('/category/{slug}', [FrontendController::class, 'category'])->name('category.show');
Route::get('/product/{slug}', [FrontendController::class, 'productShow'])->name('product.show');

// Special Category Routes (to match existing links if needed)
Route::get('/sarees', function() { return redirect()->route('category.show', 'sarees'); });
Route::get('/women', function() { return redirect()->route('category.show', 'women'); });
Route::get('/mens', function() { return redirect()->route('category.show', 'mens'); });
Route::get('/kids', function() { return redirect()->route('category.show', 'kids'); });

// Admin Routes
Route::group(['prefix' => 'admin'], function () {
    // Guest Routes
    Route::group(['middleware' => 'guest:admin'], function () {
        Route::get('/login', [AdminAuthController::class, 'showLoginForm'])->name('admin.login');
        Route::post('/login', [AdminAuthController::class, 'login'])->name('admin.login.post');
    });

    // Authenticated Routes
    Route::group(['middleware' => 'auth:admin'], function () {
        Route::get('/', function() {
            return redirect()->route('admin.dashboard');
        });
        Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('admin.dashboard');
        Route::post('/logout', [AdminAuthController::class, 'logout'])->name('admin.logout');
        
        // Category Management
        Route::resource('categories', CategoryController::class)->names('admin.categories');

        // Orders
        Route::get('/orders/{order}/invoice', [OrderController::class, 'downloadInvoice'])->name('admin.orders.invoice');
        Route::resource('orders', OrderController::class)->names('admin.orders');
        Route::resource('sub-categories', SubCategoryController::class)->names('admin.sub-categories');
        Route::resource('child-categories', ChildCategoryController::class)->names('admin.child-categories');
        
        // Products
        Route::resource('products', ProductController::class)->names('admin.products');
        
        // AJAX Helpers
        Route::get('/get-sub-categories/{category_id}', [ChildCategoryController::class, 'getSubCategories']);
        Route::get('/get-child-categories/{sub_category_id}', [ProductController::class, 'getChildCategories']);
    });
});
