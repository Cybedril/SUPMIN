<?php

use App\Modules\Auth\Controllers\AuthController;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Modules\Entity\Controllers\EntityController;
use App\Modules\Mission\Controllers\MissionController;
use App\Modules\Mission\Models\Mission;
use App\Models\User;

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

        Route::put('/entities/{entity}', [EntityController::class, 'update'])
            ->middleware('role:admin');

        Route::delete('/entities/{entity}', [EntityController::class, 'destroy'])
            ->middleware('role:admin');

        // Missions
        Route::get('/missions', [MissionController::class, 'index']);

        Route::post('/missions', [MissionController::class, 'store'])
            ->middleware('role:admin|superviseur');

        Route::put('/missions/{mission}', [MissionController::class, 'update'])
            ->middleware('role:admin|superviseur');

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

        // Notifications
        Route::get('/notifications', function (Request $request) {
            return response()->json([
                'success' => true,
                'data' => $request->user()->notifications,
                'message' => 'Liste des notifications',
                'errors' => null
            ]);
        });

        // Notifications non lues
        Route::get('/notifications/unread', function (Request $request) {
            return response()->json([
                'success' => true,
                'data' => $request->user()->unreadNotifications,
                'message' => 'Notifications non lues',
                'errors' => null
            ]);
        });

        // Marquer une notification comme lue
        Route::post('/notifications/{id}/read', function (Request $request, $id) {

            $notification = $request->user()
                ->notifications()
                ->where('id', $id)
                ->first();

            if (!$notification) {
                return response()->json([
                    'success' => false,
                    'data' => null,
                    'message' => 'Notification introuvable',
                    'errors' => null
                ], 404);
            }

            $notification->markAsRead();

            return response()->json([
                'success' => true,
                'data' => null,
                'message' => 'Notification marquée comme lue',
                'errors' => null
            ]);
        });

        // Tout marquer comme lu
        Route::post('/notifications/read-all', function (Request $request) {

            $request->user()->unreadNotifications->markAsRead();

            return response()->json([
                'success' => true,
                'data' => null,
                'message' => 'Toutes les notifications sont marquées comme lues',
                'errors' => null
            ]);
        });

        // Dashboard
        Route::get('/dashboard', function (Request $request) {

            $user = $request->user();

            return response()->json([
                'success' => true,
                'data' => [
                    'missions_total' => Mission::count(),

                    'missions_by_status' => [
                        'pending' => Mission::where('status', 'PENDING')->count(),
                        'in_progress' => Mission::where('status', 'IN_PROGRESS')->count(),
                        'completed' => Mission::where('status', 'COMPLETED')->count(),
                        'cancelled' => Mission::where('status', 'CANCELLED')->count(),
                    ],

                    'agents_total' => User::role('agent')->count(),

                    'my_missions_count' => $user->missions()->count(),

                    'my_role' => $user->getRoleNames(),
                ],
                'message' => 'Dashboard',
                'errors' => null
            ]);
        });

    });

});