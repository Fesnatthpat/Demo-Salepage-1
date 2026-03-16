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
        return Socialite::driver('line')
            ->with(['scope' => 'profile openid email'])
            ->redirect();
    }

    public function handleLineCallback()
    {
        try {
            // 💡 เติม ->stateless() เพื่อปิดการตรวจสอบ State ตอนทดสอบใน Local (แก้ปัญหา Session เด้ง)
            $lineUser = Socialite::driver('line')->stateless()->user();

            // 1. เก็บ Session ตะกร้าสินค้าของ Guest ก่อนที่ Session จะเปลี่ยนหลัง Login
            $guestSessionId = session()->getId();

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

            // 3. ทำการล็อกอินเข้าสู่ระบบของ Laravel
            Auth::login($user);

            // 4. รวมตะกร้าสินค้าจากตอนที่ยังไม่ได้ล็อกอิน เข้ากับบัญชีของผู้ใช้
            $this->cartService->mergeGuestCart($guestSessionId, $user->id);

            // กลับไปยังหน้าแรก หรือหน้าที่ลูกค้าตั้งใจจะไปก่อนถูกบังคับล็อกอิน
            return redirect()->intended('/');

        } catch (\Exception $e) {
            // เก็บ Log เผื่อระบบพัง จะได้ตามไปดูใน storage/logs/laravel.log ได้
            Log::error('LINE Login Error: ' . $e->getMessage());
            
            // 💡 ปรับข้อความแจ้งเตือนให้ดึง Error จริงๆ ออกมาโชว์หน้าเว็บชั่วคราว เราจะได้รู้สาเหตุ
            return redirect('/login')->with('error', 'เกิดข้อผิดพลาด: ' . $e->getMessage());
        }
    }
}