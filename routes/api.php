<?php

use App\Http\Controllers\PostController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::prefix('user')->group(function () {
    Route::post('login', UserController::class . '@login');
    Route::prefix('posts')
        ->middleware('auth:sanctum')
        ->group(function () {
            Route::post('search', PostController::class . '@userSearch');
            Route::post('/', PostController::class . '@store');
            Route::put('{post}', PostController::class . '@update');
            Route::delete('{post}', PostController::class . '@destroy');
        });
});

Route::prefix('posts')->group(function () {
    Route::post('search', PostController::class . '@search');
});
