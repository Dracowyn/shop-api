<?php
/**
 * @author Dracowyn
 * @since 2023-12-29 13:58
 */

use App\Http\Controllers\Stock\AdminController;
use App\Http\Controllers\Stock\Controller;
use App\Http\Controllers\Stock\HighseaController;
use App\Http\Controllers\Stock\PrivateseaController;
use App\Http\Controllers\Stock\SourceController;
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
    Route::post('list', [AdminController::class, 'list']);
});

// 需要登录验证的路由
Route::prefix('manage')->middleware('AdminAuth')->group(function () {
    Route::post('source/index', [SourceController::class, 'index']);
    Route::post('source/add', [SourceController::class, 'add']);
    Route::post('source/del', [SourceController::class, 'del']);
    Route::post('source/info', [SourceController::class, 'info']);
    Route::post('source/edit', [SourceController::class, 'edit']);

    Route::post('highsea/index', [HighseaController::class, 'index']);
    Route::post('highsea/info', [HighseaController::class, 'info']);
    Route::post('highsea/del', [HighseaController::class, 'del']);
    Route::post('highsea/allot', [HighseaController::class, 'allot']);
    Route::post('highsea/apply', [HighseaController::class, 'apply']);

    Route::post('privatesea/index', [PrivateseaController::class, 'index']);
    Route::post('privatesea/info', [PrivateseaController::class, 'info']);
    Route::post('privatesea/del', [PrivateseaController::class, 'del']);
    Route::post('privatesea/recovery', [PrivateseaController::class, 'recovery']);
    Route::post('privatesea/avatar', [PrivateseaController::class, 'avatar']);
    Route::post('privatesea/add', [PrivateseaController::class, 'add']);
});

Route::prefix('controller')->middleware('AdminAuth')->group(function () {
    Route::post('total', [Controller::class, 'total']);
    Route::post('business', [Controller::class, 'business']);
});
