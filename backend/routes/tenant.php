<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Stancl\Tenancy\Middleware\InitializeTenancyByDomain;
use Stancl\Tenancy\Middleware\PreventAccessFromCentralDomains;

Route::middleware([
    'api',
    InitializeTenancyByDomain::class,
    PreventAccessFromCentralDomains::class,
])->group(function () {
    
    // Public Event Routes
    Route::prefix('public')->group(function () {
        Route::get('events/{event}', [\App\Http\Controllers\Api\Public\EventController::class, 'show']);
        Route::post('events/{event}/register', [\App\Http\Controllers\Api\Public\EventController::class, 'register']);
    });

    // Admin Routes
    Route::middleware('auth:sanctum')->prefix('admin')->group(function () {
        Route::apiResource('events', \App\Http\Controllers\Api\Admin\EventController::class);
        Route::apiResource('events.attendees', \App\Http\Controllers\Api\Admin\AttendeeController::class)
            ->only(['index', 'show', 'destroy']);
    });
});