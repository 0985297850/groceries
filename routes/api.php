<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\BannerController;
use App\Http\Controllers\Api\UserController;
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

Route::group(['middleware' => 'api', 'prefix' => '/v1/auth'], function () {
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/logout', [AuthController::class, 'logout']);
});

Route::group(['middleware' => 'jwt.auth', 'prefix' => 'v1'], function () {
    Route::controller(UserController::class)->group(function () {
        Route::prefix('user')->group(function () {
            Route::get('/profile', 'index');
            Route::post('update', 'update');
        });
    });
});

Route::group(['middleware' => 'jwt.auth', 'prefix' => 'v1'], function () {
    Route::controller(BannerController::class)->group(function () {
        Route::prefix('banner')->group(function () {
            Route::get('index', 'index');
            Route::post('create', 'create');
            Route::get('update/{id}', 'update');
            Route::get('delete/{id}', 'delete');
        });
    });
});
