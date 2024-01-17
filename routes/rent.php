<?php
/**
 * @author Dracowyn
 * @since 2024-01-09 15:47
 */

use App\Http\Controllers\Rent\Business\BaseController;
use App\Http\Controllers\Rent\CategoryController;
use App\Http\Controllers\Rent\HomeController;
use App\Http\Controllers\Rent\Product\ProductController;
use App\Http\Controllers\Shop\Business\EmailController;
use App\Http\Controllers\Shop\Business\RecordController;
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

    // 消费记录的路由
    Route::post('record/index', [RecordController::class, 'index']);
});

Route::post('home/index', [HomeController::class, 'index']);

Route::prefix('category')->group(function () {
    Route::post('hot', [CategoryController::class, 'hot']);
    Route::post('index', [CategoryController::class, 'index']);
    Route::post('info', [CategoryController::class, 'info']);
});

Route::prefix('category')->middleware('Auth')->group(function () {
    Route::post('collection', [CategoryController::class, 'collection']);
});

Route::prefix('product')->group(function () {
    Route::post('index', [ProductController::class, 'index']);
});
