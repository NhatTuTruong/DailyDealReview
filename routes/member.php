<?php

use App\Http\Controllers\Member\AuthController;
use App\Http\Controllers\Member\MemberController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest:member')->prefix('member')->group(function () {
    Route::get('login', [AuthController::class, 'showLoginForm'])->name('member_login');
    Route::post('login', [AuthController::class, 'login']);
});
//
Route::prefix('member')->middleware('member_auth')->group(function () {

    Route::get('/', [MemberController::class, 'index'])->name('member_home');

    Route::get('/profile', [MemberController::class, 'profile'])->name('member_profile');
    Route::post('/profile', [MemberController::class, 'profileUpdate'])->name('member_profile_update');
    Route::post('/change-password', [MemberController::class, 'changePassword'])->name('member_change_password');

//    Route::get('verify-email', EmailVerificationPromptController::class)
//        ->name('verification.notice');
//
//    Route::get('verify-email/{id}/{hash}', VerifyEmailController::class)
//        ->middleware(['signed', 'throttle:6,1'])
//        ->name('verification.verify');
//
//    Route::post('email/verification-notification', [EmailVerificationNotificationController::class, 'store'])
//        ->middleware('throttle:6,1')
//        ->name('verification.send');
//
//    Route::get('confirm-password', [ConfirmablePasswordController::class, 'show'])
//        ->name('password.confirm');
//
//    Route::post('confirm-password', [ConfirmablePasswordController::class, 'store']);
//

    Route::post('logout', [AuthController::class, 'logout'])->name('member_logout');
});
//Route::middleware('member_auth')->group(function () {
//    Route::get('customer.html', [CustomerController::class, 'index'])->name('customer_list');
//    Route::get('dang-ky.html', [CustomerController::class, 'register'])->name('customer_register');
//    Route::post('customer/verify', [CustomerController::class, 'registerVerify'])->name('customer_register_verification');
//    Route::post('customer/save', [CustomerController::class, 'registerSave'])->name('customer_register_save');
//});