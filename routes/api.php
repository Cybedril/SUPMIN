<?php
use App\Modules\Auth\Controllers\AuthController;
use Illuminate\Support\Facades\Route;
use App\Modules\Entity\Controllers\EntityController;
use App\Modules\Mission\Controllers\MissionController;

Route::prefix('v1')->group(function () {

    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/password/reset', [AuthController::class, 'resetPassword']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::get('/me', [AuthController::class, 'me']);
        Route::apiResource('entities', EntityController::class);

        Route::get('/entities', [EntityController::class, 'index']);
    Route::post('/entities', [EntityController::class, 'store']);

    Route::get('/missions', [MissionController::class, 'index']);
    Route::post('/missions', [MissionController::class, 'store']);

    });

});

