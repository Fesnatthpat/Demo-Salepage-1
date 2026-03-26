<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\VisitorLog;
use Illuminate\Http\Request;

class VisitorLogController extends Controller
{
    /**
     * Display a listing of the visitors.
     */
    public function index(Request $request)
    {
        $query = VisitorLog::with('user')->orderBy('created_at', 'desc');

        // Filter: วันที่ (Default วันนี้)
        if ($request->filled('date')) {
            $query->where('visit_date', $request->date);
        }

        // Filter: ประเภทผู้เข้าชม (Logged-in / Guest)
        if ($request->filled('type')) {
            if ($request->type === 'user') {
                $query->whereNotNull('user_id');
            } elseif ($request->type === 'guest') {
                $query->whereNull('user_id');
            }
        }

        // Filter: Search IP หรือ Path
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('ip_address', 'like', "%$search%")
                  ->orWhere('path', 'like', "%$search%");
            });
        }

        $visitors = $query->paginate(20)->withQueryString();

        // สถิติสรุปเบื้องต้น
        $stats = [
            'total_unique' => VisitorLog::distinct('ip_address')->count(),
            'today_unique' => VisitorLog::where('visit_date', now()->toDateString())->distinct('ip_address')->count(),
            'logged_in'    => VisitorLog::whereNotNull('user_id')->distinct('user_id')->count(),
        ];

        return view('admin.visitors.index', compact('visitors', 'stats'));
    }

    /**
     * Remove the specified visitor log.
     */
    public function destroy(VisitorLog $visitorLog)
    {
        $visitorLog->delete();
        return back()->with('success', 'ลบบันทึกการเข้าชมเรียบร้อยแล้ว');
    }

    /**
     * Clear all logs (Optional)
     */
    public function clearAll()
    {
        VisitorLog::truncate();
        return back()->with('success', 'ล้างข้อมูลการเข้าชมทั้งหมดแล้ว');
    }
}
