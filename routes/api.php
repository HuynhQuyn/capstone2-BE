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
        Route::get('/list-user', [\App\Http\Controllers\Admin\UserController::class, 'getList']);
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
            Route::post('/check-is-destroy', [\App\Http\Controllers\Admin\ChapterController::class, 'destroy']);

        });

        Route::group(['prefix' => '/lesson'], function () {
            Route::post('/create', [\App\Http\Controllers\Admin\LessonController::class, 'store']);
            Route::post('/get-data', [\App\Http\Controllers\Admin\LessonController::class, 'getData']);
            Route::get('/get-data/{id}', [\App\Http\Controllers\Admin\LessonController::class, 'getDataById']);
            Route::post('/update', [\App\Http\Controllers\Admin\LessonController::class, 'update']);
            Route::get('/destroy/{id}', [\App\Http\Controllers\Admin\LessonController::class, 'destroy']);
            Route::post('/change-position', [\App\Http\Controllers\Admin\LessonController::class, 'changePosition']);
        });

        Route::group(['prefix' => '/class'], function () {
            Route::post('/create', [\App\Http\Controllers\Admin\ClassRoomController::class, 'store']);
            Route::post('/get-data', [\App\Http\Controllers\Admin\ClassRoomController::class, 'getData']);
            Route::get('/get-data/{id}', [\App\Http\Controllers\Admin\ClassRoomController::class, 'getDataById']);
            Route::post('/update', [\App\Http\Controllers\Admin\ClassRoomController::class, 'update']);
            Route::get('/destroy/{id}', [\App\Http\Controllers\Admin\ClassRoomController::class, 'destroy']);
        });

        Route::group(['prefix' => '/class-detail'], function () {
            Route::get('/get-data/{id_class}', [\App\Http\Controllers\Admin\ClassDetailController::class, 'getData']);
            Route::get('/get-data-by-id/{id}', [\App\Http\Controllers\Admin\ClassDetailController::class, 'getDataById']);
            Route::post('/update', [\App\Http\Controllers\Admin\ClassDetailController::class, 'update']);
        });

        Route::get('/list-question', [\App\Http\Controllers\Admin\QuestionController::class, 'getList']);
        Route::group(['prefix' => '/question'], function () {
            Route::post('/create', [\App\Http\Controllers\Admin\QuestionController::class, 'store']);
            Route::post('/get-data', [\App\Http\Controllers\Admin\QuestionController::class, 'getData']);
            Route::get('/get-data/{id}', [\App\Http\Controllers\Admin\QuestionController::class, 'getDataById']);
            Route::post('/update', [\App\Http\Controllers\Admin\QuestionController::class, 'update']);
            Route::get('/destroy/{id}', [\App\Http\Controllers\Admin\QuestionController::class, 'destroy']);
        });

        Route::get('/list-excercise', [\App\Http\Controllers\Admin\ExcerciseController::class, 'getList']);
        Route::get('/list-excercise-offline', [\App\Http\Controllers\Admin\ExcerciseController::class, 'getListOffline']);
        Route::get('/list-excercise-final/{id_cource}', [\App\Http\Controllers\Admin\ExcerciseController::class, 'getListFinalOfCource']);
        Route::group(['prefix' => '/excercise'], function () {
            Route::post('/create', [\App\Http\Controllers\Admin\ExcerciseController::class, 'store']);
            Route::post('/get-data', [\App\Http\Controllers\Admin\ExcerciseController::class, 'getData']);
            Route::get('/get-data/{id}', [\App\Http\Controllers\Admin\ExcerciseController::class, 'getDataById']);
            Route::post('/update', [\App\Http\Controllers\Admin\ExcerciseController::class, 'update']);
            Route::get('/destroy/{id}', [\App\Http\Controllers\Admin\ExcerciseController::class, 'destroy']);
        });
    });

    Route::group(['prefix' => '/user'], function () {
        Route::post('/list-cource', [\App\Http\Controllers\User\HomePageController::class, 'listCource']);
        Route::get('/cource/{id}', [\App\Http\Controllers\User\HomePageController::class, 'courceDetail']);
        Route::get('/list-my-schedule', [\App\Http\Controllers\User\HomePageController::class, 'listSchedule']);
        Route::get('/cource/register/{id_cource}', [\App\Http\Controllers\User\HomePageController::class, 'registerCource']);
        Route::get('/cource/un-register/{id_cource}', [\App\Http\Controllers\User\HomePageController::class, 'unregisterCource']);
        Route::get('/cource/check-register/{id_cource}', [\App\Http\Controllers\User\HomePageController::class, 'checkRegisterCource']);
        Route::get('/cource/register-certificate/{id_cource}', [\App\Http\Controllers\User\HomePageController::class, 'registerCertificate']);


        Route::get('/excercise-offline/{id_cource}/{id_excercise}', [\App\Http\Controllers\User\ExcerciseController::class, 'getExcerciseOfflineByID']);
        Route::get('/list-certificate', [\App\Http\Controllers\User\CertificateController::class, 'getListCertificate']);

    });
});
