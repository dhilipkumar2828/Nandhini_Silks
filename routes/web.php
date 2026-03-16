<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AdminAuthController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\SubCategoryController;
use App\Http\Controllers\Admin\ChildCategoryController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\FrontendController;
use App\Http\Controllers\Auth\UserAuthController;

Route::get('/', [FrontendController::class, 'index'])->name('home');

// User Authentication
Route::post('/login', [UserAuthController::class, 'login'])->name('login.submit');
Route::post('/register', [UserAuthController::class, 'register'])->name('register');
Route::post('/logout', [UserAuthController::class, 'logout'])->name('logout');

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
Route::post('/login', [FrontendController::class, 'postLogin'])->name('login.submit');
Route::post('/register', [FrontendController::class, 'postRegister'])->name('register');
Route::get('/my-account', [FrontendController::class, 'myAccount'])->name('my-account');
Route::get('/my-addresses', [FrontendController::class, 'myAddresses'])->name('my-addresses');
Route::get('/my-reviews', [FrontendController::class, 'myReviews'])->name('my-reviews');
Route::get('/my-profile', [FrontendController::class, 'myProfile'])->name('my-profile');
Route::get('/order-confirmation', [FrontendController::class, 'orderConfirmation'])->name('order-confirmation');
Route::get('/order-detail', [FrontendController::class, 'orderDetail'])->name('order-detail');

// Dynamic Products/Categories
Route::get('/category/{slug}', [FrontendController::class, 'category'])->name('category.show');
Route::get('/product/{slug}', [FrontendController::class, 'productShow'])->name('product.show');

// Special Category Routes (to match existing links if needed)
Route::get('/sarees', function () {
    return redirect()->route('category.show', 'sarees'); });
Route::get('/women', function () {
    return redirect()->route('category.show', 'women'); });
Route::get('/mens', function () {
    return redirect()->route('category.show', 'mens'); });
Route::get('/kids', function () {
    return redirect()->route('category.show', 'kids'); });

// Admin Routes
Route::group(['prefix' => 'admin'], function () {
    // Guest Routes
    Route::group(['middleware' => 'guest:admin'], function () {
        Route::get('/login', [AdminAuthController::class, 'showLoginForm'])->name('admin.login');
        Route::post('/login', [AdminAuthController::class, 'login'])->name('admin.login.post');
    });

    // Authenticated Routes
    Route::group(['middleware' => 'auth:admin'], function () {
        Route::get('/', function () {
            return redirect()->route('admin.dashboard');
        });
        Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('admin.dashboard');
        Route::post('/logout', [AdminAuthController::class, 'logout'])->name('admin.logout');

        // Category Management
        Route::resource('categories', CategoryController::class)->names('admin.categories');

        // Orders
        Route::resource('sub-categories', SubCategoryController::class)->names('admin.sub-categories');
        Route::resource('child-categories', ChildCategoryController::class)->names('admin.child-categories');

        // Attributes
        Route::resource('attributes', \App\Http\Controllers\Admin\AttributeController::class)->names('admin.attributes');
        Route::resource('attribute-values', \App\Http\Controllers\Admin\AttributeValueController::class)->names('admin.attribute-values');

        // Appearance
        Route::resource('banners', \App\Http\Controllers\Admin\BannerController::class)->names('admin.banners');
        Route::resource('ads', \App\Http\Controllers\Admin\AdController::class)->names('admin.ads');
        Route::resource('testimonials', \App\Http\Controllers\Admin\TestimonialController::class)->names('admin.testimonials');

        // Tax Settings
        Route::resource('tax-settings', \App\Http\Controllers\Admin\TaxSettingController::class)->names('admin.tax-settings');
        Route::resource('tax-classes', \App\Http\Controllers\Admin\TaxClassController::class)->names('admin.tax-classes');
        Route::resource('tax-rates', \App\Http\Controllers\Admin\TaxRateController::class)->names('admin.tax-rates');

        // Stock Maintenance
        Route::get('/stock', [\App\Http\Controllers\Admin\StockController::class, 'index'])->name('admin.stock.index');
        Route::post('/stock/update-bulk', [\App\Http\Controllers\Admin\StockController::class, 'updateBulk'])->name('admin.stock.update-bulk');
        Route::get('/stock/{product}/logs', [\App\Http\Controllers\Admin\StockController::class, 'showLogs'])->name('admin.stock.logs');

        // Products
        Route::resource('products', ProductController::class)->names('admin.products');

        // AJAX Helpers
        Route::get('/get-sub-categories/{category_id}', [ChildCategoryController::class, 'getSubCategories']);
        Route::get('/get-child-categories/{sub_category_id}', [ProductController::class, 'getChildCategories']);
    });
});
