<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use Symfony\Component\HttpFoundation\Response;

class HandleProxiedRequests
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // 1. ตรวจสอบว่าใช้งานผ่าน Proxy (เช่น ngrok หรือ Load Balancer) หรือ HTTPS หรือไม่
        // หรือมีการตั้งค่า FORCE_HTTPS=true ใน .env
        $isForwardedHttps = $request->header('X-Forwarded-Proto') === 'https';
        $isSecure = $request->secure();
        
        // ตรวจสอบว่า APP_URL เริ่มต้นด้วย https:// หรือไม่
        $appUrl = config('app.url');
        $isHttpsConfigured = str_starts_with($appUrl, 'https://');

        if ($isForwardedHttps || $isSecure || $isHttpsConfigured) {
            // บังคับให้ใช้ HTTPS ในการสร้าง URL ทั้งหมด (เช่น route(), url(), action())
            URL::forceScheme('https');
            
            // บังคับให้ใช้ APP_URL ที่ตั้งไว้ใน .env เป็น Root URL
            // เพื่อป้องกันปัญหาการ Redirect ไปยัง Host ภายใน (เช่น localhost หรือ 127.0.0.1)
            if ($appUrl && !str_contains($appUrl, 'localhost') && !str_contains($appUrl, '127.0.0.1')) {
                URL::forceRootUrl($appUrl);
            }
        }

        return $next($request);
    }
}
