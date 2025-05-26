<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;


Route::prefix('auth')->group(function () {
    Route::post('register', [AuthController::class, 'register']);
    Route::post('login', [AuthController::class, 'login']);
    Route::post('logout', [AuthController::class, 'logout'])
        ->middleware('auth:sanctum');
});

// Public routes available on central domain
Route::prefix('public')->group(function () {
    Route::get('events', [\App\Http\Controllers\Api\Public\EventController::class, 'index']);
    Route::get('organizations', [\App\Http\Controllers\Api\Public\OrganizationController::class, 'index']);
});

// Tenant routes (will be prefixed with organization slug)
Route::prefix('{organization}')->group(function () {
    Route::prefix('public')->group(function () {
        // List all events for organization
        Route::get('events', [\App\Http\Controllers\Api\Public\EventController::class, 'index']);
        
        // Show single event
        Route::get('events/{event}', [\App\Http\Controllers\Api\Public\EventController::class, 'show']);
        
        // Register for event
        Route::post('events/{event}/register', [\App\Http\Controllers\Api\Public\EventController::class, 'register']);
    });

    Route::middleware('auth:sanctum')->prefix('admin')->group(function () {
        Route::apiResource('events', \App\Http\Controllers\Api\Admin\EventController::class);
    });
    Route::prefix('events/{eventId}/attendees')->group(function () {
        Route::get('/', [\App\Http\Controllers\Api\Admin\AttendeeController::class, 'index']);
        Route::get('{id}', [\App\Http\Controllers\Api\Admin\AttendeeController::class, 'show']);
        Route::delete('{id}', [\App\Http\Controllers\Api\Admin\AttendeeController::class, 'destroy']);
    });
});