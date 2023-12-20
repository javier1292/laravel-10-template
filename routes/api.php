<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\VerificationController;
use Illuminate\Support\Facades\Route;

// v1 prefix
Route::prefix('api/v1')->group(function () {
    // Public routes
    Route::controller(AuthController::class)->group(function () {
        Route::post('/login', 'login');
        Route::post('/register', 'register');
    });

    Route::controller(VerificationController::class)->group(function () {
        Route::get('/email/verify/{id}/{hash}', 'verify')->name('verification.verify');
        Route::post('/forgot-password', 'forgotPassword')->name('password.forgot');
        Route::post('/reset-password', 'resetPassword')->name('password.reset');
    });

    // Protected routes
    Route::group(['middleware' => ['auth:sanctum']], function () {

        Route::controller(AuthController::class)->group(function () {
            Route::post('/logout', 'logout');
            Route::put('/profile/password', 'changePassword');
        });

        Route::controller(ProfileController::class)->group(function () {
            Route::post('/profile/avatar', 'uploadAvatar');
        });
    });
});
