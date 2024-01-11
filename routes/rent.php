<?php
/**
 * @author Dracowyn
 * @since 2024-01-09 15:47
 */

use App\Http\Controllers\Rent\Business\BaseController;
use App\Http\Controllers\Rent\HomeController;
use App\Http\Controllers\Shop\Business\EmailController;
use Illuminate\Support\Facades\Route;

Route::prefix('business')->group(function () {
    Route::post('base/register', [BaseController::class, 'register']);
    Route::post('base/login', [BaseController::class, 'login']);
});

Route::prefix('business')->middleware('Auth')->group(function () {
    Route::post('base/index', [BaseController::class, 'index']);
    Route::post('base/profile', [BaseController::class, 'profile']);
    Route::post('email/send', [EmailController::class, 'send']);
    Route::post('email/check', [EmailController::class, 'check']);
});

Route::post('home/index', [HomeController::class, 'index']);
