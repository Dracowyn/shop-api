<?php
/**
 * @author Dracowyn
 * @since 2023-12-12 15:09
 */


use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Shop\Business\BaseController;

Route::prefix('business')->group(function () {
    Route::post('register', [BaseController::class, 'register']);
});
