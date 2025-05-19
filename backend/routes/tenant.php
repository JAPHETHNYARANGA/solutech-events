<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Stancl\Tenancy\Middleware\InitializeTenancyByPath;
use Stancl\Tenancy\Middleware\PreventAccessFromCentralDomains;

Route::middleware([
    'api',
    InitializeTenancyByPath::class,
    PreventAccessFromCentralDomains::class,
])->prefix('{organization}')->group(function () {
    
    // Public Event Routes
    Route::prefix('public')->group(function () {
        Route::get('events/{event}', [\App\Http\Controllers\Api\Public\EventController::class, 'show']);
        Route::post('events/{event}/register', [\App\Http\Controllers\Api\Public\EventController::class, 'register']);
    });

    // Admin Routes
    Route::middleware('auth:sanctum')->prefix('admin')->group(function () {
        Route::apiResource('events', \App\Http\Controllers\Api\Admin\EventController::class);
    });
});