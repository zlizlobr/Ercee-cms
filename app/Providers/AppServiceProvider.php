<?php

namespace App\Providers;

use App\Domain\Content\Menu;
use App\Domain\Content\Navigation;
use App\Domain\Content\Page;
use App\Domain\Content\ThemeSetting;
use App\Domain\Media\Media;
use App\Domain\Media\MediaLibrary;
use App\Observers\AttributeObserver;
use App\Observers\FormObserver;
use App\Observers\MediaLibraryObserver;
use App\Observers\MediaObserver;
use App\Observers\MenuObserver;
use App\Observers\NavigationObserver;
use App\Observers\PageObserver;
use App\Observers\ProductObserver;
use App\Observers\ProductReviewObserver;
use App\Observers\TaxonomyObserver;
use App\Observers\ThemeSettingObserver;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Modules\Commerce\Domain\Attribute;
use Modules\Commerce\Domain\Contracts\PaymentGatewayInterface;
use Modules\Commerce\Domain\Gateways\StripeGateway;
use Modules\Commerce\Domain\Product;
use Modules\Commerce\Domain\ProductReview;
use Modules\Commerce\Domain\Taxonomy;
use Modules\Forms\Domain\Form;

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
        // Isolate form submissions per form to prevent a noisy form from blocking others.
        RateLimiter::for('form-submissions', function (Request $request) {
            $formId = (string) ($request->route('id') ?? 'unknown');
            $key = implode('|', [$request->ip(), 'form-submit', $formId]);

            return Limit::perMinute(5)->by($key)->response(function () {
                return response()->json(['error' => 'Too many requests', 'retry_after' => 60], 429);
            });
        });

        // Protect checkout/payment flows with a dedicated limiter.
        RateLimiter::for('checkout', function (Request $request) {
            $key = implode('|', [$request->ip(), 'checkout']);

            return Limit::perMinute(10)->by($key)->response(function () {
                return response()->json(['error' => 'Too many requests', 'retry_after' => 60], 429);
            });
        });

        // Read-only API endpoints are isolated by route+params in the key.
        RateLimiter::for('api-read', function (Request $request) {
            $route = $request->route();
            $routeKey = $route ? $route->uri() : 'unknown';
            $params = $route ? implode('|', $route->parameters()) : 'none';
            $key = implode('|', [$request->ip(), 'api-read', $routeKey, $params]);

            return Limit::perMinute(60)->by($key)->response(function () {
                return response()->json(['error' => 'Too many requests', 'retry_after' => 60], 429);
            });
        });

        // Media resolving is write-adjacent and should not impact read traffic.
        RateLimiter::for('media-resolve', function (Request $request) {
            $mediaKey = (string) ($request->input('uuid') ?? $request->input('id') ?? 'unknown');
            $key = implode('|', [$request->ip(), 'media-resolve', $mediaKey]);

            return Limit::perMinute(30)->by($key)->response(function () {
                return response()->json(['error' => 'Too many requests', 'retry_after' => 60], 429);
            });
        });

        // Internal endpoints are scoped by auth token where possible.
        RateLimiter::for('api-internal', function (Request $request) {
            $key = $request->bearerToken() ?? $request->header('X-Api-Token') ?? $request->ip();

            return Limit::perMinute(30)->by($key)->response(function () {
                return response()->json(['error' => 'Too many requests', 'retry_after' => 60], 429);
            });
        });

        // Webhooks are isolated to prevent third-party spikes from impacting the API.
        RateLimiter::for('webhooks', function (Request $request) {
            return Limit::perMinute(100)->by($request->ip())->response(function () {
                return response()->json(['error' => 'Too many requests', 'retry_after' => 60], 429);
            });
        });
    }
}
