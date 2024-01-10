<?php
/**
 * @author Dracowyn
 * @since 2024-01-09 15:47
 */

use App\Http\Controllers\Rent\Business\BaseController;
use Illuminate\Support\Facades\Route;

Route::prefix('business')->group(function () {
    Route::post('base/register', [BaseController::class, 'register']);
    Route::post('base/login', [BaseController::class, 'login']);
});

Route::prefix('business')->middleware('Auth')->group(function () {
    Route::post('base/profile', [BaseController::class, 'profile']);
});
