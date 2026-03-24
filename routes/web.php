<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AdminAuthController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\SubCategoryController;
use App\Http\Controllers\Admin\ChildCategoryController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\FrontendController;
use App\Http\Controllers\Auth\UserAuthController;
use App\Http\Controllers\Admin\AdminProfileController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\UserAddressController;
use App\Http\Controllers\Admin\OrderController;

Route::post('/addresses', [UserAddressController::class, 'store'])->name('addresses.store');

Route::get('/', [FrontendController::class, 'index'])->name('home');
Route::get('/shop', [FrontendController::class, 'shop'])->name('shop');

// User Authentication
Route::post('/login', [UserAuthController::class, 'login'])->name('login.submit');
Route::post('/register', [UserAuthController::class, 'register'])->name('register');
Route::post('/logout', [UserAuthController::class, 'logout'])->name('logout');

use App\Http\Controllers\WishlistController;

// Frontend Static Pages
Route::get('/about', [FrontendController::class, 'about'])->name('about');
Route::get('/contact', [FrontendController::class, 'contact'])->name('contact');
Route::post('/contact', [FrontendController::class, 'contactSubmit'])->name('contact.submit');

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

Route::middleware(['auth'])->group(function () {
    Route::get('/my-account', [FrontendController::class, 'myAccount'])->name('my-account');
    Route::get('/my-addresses', [FrontendController::class, 'myAddresses'])->name('my-addresses');
    Route::get('/my-reviews', [FrontendController::class, 'myReviews'])->name('my-reviews');
    Route::get('/my-profile', [FrontendController::class, 'myProfile'])->name('my-profile');
    Route::post('/my-profile/update', [FrontendController::class, 'updateProfile'])->name('profile.update');
    Route::post('/my-profile/photo', [FrontendController::class, 'updateProfilePhoto'])->name('profile.photo');
    Route::delete('/my-reviews/{id}', [FrontendController::class, 'deleteReview'])->name('profile.review.delete');
    Route::put('/my-reviews/{id}', [FrontendController::class, 'updateReview'])->name('profile.review.update');
    Route::post('/product/{product}/review', [FrontendController::class, 'storeReview'])->name('product.review.store');
    Route::get('/my-orders', [FrontendController::class, 'myOrders'])->name('my-orders');
    Route::get('/order-detail', [FrontendController::class, 'orderDetail'])->name('order-detail');

    // Checkout & Wishlist (Auth Required)
    Route::get('/checkout', [CartController::class, 'checkout'])->name('checkout');
    Route::post('/checkout', [CartController::class, 'placeOrder'])->name('checkout.place');
    Route::get('/wishlist', [WishlistController::class, 'index'])->name('wishlist');
    Route::post('/wishlist/add/{product}', [WishlistController::class, 'add'])->name('wishlist.add');
    Route::post('/wishlist/remove/{product}', [WishlistController::class, 'remove'])->name('wishlist.remove');
    Route::post('/payment/razorpay/verify', [CartController::class, 'verifyRazorpay'])->name('razorpay.verify');
});

// Cart routes (Guest allowed to add, but cannot checkout)
Route::get('/cart', [CartController::class, 'index'])->name('cart');
Route::post('/cart/add/{product}', [CartController::class, 'add'])->name('cart.add');
Route::post('/cart/update', [CartController::class, 'update'])->name('cart.update');
Route::post('/cart/remove/{key}', [CartController::class, 'remove'])->name('cart.remove');
Route::get('/cart/mini-cart', [CartController::class, 'getMiniCart'])->name('cart.mini-cart');
Route::post('/cart/coupon', [CartController::class, 'applyCoupon'])->name('cart.coupon.apply');
Route::post('/cart/coupon/remove', [CartController::class, 'removeCoupon'])->name('cart.coupon.remove');

Route::get('/order-confirmation/{order?}', [CartController::class, 'orderConfirmation'])->name('order-confirmation');

// Dynamic Products/Categories
Route::get('/category/{slug}', [FrontendController::class, 'category'])->name('category.show');
Route::get('/category/{cat_slug}/{sub_slug}', [FrontendController::class, 'category'])->name('subCategory.show');
Route::get('/category/{cat_slug}/{sub_slug}/{child_slug}', [FrontendController::class, 'category'])->name('childCategory.show');
Route::get('/search', [FrontendController::class, 'search'])->name('search');
Route::get('/product/{slug}', [FrontendController::class, 'productShow'])->name('product.show');

// Special Category Routes (to match existing links if needed)

// This catch-all route handles direct category slugs (e.g. /sarees instead of /category/sarees)
// It MUST stay at the bottom of the front-end routes to avoid conflicts with static pages.
Route::get('/{slug}', [FrontendController::class, 'category'])->name('category.slug');

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
        Route::resource('orders', OrderController::class)->names('admin.orders');
        Route::get('orders/{order}/invoice', [OrderController::class, 'downloadInvoice'])->name('admin.orders.invoice');

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
        Route::resource('coupons', \App\Http\Controllers\Admin\CouponController::class)->names('admin.coupons');

        // Users
        Route::resource('users', UserController::class)->only(['index', 'show', 'edit', 'update'])->names('admin.users');
        Route::post('users/{user}/addresses', [UserController::class, 'storeAddress'])->name('admin.users.addresses.store');
        Route::delete('users/{user}/addresses/{address}', [UserController::class, 'destroyAddress'])->name('admin.users.addresses.destroy');
        
        // Stock Maintenance
        Route::get('/stock', [\App\Http\Controllers\Admin\StockController::class, 'index'])->name('admin.stock.index');
        Route::post('/stock/update-bulk', [\App\Http\Controllers\Admin\StockController::class, 'updateBulk'])->name('admin.stock.update-bulk');
        Route::get('/stock/{product}/logs', [\App\Http\Controllers\Admin\StockController::class, 'showLogs'])->name('admin.stock.logs');

        // Products
        Route::resource('products', ProductController::class)->names('admin.products');

        // Admin Profile & Management
        Route::get('/profile', [AdminProfileController::class, 'index'])->name('admin.profile.index');
        Route::post('/profile/update', [AdminProfileController::class, 'updateProfile'])->name('admin.profile.update');
        Route::post('/profile/photo', [AdminProfileController::class, 'updatePhoto'])->name('admin.profile.photo');
        Route::post('/profile/password', [AdminProfileController::class, 'updatePassword'])->name('admin.profile.password');
        Route::get('/manage-admins', [AdminProfileController::class, 'admins'])->name('admin.manage-admins.index');
        Route::post('/manage-admins', [AdminProfileController::class, 'storeAdmin'])->name('admin.manage-admins.store');

        // AJAX Helpers
        Route::get('/get-sub-categories/{category_id}', [ChildCategoryController::class, 'getSubCategories']);
        Route::get('/get-child-categories/{sub_category_id}', [ProductController::class, 'getChildCategories']);
    });
});
