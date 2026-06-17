<?php

namespace App\Modules\Notification\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;

class NotificationController extends Controller
{
    /**
     * GET /notifications — Liste les notifications de l'utilisateur connecté
     */
    public function index(Request $request)
    {
        // Forcer la locale française pour diffForHumans()
        Carbon::setLocale('fr');

        $notifications = $request->user()
            ->notifications()
            ->latest()
            ->get()
            ->map(function ($n) {
                return [
                    'id'         => $n->id,
                    'type'       => $n->data['type'] ?? 'systeme',
                    'titre'      => $n->data['titre'] ?? 'Notification',
                    'message'    => $n->data['message'] ?? '',
                    'lu'         => !is_null($n->read_at),
                    'temps'      => $n->created_at->diffForHumans(),
                    'created_at' => $n->created_at,
                    'data'       => $n->data,
                ];
            });

        return response()->json([
            'success' => true,
            'data'    => $notifications,
            'unread'  => $notifications->where('lu', false)->count(),
            'message' => 'Notifications',
            'errors'  => null
        ]);
    }

    /**
     * PATCH /notifications/{id}/read
     */
    public function markRead($id)
    {
        $notification = auth()->user()->notifications()->findOrFail($id);
        $notification->markAsRead();

        return response()->json([
            'success' => true,
            'data'    => null,
            'message' => 'Notification lue',
            'errors'  => null
        ]);
    }

    /**
     * PATCH /notifications/read-all
     */
    public function markAllRead(Request $request)
    {
        $request->user()->unreadNotifications->markAsRead();

        return response()->json([
            'success' => true,
            'data'    => null,
            'message' => 'Toutes les notifications marquées comme lues',
            'errors'  => null
        ]);
    }

    /**
     * DELETE /notifications/{id}
     */
    public function destroy($id)
    {
        $notification = auth()->user()->notifications()->findOrFail($id);
        $notification->delete();

        return response()->json([
            'success' => true,
            'data'    => null,
            'message' => 'Notification supprimée',
            'errors'  => null
        ]);
    }
}