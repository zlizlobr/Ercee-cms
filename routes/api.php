<?php

use App\Http\Controllers\Api\CheckoutController;
use App\Http\Controllers\Api\FormController;
use App\Http\Controllers\Api\NavigationController;
use App\Http\Controllers\Api\PageController;
use App\Http\Controllers\Api\WebhookController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    Route::get('/pages/{slug}', [PageController::class, 'show']);
    Route::get('/navigation', [NavigationController::class, 'index']);

    Route::post('/forms/{id}/submit', [FormController::class, 'submit'])
        ->middleware('throttle:form-submissions');

    Route::post('/checkout', [CheckoutController::class, 'checkout'])
        ->middleware('throttle:checkout');
});

Route::prefix('webhooks')->group(function () {
    Route::post('/stripe', [WebhookController::class, 'stripe']);
});
