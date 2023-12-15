<?php
/**
 * @author Dracowyn
 * @since 2023-12-12 15:09
 */


use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Shop\Business\BaseController;
use App\Http\Controllers\Shop\Business\AddressController;

Route::prefix('business')->group(function () {
    Route::post('base/register', [BaseController::class, 'register']);
    Route::post('base/login', [BaseController::class, 'login']);
    Route::post('base/check', [BaseController::class, 'check']);
});

// 验证用户登录后才能操作的路由
Route::prefix('business')->middleware('Auth')->group(function () {
    Route::post('base/profile', [BaseController::class, 'profile']);

    // 收货地址的路由
    Route::post('address/index', [AddressController::class, 'index']);
    Route::post('address/add', [AddressController::class, 'add']);
    Route::post('address/info', [AddressController::class, 'info']);
    Route::post('address/edit', [AddressController::class, 'edit']);
    Route::post('address/del', [AddressController::class, 'del']);
    Route::post('address/selected', [AddressController::class, 'selected']);
});
