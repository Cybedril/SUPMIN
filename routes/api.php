<?php
use App\Modules\Auth\Controllers\AuthController;
use Illuminate\Support\Facades\Route;
use App\Modules\Entity\Controllers\EntityController;
use App\Modules\Mission\Controllers\MissionController;

Route::prefix('v1')->group(function () {

    // Auth
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/password/reset', [AuthController::class, 'resetPassword']);

    Route::middleware('auth:sanctum')->group(function () {

        Route::post('/logout', [AuthController::class, 'logout']);
        Route::get('/me', [AuthController::class, 'me']);

        // Entities
        Route::get('/entities', [EntityController::class, 'index']);
        Route::post('/entities', [EntityController::class, 'store'])
            ->middleware('role:admin');

        // Missions
        Route::get('/missions', [MissionController::class, 'index']);
        Route::post('/missions', [MissionController::class, 'store'])
            ->middleware('role:admin|superviseur');
    });

});

