<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Stancl\Tenancy\Middleware\InitializeTenancyByDomain;
use Stancl\Tenancy\Middleware\PreventAccessFromCentralDomains;



Route::middleware([
    'api', // Use API middleware group instead of web
    InitializeTenancyByDomain::class,
    PreventAccessFromCentralDomains::class,
])->group(function () {
    
    // Authentication Routes
    Route::prefix('auth')->group(function () {
        Route::post('login', [\App\Http\Controllers\Api\AuthController::class, 'login']);
        Route::post('logout', [\App\Http\Controllers\Api\AuthController::class, 'logout'])
            ->middleware('auth:sanctum');
    });

    // Admin Routes (require authentication)
    Route::middleware('auth:sanctum')->prefix('admin')->group(function () {
        // Organizations
        Route::apiResource('organizations', \App\Http\Controllers\Api\Admin\OrganizationController::class)
            ->except(['index', 'show']);
        
        // Events
        Route::apiResource('events', \App\Http\Controllers\Api\Admin\EventController::class);
        
        // Attendees
        Route::apiResource('events.attendees', \App\Http\Controllers\Api\Admin\AttendeeController::class)
            ->only(['index', 'show', 'destroy']);
    });

    // Public Routes (no authentication required)
    Route::prefix('public')->group(function () {
        Route::get('events', [\App\Http\Controllers\Api\Public\EventController::class, 'index']);
        Route::get('events/{event}', [\App\Http\Controllers\Api\Public\EventController::class, 'show']);
        Route::post('events/{event}/register', [\App\Http\Controllers\Api\Public\EventController::class, 'register']);
    });

});