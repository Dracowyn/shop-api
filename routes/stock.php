<?php
/**
 * @author Dracowyn
 * @since 2023-12-29 13:58
 */

use App\Http\Controllers\Stock\AdminController;
use App\Http\Controllers\Stock\Controller;
use App\Http\Controllers\Stock\HighseaController;
use App\Http\Controllers\Stock\PrivateseaController;
use App\Http\Controllers\Stock\ProductController;
use App\Http\Controllers\Stock\ReceiveController;
use App\Http\Controllers\Stock\RecycleseaController;
use App\Http\Controllers\Stock\SourceController;
use App\Http\Controllers\Stock\SubjectController;
use App\Http\Controllers\Stock\VisitController;
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
    Route::post('privatesea/edit', [PrivateseaController::class, 'edit']);

    Route::post('receive/index', [ReceiveController::class, 'index']);

    Route::post('visit/index', [VisitController::class, 'index']);
    Route::post('visit/business', [VisitController::class, 'business']);
    Route::post('visit/add', [VisitController::class, 'add']);
    Route::post('visit/del', [VisitController::class, 'del']);
    Route::post('visit/info', [VisitController::class, 'info']);
    Route::post('visit/edit', [VisitController::class, 'edit']);

    Route::post('recyclesea/index', [RecycleseaController::class, 'index']);
    Route::post('recyclesea/info', [RecycleseaController::class, 'info']);
    Route::post('recyclesea/recover', [RecycleseaController::class, 'recover']);
    Route::post('recyclesea/del', [RecycleseaController::class, 'del']);

    Route::post('subject/index', [SubjectController::class, 'index']);

    Route::post('product/index', [ProductController::class, 'index']);
});

Route::prefix('controller')->middleware('AdminAuth')->group(function () {
    Route::post('total', [Controller::class, 'total']);
    Route::post('business', [Controller::class, 'business']);
});
