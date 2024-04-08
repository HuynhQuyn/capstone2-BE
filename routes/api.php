<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
Route::post('/login', [\App\Http\Controllers\AuthController::class, 'actionLogin']);

Route::group(['middleware' => 'jwt'], function ($router) {
    Route::get('/me', [\App\Http\Controllers\AuthController::class, 'getDataUser']);
    Route::get('/logout', [\App\Http\Controllers\AuthController::class, 'actionLogout']);

    Route::group(['prefix' => '/admin'], function () {
        Route::group(['prefix' => '/user'], function () {
            Route::post('/create', [\App\Http\Controllers\Admin\UserController::class, 'store']);
            Route::post('/get-data', [\App\Http\Controllers\Admin\UserController::class, 'getData']);
            Route::get('/update-status/{id}', [\App\Http\Controllers\Admin\UserController::class, 'updateStatus']);
            Route::get('/destroy/{id}', [\App\Http\Controllers\Admin\UserController::class, 'destroy']);
        });

        Route::group(['prefix' => '/cource'], function () {
            Route::post('/create', [\App\Http\Controllers\Admin\CourceController::class, 'store']);
            Route::post('/get-data', [\App\Http\Controllers\Admin\CourceController::class, 'getData']);
            Route::get('/get-data/{id}', [\App\Http\Controllers\Admin\CourceController::class, 'getDataById']);
            Route::post('/update', [\App\Http\Controllers\Admin\CourceController::class, 'update']);
            Route::get('/update-status/{id}', [\App\Http\Controllers\Admin\CourceController::class, 'updateStatus']);
            Route::get('/destroy/{id}', [\App\Http\Controllers\Admin\CourceController::class, 'destroy']);
        });

        Route::group(['prefix' => '/chapter'], function () {
            Route::post('/create', [\App\Http\Controllers\Admin\ChapterController::class, 'store']);
            Route::get('/get-data/{id}', [\App\Http\Controllers\Admin\ChapterController::class, 'getData']);
            Route::post('/update', [\App\Http\Controllers\Admin\ChapterController::class, 'update']);
        });
    });
});
