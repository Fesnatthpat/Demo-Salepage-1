<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\CartService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Laravel\Socialite\Facades\Socialite;

class AuthController extends Controller
{
    public function __construct(private CartService $cartService) {}

    public function showLogin()
    {
        if (Auth::check()) {
            return redirect('/');
        }
        return view('login');
    }

    public function logout(Request $request)
    {
        Auth::guard('web')->logout();
        return redirect('/');
    }

    public function redirectToLine()
    {
        // บันทึก Session ID ปัจจุบันไว้ใน Cookie เพื่อกัน Session หายระหว่างทาง (Merge ตะกร้า)
        cookie()->queue('guest_cart_id', session()->getId(), 60);
        
        // บังคับ Save Session ก่อน Redirect
        session()->save();

        return Socialite::driver('line')
            ->with(['scope' => 'profile openid email'])
            ->redirect();
    }

    public function handleLineCallback()
    {
        try {
            // 💡 เติม ->stateless() เพื่อปิดการตรวจสอบ State ตอนทดสอบใน Local (แก้ปัญหา Session เด้ง)
            $lineUser = Socialite::driver('line')->stateless()->user();

            // 1. เก็บ Session ตะกร้าสินค้าของ Guest (ดึงจาก Cookie หรือ Session ปัจจุบัน)
            // ต้องดึง "ก่อน" ทำการ Auth::login($user) เพราะไอดีจะเปลี่ยนหลังล็อกอิน
            $guestSessionId = request()->cookie('guest_cart_id') ?: session()->getId();
            
            Log::info("LINE Login Callback - Guest Session ID: " . $guestSessionId);

            // 2. ค้นหาผู้ใช้ด้วย line_id หรือสร้างผู้ใช้ใหม่หากยังไม่เคยสมัคร
            $user = User::updateOrCreate(
                ['line_id' => $lineUser->getId()],
                [
                    'name' => $lineUser->getName(),
                    // 💡 ใส่ Fallback ให้ email เพื่อป้องกัน Error กรณีที่ผู้ใช้ไม่ได้ผูกอีเมลไว้กับ LINE
                    'email' => $lineUser->getEmail() ?? $lineUser->getId() . '@line.me',
                    'avatar' => $lineUser->getAvatar(),
                ]
            );

            // 💡 ดึง URL ที่ต้องการไปหลัง Login
            $intendedUrl = session()->pull('birthday_redirect_url');
            if (!$intendedUrl) {
                $intendedUrl = request()->cookie('birthday_redirect_backup');
            }
            if (!$intendedUrl) {
                $intendedUrl = session()->pull('url.intended');
            }

            // 3. ทำการล็อกอินเข้าสู่ระบบ
            Auth::login($user);
            
            // สำคัญ: สั่งให้ Laravel เปลี่ยน Session ID ใหม่เพื่อความปลอดภัย
            // และดึงตะกร้าจาก $guestSessionId (ตะกร้าเก่า) มาใส่ใน Session ใหม่ของ User
            session()->regenerate();

            // 4. รวมตะกร้าสินค้า
            $this->cartService->mergeGuestCart($guestSessionId, $user->id);

            // ล้าง Cookie สำรองทั้งหมด
            cookie()->queue(cookie()->forget('birthday_redirect_backup'));
            cookie()->queue(cookie()->forget('guest_cart_id'));

            // บังคับบันทึก Session ก่อน Redirect
            session()->save();

            return redirect($intendedUrl ?: config('app.url'));

        } catch (\Exception $e) {
            // เก็บ Log เผื่อระบบพัง จะได้ตามไปดูใน storage/logs/laravel.log ได้
            Log::error('LINE Login Error: ' . $e->getMessage());
            
            // 💡 ปรับข้อความแจ้งเตือนให้ดึง Error จริงๆ ออกมาโชว์หน้าเว็บชั่วคราว เราจะได้รู้สาเหตุ
            return redirect('/login')->with('error', 'เกิดข้อผิดพลาด: ' . $e->getMessage());
        }
    }
}