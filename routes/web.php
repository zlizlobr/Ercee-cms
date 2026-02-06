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
