<?php

use App\Http\Controllers\Api\MediaController;
use App\Http\Controllers\Api\NavigationController;
use App\Http\Controllers\Api\PageController;
use App\Http\Controllers\Api\RebuildFrontendController;
use App\Http\Controllers\Api\ThemeController;
use Illuminate\Support\Facades\Route;

Route::get('/health', App\Http\Controllers\Api\HealthController::class);

Route::prefix('v1')->middleware('api.public')->group(function () {
    Route::get('/pages', [PageController::class, 'index'])->middleware('throttle:api-read');
    Route::get('/pages/{slug}', [PageController::class, 'show'])->middleware('throttle:api-read');
    Route::get('/navigation', [NavigationController::class, 'index'])->middleware('throttle:api-read');
    Route::get('/navigation/{menuSlug}', [NavigationController::class, 'index'])->middleware('throttle:api-read');
    Route::get('/menus/{menuSlug}', [NavigationController::class, 'show'])->middleware('throttle:api-read');
    Route::get('/theme', [ThemeController::class, 'index'])->middleware('throttle:api-read');
    Route::get('/media', [MediaController::class, 'index'])->middleware('throttle:api-read');
    Route::get('/media/{uuid}', [MediaController::class, 'show'])->middleware('throttle:api-read');

    Route::post('/media/resolve', [MediaController::class, 'resolve'])
        ->middleware('throttle:media-resolve');
});

// Public form submission endpoint (no authentication required)
Route::post('/v1/forms/{id}/submit', function () {
    // TODO: Implement form submission handler
    // This endpoint is intentionally excluded from bearer token authentication
    return response()->json(['message' => 'Form endpoint not yet implemented'], 501);
});

Route::prefix('internal')->middleware(['api.auth', 'throttle:api-internal'])->group(function () {
    Route::post('/rebuild-frontend', [RebuildFrontendController::class, 'rebuild']);
});
