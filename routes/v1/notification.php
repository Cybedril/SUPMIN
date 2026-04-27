<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;

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