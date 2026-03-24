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
        \Illuminate\Pagination\Paginator::useTailwind();
        \Illuminate\Pagination\Paginator::defaultView('vendor.pagination.custom');

        view()->composer('frontend.layouts.app', function ($view) {
            $categories = \App\Models\Category::with('subCategories.childCategories')
                ->where('status', 1)
                ->whereIn('name', ['Sarees', 'Men', 'Kids', 'Women']) // Added Filter
                ->orderBy('display_order', 'asc')
                ->get();
            $view->with('headerCategories', $categories);
        });
    }
}
