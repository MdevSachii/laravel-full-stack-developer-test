<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PackageController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\AuthController;

Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);

// Apply JWT middleware only to protected routes
Route::middleware([\App\Http\Middleware\VerifyJWTToken::class])->group(function () {
    Route::get('/packages', [PackageController::class, 'index']);
    Route::get('/available-seat', [PackageController::class, 'availableSeat']);
    Route::post('/book', [BookingController::class, 'bookPackage']);
    Route::get('/user', [AuthController::class, 'me']);
});

