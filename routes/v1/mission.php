<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Modules\Mission\Controllers\MissionController;

Route::get('/missions', [MissionController::class, 'index']);

Route::post('/missions', [MissionController::class, 'store'])
    ->middleware('role:admin|superviseur');

Route::put('/missions/{mission}', [MissionController::class, 'update'])
    ->middleware('role:admin|superviseur');

Route::get('/missions/{mission}/pdf', [MissionController::class, 'pdf']);
   

// Mes missions
Route::get('/my-missions', function (Request $request) {

    $missions = $request->user()
        ->missions()
        ->with('entity')
        ->get();

    return response()->json([
        'success' => true,
        'data' => $missions,
        'message' => 'Mes missions',
        'errors' => null
    ]);
});