<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\VisitorLog;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class TrackVisitor
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // ข้ามการบันทึกเฉพาะหน้าหลังบ้าน (Admin Dashboard) และ API
        if ($request->is('admin*') || $request->is('api*') || $request->is('build*') || $request->is('images*') || $request->ajax()) {
            return $next($request);
        }

        $ipAddress = $request->ip();
        $visitDate = now()->toDateString();
        // ถ้าเป็นหน้าหลักให้เก็บเป็นค่าว่าง หรือ / ให้เป็นมาตรฐานเดียวกัน
        $path = $request->path() == '/' ? '' : $request->path();
        
        // เช็คทั้ง User ปกติ และ Admin ที่กำลังดูหน้าบ้าน
        $userId = Auth::check() ? Auth::id() : null;

        // บันทึกเฉพาะครั้งแรกที่เจอ IP นี้ + Path นี้ในวันนี้
        $exists = VisitorLog::where('ip_address', $ipAddress)
                           ->where('visit_date', $visitDate)
                           ->where('path', (string)$path)
                           ->where('user_id', $userId)
                           ->exists();

        if (!$exists) {
            VisitorLog::create([
                'ip_address' => $ipAddress,
                'user_agent' => $request->userAgent(),
                'user_id'    => $userId,
                'path'       => (string)$path,
                'visit_date' => $visitDate
            ]);
        }

        return $next($request);
    }
}
