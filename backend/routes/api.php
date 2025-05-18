<?php

use App\Http\Controllers\Api\Public\EventController;
use App\Http\Controllers\Api\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::prefix('public')->group(function () {
    Route::get('events', [EventController::class, 'indexAll']); // Show all events
});

Route::prefix('auth')->group(function () {
    Route::post('register', [AuthController::class, 'register']); // Admin registration
    Route::post('login', [AuthController::class, 'login']); // Admin login
});