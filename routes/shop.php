<?php
/**
 * @author Dracowyn
 * @since 2023-12-12 15:09
 */


use App\Http\Controllers\Shop\Business\CollectionController;
use App\Http\Controllers\Shop\Business\EmailController;
use App\Http\Controllers\Shop\Business\RecordController;
use App\Http\Controllers\Shop\HomeController;
use App\Http\Controllers\Shop\Product\CartController;
use App\Http\Controllers\Shop\Product\OrderController;
use App\Http\Controllers\Shop\Product\ProductController;
use App\Http\Controllers\Shop\Product\TypeController;
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
    Route::post('address/default', [AddressController::class, 'default']);

    // 邮箱验证码的路由
    Route::post('email/send', [EmailController::class, 'send']);
    Route::post('email/check', [EmailController::class, 'check']);

    // 收藏商品的路由
    Route::post('collection/index', [CollectionController::class, 'index']);

    // 消费记录的路由
    Route::post('record/index', [RecordController::class, 'index']);
});

// 不带前缀且不需要验证的路由
Route::post('index/index', [HomeController::class, 'index']);
Route::post('index/new', [HomeController::class, 'new']);
Route::post('index/hot', [HomeController::class, 'hot']);

// 商品相关的路由，不需要验证
Route::prefix('product')->group(function () {
    Route::post('product/index', [ProductController::class, 'index']);
    Route::post('product/info', [ProductController::class, 'info']);
//    Route::post('product/type', [ProductController::class, 'type']);
//    Route::post('product/search', [ProductController::class, 'search']);

});

// 商品相关的路由，需要验证
Route::prefix('product')->middleware('Auth')->group(function () {
    // 收藏商品
    Route::post('product/collection', [ProductController::class, 'collect']);

    // 购物车相关的路由
    Route::post('cart/index', [CartController::class, 'index']);
    Route::post('cart/add', [CartController::class, 'add']);
    Route::post('cart/update', [CartController::class, 'update']);
    Route::post('cart/del', [CartController::class, 'del']);
    Route::post('cart/info', [CartController::class, 'info']);

    // 订单相关的路由
    Route::post('order/create', [OrderController::class, 'create']);
    Route::post('order/index', [OrderController::class, 'index']);
    Route::post('order/info', [OrderController::class, 'info']);
    Route::post('order/pay', [OrderController::class, 'pay']);
    Route::post('order/cancel', [OrderController::class, 'cancel']);
    Route::post('order/rejected', [OrderController::class, 'rejected']);
    Route::post('order/confirm', [OrderController::class, 'confirm']);
});

// 商品分类相关的路由，不需要验证
Route::prefix('type')->group(function () {
    Route::post('index', [TypeController::class, 'index']);
    Route::post('product', [TypeController::class, 'product']);
});
