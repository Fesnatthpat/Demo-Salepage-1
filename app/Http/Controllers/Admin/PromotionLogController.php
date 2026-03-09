<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PromotionUsageLog;
use Illuminate\Http\Request;

class PromotionLogController extends Controller
{
    public function index(Request $request)
    {
        $query = PromotionUsageLog::with(['promotion', 'order', 'user'])
            ->latest();

        // กรองตามรหัสส่วนลด
        if ($request->filled('code')) {
            $query->where('code_used', 'like', '%' . $request->code . '%');
        }

        // กรองตามชื่อลูกค้า
        if ($request->filled('customer')) {
            $query->whereHas('user', function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->customer . '%');
            });
        }

        // กรองตามชื่อโปรโมชั่น
        if ($request->filled('promotion')) {
            $query->whereHas('promotion', function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->promotion . '%');
            });
        }

        $logs = $query->paginate(20);

        return view('admin.promotions.logs', compact('logs'));
    }
}
