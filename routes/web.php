<?php

use App\Http\Controllers\Admin\PagePreviewController;
use App\Http\Controllers\FrontendController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;

/**
 * Web routes for frontend requests and locale switching.
 */

// Serve media files (UUID paths provide security through obscurity)
Route::get('/media/{path}', function (string $path) {
    $fullPath = Storage::disk('media')->path($path);

    if (! file_exists($fullPath)) {
        abort(404);
    }

    return response()->file($fullPath, [
        'Cache-Control' => 'public, max-age=31536000',
    ]);
})->where('path', '.*')->name('media.serve');

// Admin page preview (requires authentication)
Route::get('/admin/pages/{page}/preview', PagePreviewController::class)
    ->middleware(['web', 'auth'])
    ->name('admin.pages.preview');

// Serve generated theme build artifacts directly from storage.
Route::get('/storage/app/theme-builds/{path}', function (string $path) {
    $buildsRoot = realpath(storage_path('app/theme-builds'));

    if ($buildsRoot === false) {
        abort(404);
    }

    $requestedPath = storage_path('app/theme-builds/' . ltrim($path, '/'));
    $resolvedPath = realpath($requestedPath);

    // Block directory traversal and allow only existing files from theme-builds root.
    if ($resolvedPath === false || ! is_file($resolvedPath) || ! str_starts_with($resolvedPath, $buildsRoot . DIRECTORY_SEPARATOR)) {
        abort(404);
    }

    return response()->download($resolvedPath);
})->where('path', '.*')->name('theme-builds.download');

// Language switcher
Route::get('/lang/{locale}', function (string $locale) {
    $supported = ['cs', 'en'];

    if (in_array($locale, $supported)) {
        session(['locale' => $locale]);
    }

    return redirect()->back();
})->name('locale.switch');

// Public routes - redirect to headless frontend when FRONTEND_URL is configured
Route::middleware('redirect.frontend')->group(function () {
    Route::get('/', [FrontendController::class, 'home'])->name('frontend.home');
    Route::get('/{slug}', [FrontendController::class, 'page'])->name('frontend.page')->where('slug', '^(?!api|admin|filament).*$');
});
