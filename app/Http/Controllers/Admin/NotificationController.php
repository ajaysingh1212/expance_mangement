<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\UserNotification;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index()
    {
        $notifications = auth()->user()->notifications()->latest()->paginate(20);
        return view('admin.notifications.index', compact('notifications'));
    }

    public function markRead(UserNotification $notification)
    {
        if ($notification->user_id !== auth()->id()) abort(403);
        $notification->markAsRead();
        return back()->with('success', 'Notification marked as read.');
    }

    public function markAllRead()
    {
        auth()->user()->notifications()->whereNull('read_at')->update(['read_at' => now()]);
        return back()->with('success', 'All notifications marked as read.');
    }

    public function destroy(UserNotification $notification)
    {
        if ($notification->user_id !== auth()->id()) abort(403);
        $notification->delete();
        return back()->with('success', 'Notification deleted.');
    }
}
