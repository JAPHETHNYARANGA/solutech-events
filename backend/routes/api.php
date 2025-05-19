<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\Public\EventController;

// Central domain routes
Route::prefix('auth')->group(function () {
    Route::post('register', [AuthController::class, 'register']);
    Route::post('login', [AuthController::class, 'login']);
    Route::post('logout', [AuthController::class, 'logout'])
        ->middleware('auth:sanctum');
});

// Public routes available on central domain
Route::prefix('public')->group(function () {
    Route::get('organizations', [AuthController::class, 'listOrganizations']);
    Route::get('events', [EventController::class, 'indexAll']);
});