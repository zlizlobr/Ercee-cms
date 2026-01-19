<?php

namespace App\Providers;

use App\Domain\Content\Navigation;
use App\Domain\Content\Page;
use App\Domain\Form\Events\ContractCreated;
use App\Listeners\StartFunnelsOnContractCreated;
use App\Observers\NavigationObserver;
use App\Observers\PageObserver;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\RateLimiter;
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

        $this->configureRateLimiting();
        $this->registerEventListeners();
    }

    protected function registerEventListeners(): void
    {
        Event::listen(ContractCreated::class, StartFunnelsOnContractCreated::class);
    }

    protected function configureRateLimiting(): void
    {
        RateLimiter::for('form-submissions', function (Request $request) {
            return Limit::perMinute(5)->by($request->ip());
        });
    }
}
