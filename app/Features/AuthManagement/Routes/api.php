<?php

use App\Facades\WhatsApp;
use App\Features\AuthManagement\Controllers\TokenController;
use App\Features\AuthManagement\Controllers\ChangePasswordController;
use App\Features\AuthManagement\Controllers\ForgotPasswordController;
use App\Features\AuthManagement\Controllers\MagicLoginController;
use App\Features\AuthManagement\Controllers\ResetPasswordController;
use App\Features\AuthManagement\Controllers\ProfileController;
use App\Features\AuthManagement\Controllers\SignUpController;
use Illuminate\Support\Facades\Route;

// login

Route::prefix('login')->group(function () {
    Route::post('/', [TokenController::class, 'login']);
    // magic login
    Route::get('magic/{token}/verify', [MagicLoginController::class, 'verify']);
});
// magic login
Route::post('magic/request', [MagicLoginController::class, 'requestLink']);

// sign up
Route::post('sign-up', [SignUpController::class, 'signUp']);

// forgot password
Route::post('forgot-password', [ForgotPasswordController::class, 'forgotPassword']);
Route::post('forgot-verify-code', [ForgotPasswordController::class, 'forgotVerifyCode']);
// reset password
Route::post('reset-password', [ResetPasswordController::class, 'resetPassword']);
Route::middleware('auth:sanctum')->group(function () {
    // logout
    Route::post('logout', [TokenController::class, 'logout']);
    // change password
    Route::put('change-password', [ChangePasswordController::class, 'changePassword']);

    // get auth user profile
    Route::get('auth/profile', [ProfileController::class, 'show']);
    // update auth user profile
    Route::put('auth/profile', [ProfileController::class, 'update']);
    // become provider
    Route::put('auth/become-provider', [ProfileController::class, 'becomeProvider']);
});




