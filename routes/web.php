<?php

use App\Http\Controllers\Admin\PagePreviewController;
use App\Http\Controllers\FrontendController;
use Illuminate\Support\Facades\Route;

/**
 * Web routes for frontend requests and locale switching.
 */

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
    Route::get('/products', [FrontendController::class, 'products'])->name('frontend.products');
    Route::get('/products/{id}', [FrontendController::class, 'product'])->name('frontend.product');
    Route::get('/checkout/{productId}', [FrontendController::class, 'checkout'])->name('frontend.checkout');
    Route::get('/thank-you', [FrontendController::class, 'thankYou'])->name('frontend.thank-you');
    Route::get('/payment/return', [FrontendController::class, 'paymentReturn'])->name('frontend.payment.return');
    Route::get('/{slug}', [FrontendController::class, 'page'])->name('frontend.page')->where('slug', '^(?!api|admin|filament).*$');
});
