<?php

use App\Http\Controllers\Api\NavigationController;
use App\Http\Controllers\Api\PageController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    Route::get('/pages/{slug}', [PageController::class, 'show']);
    Route::get('/navigation', [NavigationController::class, 'index']);
});
