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

        // 📊 คำนวณสถิติเพิ่มเติมสำหรับการวิเคราะห์
        $statsBaseQuery = VisitorLog::query();
        if ($request->filled('date')) {
            $statsBaseQuery->where('visit_date', $request->date);
        }

        // 1. สรุปหน้าที่เข้าชมมากที่สุด (Top Pages)
        $topPages = (clone $statsBaseQuery)
            ->select('path', \DB::raw('count(*) as total_visits'))
            ->groupBy('path')
            ->orderByDesc('total_visits')
            ->take(5)
            ->get();

        // 2. วิเคราะห์สินค้าที่คนเข้าชมมากที่สุด (Top Products)
        // สมมติว่า path สินค้าคือ products/{slug} หรือ products/{id}
        $topProducts = (clone $statsBaseQuery)
            ->where('path', 'like', 'product/%')
            ->select('path', \DB::raw('count(*) as total_views'))
            ->groupBy('path')
            ->orderByDesc('total_views')
            ->take(5)
            ->get();

        // 3. วิเคราะห์อุปกรณ์ (Simple Device Detection)
        $uaData = (clone $statsBaseQuery)->pluck('user_agent');
        $devices = [
            'mobile' => 0,
            'desktop' => 0
        ];
        foreach ($uaData as $ua) {
            if (preg_match('/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|mobile.+firefox|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows ce|xda|xiino/i', $ua)) {
                $devices['mobile']++;
            } else {
                $devices['desktop']++;
            }
        }

        $visitors = $query->paginate(20)->withQueryString();

        // สถิติสรุปเบื้องต้น
        $stats = [
            'total_unique' => VisitorLog::distinct('ip_address')->count(),
            'today_unique' => VisitorLog::where('visit_date', now()->toDateString())->distinct('ip_address')->count(),
            'logged_in'    => VisitorLog::whereNotNull('user_id')->distinct('user_id')->count(),
            'top_pages'    => $topPages,
            'top_products' => $topProducts,
            'devices'      => $devices
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
