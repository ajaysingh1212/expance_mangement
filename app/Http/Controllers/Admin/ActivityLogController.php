<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class ActivityLogController extends Controller
{
    use AuthorizesRequests; // ✅ FIX

    public function index(Request $request)
    {
        $this->authorize('activity.index');

        $query = ActivityLog::with('user')->latest();

        if ($request->filled('action')) {
            $query->where('action', $request->action);
        }

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $logs = $query->paginate(20);
        $actions = ActivityLog::distinct()->pluck('action');

        return view('admin.activity.index', compact('logs', 'actions'));
    }

    public function clear()
    {
        $this->authorize('activity.index');

        ActivityLog::where('created_at', '<', now()->subDays(30))->delete();

        return back()->with('success', 'Old activity logs cleared (older than 30 days).');
    }
}
