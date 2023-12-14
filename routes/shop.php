<?php
/**
 * @author Dracowyn
 * @since 2023-12-12 15:09
 */


use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Shop\Business\BaseController;

Route::prefix('business')->group(function () {
    Route::post('base/register', [BaseController::class, 'register']);
    Route::post('base/login', [BaseController::class, 'login']);
    Route::post('base/check', [BaseController::class, 'check']);
});

// 验证用户登录后才能操作的路由
Route::prefix('business')->middleware('Auth')->group(function () {
    Route::post('base/profile', [BaseController::class, 'profile']);
});
