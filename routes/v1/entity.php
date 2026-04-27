<?php

use Illuminate\Support\Facades\Route;
use App\Modules\Entity\Controllers\EntityController;

Route::get('/entities', [EntityController::class, 'index']);

Route::post('/entities', [EntityController::class, 'store'])
    ->middleware('role:admin');

Route::put('/entities/{entity}', [EntityController::class, 'update'])
    ->middleware('role:admin');

Route::delete('/entities/{entity}', [EntityController::class, 'destroy'])
    ->middleware('role:admin');