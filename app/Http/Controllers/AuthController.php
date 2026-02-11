<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\CartService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;

class AuthController extends Controller
{
    public function __construct(private CartService $cartService) {}

    // ... (ฟังก์ชัน showLogin, logout, redirectToLine เหมือนเดิม) ...
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

    // [จุดที่แก้ไขสำคัญ]
    public function handleLineCallback()
    {
        try {
            $lineUser = Socialite::driver('line')->user();
        } catch (\Exception $e) {
            return redirect('/login')->with('error', 'LINE Login Failed: '.$e->getMessage());
        }

        // 1. Get guest session key BEFORE login changes the session
        $guestSessionKey = '_guest_'.session()->getId();

        // 2. Create or find the user
        $user = User::updateOrCreate(
            ['line_id' => $lineUser->getId()],
            [
                'name' => $lineUser->getName(),
                'email' => $lineUser->getEmail(),
                'avatar' => $lineUser->getAvatar(),
            ]
        );

        // 3. Login the user
        Auth::login($user);

        // 4. Use the CartService to merge the guest cart into the user's cart
        $this->cartService->mergeGuestCart($guestSessionKey, $user->id);

        return redirect()->intended('/');
    }
}
