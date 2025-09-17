<?php

namespace App\Http\Controllers;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationsController extends Controller
{
    // استرجاع جميع الإشعارات (مقروءة وغير مقروءة)
    public function getNotifications()
    {
        $user = Auth::user();

        return response()->json([
            'unread' => $user->unreadNotifications
        ]);
    }

    // وضع الإشعار كمقروء
    public function markAsRead($notificationId)
    {
        $user = Auth::user();
        $notification = $user->notifications()->find($notificationId);

        if ($notification) {
            $notification->markAsRead();
            return response()->json(['message' => 'notification has been marked as read']);
        }

        return response()->json(['message' => 'notification not found'], 404);
    }


}
