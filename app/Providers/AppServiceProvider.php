<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Auth;
use App\Models\Setting;

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

        // Dynamic Mail Configuration from Database
        try {
            if (Schema::hasTable('settings')) {
                $settings = \App\Models\Setting::all()->pluck('value', 'key');
                if (isset($settings['mail_host'])) {
                    config([
                        'mail.mailers.smtp.host' => $settings['mail_host'],
                        'mail.mailers.smtp.port' => $settings['mail_port'] ?? 587,
                        'mail.mailers.smtp.encryption' => $settings['mail_encryption'] ?? 'tls',
                        'mail.mailers.smtp.username' => $settings['mail_username'],
                        'mail.mailers.smtp.password' => $settings['mail_password'],
                        'mail.from.address' => $settings['mail_from_address'] ?? $settings['mail_username'],
                        'mail.from.name' => $settings['mail_from_name'] ?? config('app.name'),
                    ]);
                }
            }
        } catch (\Exception $e) {
            // Silently fail if table not ready (e.g. during migrations)
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
            if (Auth::check()) {
                $cartCount = \App\Models\CartItem::where('user_id', Auth::id())->sum('quantity');
            } else {
                $cartCount = collect(session('cart', []))->sum('quantity');
            }
            $view->with('cartCount', $cartCount);

            // Wishlist items count logic
            $wishlistCount = 0;
            if (Auth::check()) {
                // If we decide to use DB for wishlist later, change here. Currently session.
                $wishlistCount = count(session('wishlist', []));
            } else {
                $wishlistCount = count(session('wishlist', []));
            }
            $view->with('wishlistCount', $wishlistCount);
        });
    }
}
