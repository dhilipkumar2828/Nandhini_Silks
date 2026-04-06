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
use App\Http\Controllers\Admin\InquiryController;
use App\Http\Controllers\Admin\AttributeController;
use App\Http\Controllers\Admin\AttributeValueController;

Route::post('/addresses', [UserAddressController::class, 'store'])->name('addresses.store');

Route::get('/', [FrontendController::class, 'index'])->name('home');
Route::get('/shop', [FrontendController::class, 'shop'])->name('shop');

// User Authentication
Route::post('/login', [UserAuthController::class, 'login'])->name('login.submit');
Route::post('/register', [UserAuthController::class, 'register'])->name('register');
Route::get('/verify-otp', [UserAuthController::class, 'showVerifyForm'])->name('otp.verify.form');
Route::post('/verify-otp', [UserAuthController::class, 'verifyOTP'])->name('otp.verify.submit');
Route::post('/resend-otp', [UserAuthController::class, 'resendOTP'])->name('otp.resend');
Route::post('/logout', [UserAuthController::class, 'logout'])->name('logout');

// Password Reset Routes
Route::get('/forgot-password', [UserAuthController::class, 'showLinkRequestForm'])->name('password.request');
Route::post('/forgot-password', [UserAuthController::class, 'sendResetLinkEmail'])->name('password.email');
Route::get('/reset-password/{token}', [UserAuthController::class, 'showResetForm'])->name('password.reset');
Route::post('/reset-password', [UserAuthController::class, 'reset'])->name('password.update');

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
    Route::put('/addresses/{address}', [UserAddressController::class, 'update'])->name('addresses.update');
    Route::delete('/addresses/{address}', [UserAddressController::class, 'destroy'])->name('addresses.destroy');
    Route::get('/my-reviews', [FrontendController::class, 'myReviews'])->name('my-reviews');
    Route::get('/my-profile', [FrontendController::class, 'myProfile'])->name('my-profile');
    Route::post('/my-profile/update', [FrontendController::class, 'updateProfile'])->name('profile.update');
    Route::post('/my-profile/photo', [FrontendController::class, 'updateProfilePhoto'])->name('profile.photo');
    Route::post('/my-profile/email/request-otp', [FrontendController::class, 'requestEmailChangeOtp'])->name('profile.email.request_otp');
    Route::post('/my-profile/email/verify-otp', [FrontendController::class, 'verifyEmailChangeOtp'])->name('profile.email.verify_otp');
    Route::delete('/my-reviews/{id}', [FrontendController::class, 'deleteReview'])->name('profile.review.delete');
    Route::put('/my-reviews/{id}', [FrontendController::class, 'updateReview'])->name('profile.review.update');
    Route::post('/product/{product}/review', [FrontendController::class, 'storeReview'])->name('product.review.store');
    Route::get('/my-orders', [FrontendController::class, 'myOrders'])->name('my-orders');
    Route::get('/order-detail', [FrontendController::class, 'orderDetail'])->name('order-detail');
    Route::post('/order/return-request/{id}', [FrontendController::class, 'returnRequest'])->name('order.return.request');

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
Route::post('/cart/shipping', [CartController::class, 'updateShippingDestination'])->name('cart.shipping.update');
Route::post('/check-serviceability', [CartController::class, 'checkServiceability'])->name('check-serviceability');

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

        // Slug & Name Uniqueness Checks (Must be above resources to avoid conflict with show() method)
        Route::get('/categories/check-slug', [CategoryController::class, 'checkSlug'])->name('admin.categories.check-slug');
        Route::get('/categories/check-name', [CategoryController::class, 'checkName'])->name('admin.categories.check-name');
        
        Route::get('/sub-categories/check-slug', [SubCategoryController::class, 'checkSlug'])->name('admin.sub-categories.check-slug');
        Route::get('/sub-categories/check-name', [SubCategoryController::class, 'checkName'])->name('admin.sub-categories.check-name');
        
        Route::get('/child-categories/check-slug', [ChildCategoryController::class, 'checkSlug'])->name('admin.child-categories.check-slug');
        Route::get('/child-categories/check-name', [ChildCategoryController::class, 'checkName'])->name('admin.child-categories.check-name');

        Route::get('/attributes/check-slug', [AttributeController::class, 'checkSlug'])->name('admin.attributes.check-slug');
        Route::get('/attributes/check-name', [AttributeController::class, 'checkName'])->name('admin.attributes.check-name');

        // Category Management
        Route::resource('categories', CategoryController::class)->names('admin.categories');

        // Orders
        Route::resource('orders', OrderController::class)->names('admin.orders');
        Route::get('orders/{order}/invoice', [OrderController::class, 'downloadInvoice'])->name('admin.orders.invoice');
        Route::post('orders/{order}/shiprocket/push', [OrderController::class, 'pushToShiprocket'])->name('admin.orders.shiprocket.push');
        Route::post('orders/{order}/shiprocket/awb', [OrderController::class, 'assignShiprocketAWB'])->name('admin.orders.shiprocket.awb');
        Route::get('orders/{order}/shiprocket/label', [OrderController::class, 'generateShiprocketLabel'])->name('admin.orders.shiprocket.label');
        Route::post('orders/{order}/shiprocket/pickup', [OrderController::class, 'requestShiprocketPickup'])->name('admin.orders.shiprocket.pickup');
        Route::post('orders/{order}/shiprocket/return', [OrderController::class, 'createShiprocketReturn'])->name('admin.orders.shiprocket.return');

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
        Route::resource('offer-collections', \App\Http\Controllers\Admin\OfferCollectionController::class)->names('admin.offer-collections');

        // Shipping Management
        Route::resource('shipping-classes', \App\Http\Controllers\Admin\ShippingClassController::class)->names('admin.shipping-classes');
        Route::resource('shipping-rates', \App\Http\Controllers\Admin\ShippingRateController::class)->names('admin.shipping-rates');

        // Users
        Route::resource('users', UserController::class)->only(['index', 'show', 'edit', 'update'])->names('admin.users');
        Route::post('users/{user}/addresses', [UserController::class, 'storeAddress'])->name('admin.users.addresses.store');
        Route::put('users/{user}/addresses/{address}', [UserController::class, 'updateAddress'])->name('admin.users.addresses.update');
        Route::delete('users/{user}/addresses/{address}', [UserController::class, 'destroyAddress'])->name('admin.users.addresses.destroy');
        
        // Stock Maintenance
        Route::get('/stock', [\App\Http\Controllers\Admin\StockController::class, 'index'])->name('admin.stock.index');
        Route::post('/stock/update-bulk', [\App\Http\Controllers\Admin\StockController::class, 'updateBulk'])->name('admin.stock.update-bulk');
        Route::get('/stock/{product}/logs', [\App\Http\Controllers\Admin\StockController::class, 'showLogs'])->name('admin.stock.logs');

        // Products
        Route::get('products/check-uniqueness', [ProductController::class, 'checkUniqueness'])->name('admin.products.check-uniqueness');
        Route::get('products/success', function() { return view('admin.products.success'); })->name('admin.products.success');
        Route::resource('products', ProductController::class)->names('admin.products');

        // Admin Profile & Management
        Route::get('/profile', [AdminProfileController::class, 'index'])->name('admin.profile.index');
        Route::post('/profile/update', [AdminProfileController::class, 'updateProfile'])->name('admin.profile.update');
        Route::post('/profile/photo', [AdminProfileController::class, 'updatePhoto'])->name('admin.profile.photo');
        Route::post('/profile/password', [AdminProfileController::class, 'updatePassword'])->name('admin.profile.password');
        Route::get('/manage-admins', [AdminProfileController::class, 'admins'])->name('admin.manage-admins.index');
        Route::post('/manage-admins', [AdminProfileController::class, 'storeAdmin'])->name('admin.manage-admins.store');

        // System Settings
        Route::get('/settings', [\App\Http\Controllers\Admin\SettingController::class, 'index'])->name('admin.settings.index');
        Route::post('/settings', [\App\Http\Controllers\Admin\SettingController::class, 'update'])->name('admin.settings.update');

        // Product Reviews
        Route::get('/reviews', [\App\Http\Controllers\Admin\ProductReviewController::class, 'index'])->name('admin.reviews.index');
        Route::post('/reviews/{id}/status', [\App\Http\Controllers\Admin\ProductReviewController::class, 'updateStatus'])->name('admin.reviews.status');
        Route::delete('/reviews/{id}', [\App\Http\Controllers\Admin\ProductReviewController::class, 'destroy'])->name('admin.reviews.destroy');

        // Inquiries Management
        Route::resource('inquiries', InquiryController::class)->only(['index', 'show', 'update', 'destroy'])->names('admin.inquiries');

        // AJAX Helpers
        Route::get('/get-sub-categories/{category_id}', [ChildCategoryController::class, 'getSubCategories']);
        Route::get('/get-child-categories/{sub_category_id}', [ProductController::class, 'getChildCategories']);

        // Catch-all for admin area (handles typos like /admin/loginnn)
        Route::any('{any}', function () {
            return redirect()->route('admin.login');
        })->where('any', '.*');
    });
});

// Global fallback for website (handles typos like /loginmm)
Route::fallback(function () {
    return redirect()->route('home');
});
