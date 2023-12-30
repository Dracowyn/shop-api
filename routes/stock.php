<?php
/**
 * @author Dracowyn
 * @since 2023-12-29 13:58
 */

use App\Http\Controllers\Stock\AdminController;
use Illuminate\Support\Facades\Route;

Route::prefix('admin')->group(function () {
    Route::post('login', [AdminController::class, 'login']);
    Route::post('login2', [AdminController::class, 'login2']);
    Route::post('bind', [AdminController::class, 'bind']);
});

// 需要登录验证的路由
Route::prefix('admin')->middleware('AdminAuth')->group(function () {
    Route::post('unbind', [AdminController::class, 'unbind']);
    Route::post('avatar', [AdminController::class, 'avatar']);
    Route::post('profile', [AdminController::class, 'profile']);
});
