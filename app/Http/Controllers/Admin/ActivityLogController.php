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
        // 1. Validate Input เพื่อความปลอดภัย
        $request->validate([
            'admin_id' => 'nullable|integer|exists:admins,id',
        ]);

        // 2. Eager Load ข้อมูลที่จำเป็นเพื่อลด Query (N+1 Problem)
        $query = ActivityLog::with(['admin', 'loggable'])->latest();

        $filter_admin_name = null;

        // 3. Filter Logic
        if ($request->filled('admin_id')) {
            $adminId = $request->input('admin_id');
            $query->where('admin_id', $adminId);

            $admin = Admin::find($adminId);
            if ($admin) {
                $filter_admin_name = $admin->name;
            }
        }

        // 4. Pagination (ใช้ Simple Paginate ถ้าข้อมูลเยอะมากจะเร็วกว่า)
        $activities = $query->paginate(20)->withQueryString();

        return view('admin.activity_log.index', compact('activities', 'filter_admin_name'));
    }
}
