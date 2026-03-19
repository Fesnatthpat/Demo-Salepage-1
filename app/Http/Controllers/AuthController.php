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

            // 1. เก็บ Session ตะกร้าสินค้าของ Guest (ดึงจาก Cookie ถ้า Session ปกติหายไป)
            $guestSessionId = request()->cookie('guest_cart_id') ?: session()->getId();

            // 2. ค้นหาผู้ใช้ด้วย line_id หรือสร้างผู้ใช้ใหม่หากยังไม่เคยสมัคร
            $user = User::updateOrCreate(
                ['line_id' => $lineUser->getId()],
                [
                    'name' => $lineUser->getName(),
                    // 💡 ใส่ Fallback ให้ email เพื่อป้องกัน Error กรณีที่ผู้ใช้ไม่ได้ผูกอีเมลไว้กับ LINE
                    'email' => $lineUser->getEmail() ?? $lineUser->getId() . '@line.me',
                    'avatar' => $lineUser->getAvatar(),
                    // หมายเหตุ: หากฐานข้อมูลของคุณบังคับว่ารหัสผ่าน (password) ห้ามเป็นค่าว่าง 
                    // ให้เอาเครื่องหมาย // ด้านหน้าบรรทัดด้านล่างออกครับ
                    // 'password' => bcrypt(uniqid()), 
                ]
            );

            // 💡 ดึง URL ที่ต้องการไปหลัง Login (ตรวจสอบทั้ง Session และ Cookie สำรอง)
            $intendedUrl = session()->pull('birthday_redirect_url');
            if (!$intendedUrl) {
                $intendedUrl = request()->cookie('birthday_redirect_backup');
            }
            if (!$intendedUrl) {
                $intendedUrl = session()->pull('url.intended');
            }

            // แก้ไขปัญหา ngrok/proxy: บังคับให้ใช้ APP_URL ที่ตั้งค่าไว้ หาก URL เดิมเป็น localhost หรือ 127.0.0.1
            if ($intendedUrl) {
                $parsed = parse_url($intendedUrl);
                $appParsed = parse_url(config('app.url'));
                
                if (isset($parsed['host']) && ($parsed['host'] === '127.0.0.1' || $parsed['host'] === 'localhost')) {
                    $intendedUrl = config('app.url') . ($parsed['path'] ?? '') . (isset($parsed['query']) ? '?' . $parsed['query'] : '');
                }
            }

            // ล้าง Cookie สำรองทั้งหมด
            cookie()->queue(cookie()->forget('birthday_redirect_backup'));
            cookie()->queue(cookie()->forget('guest_cart_id'));

            // 3. ทำการล็อกอินเข้าสู่ระบบของ Laravel
            Auth::login($user);

            // 4. รวมตะกร้าสินค้าจากตอนที่ยังไม่ได้ล็อกอิน เข้ากับบัญชีของผู้ใช้
            $this->cartService->mergeGuestCart($guestSessionId, $user->id);

            // บังคับบันทึก Session ก่อน Redirect
            session()->save();

            // กลับไปยังหน้าที่ลูกค้าตั้งใจจะไป
            return redirect($intendedUrl ?: config('app.url'));

        } catch (\Exception $e) {
            // เก็บ Log เผื่อระบบพัง จะได้ตามไปดูใน storage/logs/laravel.log ได้
            Log::error('LINE Login Error: ' . $e->getMessage());
            
            // 💡 ปรับข้อความแจ้งเตือนให้ดึง Error จริงๆ ออกมาโชว์หน้าเว็บชั่วคราว เราจะได้รู้สาเหตุ
            return redirect('/login')->with('error', 'เกิดข้อผิดพลาด: ' . $e->getMessage());
        }
    }
}