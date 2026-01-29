<?php

namespace App\Providers;

use App\Domain\Commerce\Contracts\PaymentGatewayInterface;
use App\Domain\Commerce\Attribute;
use App\Domain\Commerce\Attribute;
use App\Domain\Commerce\Events\OrderPaid;
use App\Domain\Commerce\Product;
use App\Domain\Commerce\ProductReview;
use App\Domain\Commerce\Taxonomy;
use App\Domain\Commerce\Product;
use App\Domain\Commerce\ProductReview;
use App\Domain\Commerce\Taxonomy;
use App\Domain\Commerce\Gateways\StripeGateway;
use App\Domain\Content\Menu;
use App\Domain\Content\Menu;
use App\Domain\Content\Navigation;
use App\Domain\Content\Page;
use App\Domain\Content\ThemeSetting;
use App\Domain\Form\Events\ContractCreated;
use App\Domain\Form\Form;
use App\Domain\Media\Media;
use App\Domain\Media\MediaLibrary;
use App\Listeners\StartFunnelsOnContractCreated;
use App\Listeners\StartFunnelsOnOrderPaid;
use App\Observers\AttributeObserver;
use App\Observers\FormObserver;
use App\Observers\MediaLibraryObserver;
use App\Observers\MediaObserver;
use App\Observers\MenuObserver;
use App\Observers\NavigationObserver;
use App\Observers\ProductObserver;
use App\Observers\ProductReviewObserver;
use App\Observers\ProductObserver;
use App\Observers\ProductReviewObserver;
use App\Observers\PageObserver;
use App\Observers\TaxonomyObserver;
use App\Observers\TaxonomyObserver;
use App\Observers\ThemeSettingObserver;
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
        $this->app->bind(PaymentGatewayInterface::class, StripeGateway::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Page::observe(PageObserver::class);
        Navigation::observe(NavigationObserver::class);
        ThemeSetting::observe(ThemeSettingObserver::class);
        Menu::observe(MenuObserver::class);
        Product::observe(ProductObserver::class);
        Form::observe(FormObserver::class);
        Taxonomy::observe(TaxonomyObserver::class);
        Attribute::observe(AttributeObserver::class);
        ProductReview::observe(ProductReviewObserver::class);
        Media::observe(MediaObserver::class);
        MediaLibrary::observe(MediaLibraryObserver::class);

        $this->configureRateLimiting();
        $this->registerEventListeners();
    }

    protected function registerEventListeners(): void
    {
        Event::listen(ContractCreated::class, StartFunnelsOnContractCreated::class);
        Event::listen(OrderPaid::class, StartFunnelsOnOrderPaid::class);
    }

    protected function configureRateLimiting(): void
    {
        RateLimiter::for('form-submissions', function (Request $request) {
            return Limit::perMinute(5)->by($request->ip())->response(function () {
                return response()->json(['error' => 'Too many requests', 'retry_after' => 60], 429);
            });
        });

        RateLimiter::for('checkout', function (Request $request) {
            return Limit::perMinute(10)->by($request->ip())->response(function () {
                return response()->json(['error' => 'Too many requests', 'retry_after' => 60], 429);
            });
        });

        RateLimiter::for('api-read', function (Request $request) {
            return Limit::perMinute(60)->by($request->ip())->response(function () {
                return response()->json(['error' => 'Too many requests', 'retry_after' => 60], 429);
            });
        });

        RateLimiter::for('api-internal', function (Request $request) {
            $key = $request->bearerToken() ?? $request->header('X-Api-Token') ?? $request->ip();

            return Limit::perMinute(30)->by($key)->response(function () {
                return response()->json(['error' => 'Too many requests', 'retry_after' => 60], 429);
            });
        });

        RateLimiter::for('webhooks', function (Request $request) {
            return Limit::perMinute(100)->by($request->ip())->response(function () {
                return response()->json(['error' => 'Too many requests', 'retry_after' => 60], 429);
            });
        });
    }
}
