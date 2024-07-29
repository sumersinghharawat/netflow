<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
class NotificationController extends CoreInfController
{

    public function readSingleNotification(Request $request)
    {
        $user = auth()->user();
        $notification = $user->unreadNotifications()->where('id', $request->id)->first();
        return $notification->delete();
    }

    public function readAllNotification()
    {
        $user = auth()->user();
        $user->unreadNotifications->markAsRead();
        return $user->notifications()->delete();
    }
    public function getNotificationsMOre(Request $request)
    {
        $user = auth()->user();
        $notifications = $user->unreadNotifications()->latest()->paginate(5, ['*'], 'page', $request->input('page'));
        // dd($notifications);
        return response()->json([
            'html' => view('layouts.inc.notifications', compact('notifications'))->render(),
            'current_page' => $notifications->currentPage(),
            'next_page_url' => $notifications->nextPageUrl(),
        ]);
    }
}
