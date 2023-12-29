<?php
/**
 * @author Dracowyn
 * @since 2023-12-29 13:58
 */

use App\Http\Controllers\Stock\AdminController;
use Illuminate\Support\Facades\Route;

Route::prefix('admin')->group(function () {
    Route::post('login', [AdminController::class, 'login']);
});
