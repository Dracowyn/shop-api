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
});
