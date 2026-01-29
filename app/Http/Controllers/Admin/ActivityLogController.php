<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Admin;
use Illuminate\Http\Request;

class ActivityLogController extends Controller
{
    /**
     * Instantiate a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('is.superadmin');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $query = ActivityLog::with(['admin', 'loggable'])->latest();
        $filter_admin_name = null;

        if ($request->has('admin_id')) {
            $admin_id = $request->input('admin_id');
            $query->where('admin_id', $admin_id);
            $admin = Admin::find($admin_id);
            if ($admin) {
                $filter_admin_name = $admin->name;
            }
        }

        $activities = $query->paginate(20)->withQueryString();

        return view('admin.activity_log.index', compact('activities', 'filter_admin_name'));
    }
}
