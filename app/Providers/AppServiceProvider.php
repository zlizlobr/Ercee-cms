<?php

namespace App\Providers;

use App\Domain\Content\Navigation;
use App\Domain\Content\Page;
use App\Observers\NavigationObserver;
use App\Observers\PageObserver;
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
        Page::observe(PageObserver::class);
        Navigation::observe(NavigationObserver::class);
    }
}
