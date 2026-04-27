<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Modules\Mission\Models\Mission;
use App\Models\User;

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