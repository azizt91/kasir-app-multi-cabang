<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Http\Request;

class ActivityLogController extends Controller
{
    /**
     * Display a listing of the activity logs.
     */
    public function index(Request $request)
    {
        // Auth Check (already handled by middleware in route, but ensuring admin)
        if (!auth()->check() || auth()->user()->role !== 'admin') {
            abort(403, 'Akses Ditolak.');
        }

        $query = ActivityLog::with(['user', 'branch'])->latest();

        // Filter by User
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        // Filter by Action
        if ($request->filled('action')) {
            $query->where('action', $request->action);
        }

        $logs = $query->paginate(20)->withQueryString();

        // Get users for filter
        // If superadmin, get all users. If branch admin, get users of their branch.
        $usersQuery = User::query();
        if (!auth()->user()->isSuperAdmin()) {
            $usersQuery->where('branch_id', auth()->user()->branch_id);
        }
        $users = $usersQuery->get();

        // Get distinct actions for filter
        $actions = ActivityLog::select('action')->distinct()->pluck('action');

        return view('reports.activity-logs', compact('logs', 'users', 'actions'));
    }
}
