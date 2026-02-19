<?php

namespace App\Providers;

use App\Domain\Content\Menu;
use App\Domain\Content\Navigation;
use App\Domain\Content\Page;
use App\Domain\Content\CookieSetting;
use App\Domain\Content\ThemeSetting;
use App\Domain\Media\Media;
use App\Domain\Media\MediaLibrary;
use App\Observers\CookieSettingObserver;
use App\Observers\MediaLibraryObserver;
use App\Observers\MediaObserver;
use App\Observers\MenuObserver;
use App\Observers\NavigationObserver;
use App\Observers\PageObserver;
use App\Observers\ThemeSettingObserver;
use App\Contracts\Services\SubscriberServiceInterface;
use App\Domain\Subscriber\SubscriberService;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(SubscriberServiceInterface::class, SubscriberService::class);
    }

    public function boot(): void
    {
        $this->registerFilesystemCompatibilityMacros();

        Page::observe(PageObserver::class);
        Navigation::observe(NavigationObserver::class);
        ThemeSetting::observe(ThemeSettingObserver::class);
        CookieSetting::observe(CookieSettingObserver::class);
        Menu::observe(MenuObserver::class);
        Media::observe(MediaObserver::class);
        MediaLibrary::observe(MediaLibraryObserver::class);

        $this->configureRateLimiting();
    }

    protected function registerFilesystemCompatibilityMacros(): void
    {
        if (method_exists(Filesystem::class, 'isAbsolutePath') || File::hasMacro('isAbsolutePath')) {
            return;
        }

        File::macro('isAbsolutePath', function (string $path): bool {
            return str_starts_with($path, DIRECTORY_SEPARATOR)
                || preg_match('/^[A-Za-z]:[\\\\\\/]/', $path) === 1;
        });
    }

    protected function configureRateLimiting(): void
    {
        RateLimiter::for('form-submissions', function (Request $request) {
            $formId = (string) ($request->route('id') ?? 'unknown');
            $key = implode('|', [$request->ip(), 'form-submit', $formId]);

            return Limit::perMinute(5)->by($key)->response(function () {
                return response()->json(['error' => 'Too many requests', 'retry_after' => 60], 429);
            });
        });

        RateLimiter::for('checkout', function (Request $request) {
            $key = implode('|', [$request->ip(), 'checkout']);

            return Limit::perMinute(10)->by($key)->response(function () {
                return response()->json(['error' => 'Too many requests', 'retry_after' => 60], 429);
            });
        });

        RateLimiter::for('api-read', function (Request $request) {
            $route = $request->route();
            $routeKey = $route ? $route->uri() : 'unknown';
            $params = $route ? implode('|', $route->parameters()) : 'none';
            $key = implode('|', [$request->ip(), 'api-read', $routeKey, $params]);

            return Limit::perMinute(60)->by($key)->response(function () {
                return response()->json(['error' => 'Too many requests', 'retry_after' => 60], 429);
            });
        });

        RateLimiter::for('media-resolve', function (Request $request) {
            $mediaKey = (string) ($request->input('uuid') ?? $request->input('id') ?? 'unknown');
            $key = implode('|', [$request->ip(), 'media-resolve', $mediaKey]);

            return Limit::perMinute(30)->by($key)->response(function () {
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
