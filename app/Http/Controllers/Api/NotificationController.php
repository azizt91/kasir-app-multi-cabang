<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    /**
     * Get list of notifications for the authenticated user.
     */
    public function index(Request $request)
    {
        // Paginate notifications (DatabaseNotification model)
        $notifications = $request->user()->notifications()->paginate(20);
        
        return response()->json($notifications);
    }

    /**
     * Get unread count.
     */
    public function unreadCount(Request $request)
    {
        $count = $request->user()->unreadNotifications()->count();
        return response()->json(['count' => $count]);
    }

    /**
     * Mark all as read.
     */
    public function markAllRead(Request $request)
    {
        $request->user()->unreadNotifications->markAsRead();
        return response()->json(['message' => 'All notifications marked as read']);
    }

    /**
     * Clear all notifications.
     */
    public function clearAll(Request $request)
    {
        $request->user()->notifications()->delete();
        return response()->json(['message' => 'All notifications cleared']);
    }
}
