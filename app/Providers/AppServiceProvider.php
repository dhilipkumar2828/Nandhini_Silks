<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Set independent session cookie for admin area to allow parallel admin/user sessions
        if (!app()->runningInConsole() && request()->is('admin*')) {
            config(['session.cookie' => config('session.cookie') . '_admin']);
        }
        \Illuminate\Pagination\Paginator::useTailwind();
        \Illuminate\Pagination\Paginator::defaultView('vendor.pagination.custom');

        view()->composer('frontend.layouts.app', function ($view) {
            $categories = \App\Models\Category::with('subCategories.childCategories')
                ->where('status', 1)
                // ->whereIn('name', ['Sarees', 'Men', 'Kids', 'Women']) // Added Filter
                ->orderBy('display_order', 'asc')
                ->get();
            $view->with('headerCategories', $categories);

            // Total cart items count logic
            $cartCount = 0;
            if (auth()->check()) {
                $cartCount = \App\Models\CartItem::where('user_id', auth()->id())->sum('quantity');
            } else {
                $cartCount = collect(session('cart', []))->sum('quantity');
            }
            $view->with('cartCount', $cartCount);

            // Wishlist items count logic
            $wishlistCount = 0;
            if (auth()->check()) {
                // If we decide to use DB for wishlist later, change here. Currently session.
                $wishlistCount = count(session('wishlist', []));
            } else {
                $wishlistCount = count(session('wishlist', []));
            }
            $view->with('wishlistCount', $wishlistCount);
        });
    }
}
