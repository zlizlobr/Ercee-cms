<?php

use App\Http\Controllers\Api\CheckoutController;
use App\Http\Controllers\Api\FormController;
use App\Http\Controllers\Api\MediaController;
use App\Http\Controllers\Api\NavigationController;
use App\Http\Controllers\Api\PageController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\RebuildFrontendController;
use App\Http\Controllers\Api\TaxonomyMappingController;
use App\Http\Controllers\Api\ThemeController;
use App\Http\Controllers\Api\WebhookController;
use Illuminate\Support\Facades\Route;

Route::get('/health', App\Http\Controllers\Api\HealthController::class);

Route::prefix('v1')->middleware('throttle:api-read')->group(function () {
    Route::get('/pages', [PageController::class, 'index']);
    Route::get('/pages/{slug}', [PageController::class, 'show']);
    Route::get('/navigation', [NavigationController::class, 'index']);
    Route::get('/navigation/{menuSlug}', [NavigationController::class, 'index']);
    Route::get('/menus/{menuSlug}', [NavigationController::class, 'show']);
    Route::get('/products', [ProductController::class, 'index']);
    Route::get('/products/{id}', [ProductController::class, 'show'])->whereNumber('id');
    Route::get('/forms/{id}', [FormController::class, 'show'])->whereNumber('id');
    Route::get('/theme', [ThemeController::class, 'index']);
    Route::get('/media', [MediaController::class, 'index']);
    Route::get('/media/{uuid}', [MediaController::class, 'show']);
    Route::post('/media/resolve', [MediaController::class, 'resolve']);
    Route::get('/taxonomies/mapping', [TaxonomyMappingController::class, 'index']);
    Route::get('/taxonomy-mapping', [TaxonomyMappingController::class, 'index']);

    // Media resolve gets its own limiter to prevent write-heavy usage from impacting reads.
    Route::post('/media/resolve', [MediaController::class, 'resolve'])
        ->middleware('throttle:media-resolve');

    // Critical writes must be isolated and idempotent-safe.
    Route::post('/forms/{id}/submit', [FormController::class, 'submit'])
        ->whereNumber('id')
        ->middleware(['throttle:form-submissions', 'api.idempotency']);

    Route::post('/checkout', [CheckoutController::class, 'checkout'])
        ->middleware(['throttle:checkout', 'api.idempotency']);
});

Route::prefix('webhooks')->middleware(['webhook.whitelist', 'throttle:webhooks'])->group(function () {
    Route::post('/stripe', [WebhookController::class, 'stripe']);
});

Route::prefix('internal')->middleware(['api.auth', 'throttle:api-internal'])->group(function () {
    Route::post('/rebuild-frontend', [RebuildFrontendController::class, 'rebuild']);
});
