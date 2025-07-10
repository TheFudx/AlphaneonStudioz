<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\SubscriptionPackageController;
use App\Http\Controllers\API\ForgotPasswordController;
use App\Http\Controllers\API\InAppPurchaseSuccessController;
use App\Http\Controllers\API\ProfileUpdateController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('/api/user', function () {
    return app('logged-in-user');
  })->middleware('auth:api');

  Route::get('/package-details/packages', [SubscriptionPackageController::class, 'packageData'])->name('packageData');
Route::post('/delete-user-account', [ProfileUpdateController::class, 'deleteUserAccount'])->name('deletAc');
Route::post('/in-app-purchase-success', [InAppPurchaseSuccessController::class, 'success'])->name('inApp.success');

Route::post('/send-otp-on-mail',[ForgotPasswordController::class,'sendOtpOnMail']);
Route::post('/verify-otp',[ForgotPasswordController::class,'verifyOtp']);
Route::post('/update-password',[ForgotPasswordController::class,'updatePassword']);