<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Item;
use App\Models\User;
use App\Models\UserNotification;
use Spatie\Permission\Models\Role;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $stats = [];

        if ($user->isSuperAdmin()) {
            $stats['total_users']   = User::count();
            $stats['total_items']   = Item::count();
            $stats['total_roles']   = Role::count();
            $stats['active_users']  = User::where('is_active', true)->count();
        } elseif ($user->isAdmin()) {
            $myUserIds = $user->createdUsers()->pluck('id')->push($user->id);
            $stats['total_users']   = User::whereIn('id', $myUserIds)->count();
            $stats['total_items']   = Item::whereIn('created_by', $myUserIds)->count();
            $stats['total_roles']   = Role::count();
            $stats['active_users']  = User::whereIn('id', $myUserIds)->where('is_active', true)->count();
        } else {
            $stats['total_items']   = Item::where('created_by', $user->id)->count();
            $stats['active_items']  = Item::where('created_by', $user->id)->where('status', 'active')->count();
            $stats['draft_items']   = Item::where('created_by', $user->id)->where('status', 'draft')->count();
        }

        $recentActivity = ActivityLog::with('user')
            ->when(!$user->isSuperAdmin(), fn($q) => $q->where('user_id', $user->id))
            ->latest()
            ->take(8)
            ->get();

        $recentItems = Item::with('creator')
            ->forUser($user)
            ->latest()
            ->take(5)
            ->get();

        return view('admin.dashboard.index', compact('stats', 'recentActivity', 'recentItems'));
    }
}
