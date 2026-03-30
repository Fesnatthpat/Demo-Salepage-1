<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Admin;
use Illuminate\Http\Request;

class ActivityLogController extends Controller
{
    public function __construct()
    {
        // ต้องมั่นใจว่า Middleware 'is.superadmin' ทำงานถูกต้องใน Kernel.php
        $this->middleware('is.superadmin');
    }

    public function index(Request $request)
    {
        // 1. Eager Load ข้อมูลที่จำเป็น
        $query = ActivityLog::with(['admin', 'loggable'])->latest();

        // 2. Search Logic (Search Admin Name or Description)
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('description', 'like', "%$search%")
                  ->orWhereHas('admin', function($adminQuery) use ($search) {
                      $adminQuery->where('name', 'like', "%$search%");
                  });
            });
        }

        // 3. Filter: Action Type (Created, Updated, Deleted)
        if ($request->filled('action')) {
            $query->where('action', $request->action);
        }

        // 4. Filter: Date
        if ($request->filled('date')) {
            $query->whereDate('created_at', $request->date);
        }

        // 5. Pagination
        $activities = $query->paginate(20)->withQueryString();

        return view('admin.activity_log.index', compact('activities'));
    }
}
