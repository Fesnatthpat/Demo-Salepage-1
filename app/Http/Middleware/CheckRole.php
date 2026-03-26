<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string[]  ...$roles
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        $admin = auth('admin')->user();

        if (! $admin || ! $admin->role) {
            return redirect()->route('admin.login')->with('error', 'กรุณาเข้าสู่ระบบ');
        }

        // Check if the admin's role key is in the allowed roles
        if (! in_array($admin->role->role_key, $roles)) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['message' => 'คุณไม่มีสิทธิ์เข้าถึงส่วนนี้'], 403);
            }

            return redirect()->route('admin.dashboard')
                ->with('error', 'คุณไม่มีสิทธิ์เข้าถึงส่วนนี้ (เฉพาะ '.implode(', ', $roles).' เท่านั้น)');
        }

        return $next($request);
    }
}
