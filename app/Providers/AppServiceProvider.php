<?php

namespace App\Providers;

use App\Domain\Commerce\Attribute;
use App\Domain\Commerce\Product;
use App\Domain\Commerce\ProductReview;
use App\Domain\Commerce\Taxonomy;
use App\Domain\Content\Menu;
use App\Domain\Content\Navigation;
use App\Domain\Content\Page;
use App\Domain\Content\ThemeSetting;
use App\Domain\Form\Form;
use App\Domain\Media\Media;
use App\Domain\Media\MediaLibrary;
use App\Observers\AttributeObserver;
use App\Observers\FormObserver;
use App\Observers\MediaLibraryObserver;
use App\Observers\MediaObserver;
use App\Observers\MenuObserver;
use App\Observers\NavigationObserver;
use App\Observers\ProductObserver;
use App\Observers\ProductReviewObserver;
use App\Observers\PageObserver;
use App\Observers\TaxonomyObserver;
use App\Observers\ThemeSettingObserver;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Modules\Commerce\Domain\Contracts\PaymentGatewayInterface;
use Modules\Commerce\Domain\Gateways\StripeGateway;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(PaymentGatewayInterface::class, StripeGateway::class);
    }

    public function boot(): void
    {
        // Core observers
        Page::observe(PageObserver::class);
        Navigation::observe(NavigationObserver::class);
        ThemeSetting::observe(ThemeSettingObserver::class);
        Menu::observe(MenuObserver::class);
        Media::observe(MediaObserver::class);
        MediaLibrary::observe(MediaLibraryObserver::class);

        // Module observers (will move to module providers)
        Product::observe(ProductObserver::class);
        Form::observe(FormObserver::class);
        Taxonomy::observe(TaxonomyObserver::class);
        Attribute::observe(AttributeObserver::class);
        ProductReview::observe(ProductReviewObserver::class);

        $this->configureRateLimiting();
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
