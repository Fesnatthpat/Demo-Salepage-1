<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{
    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('profile.completion');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'date_of_birth' => 'required|date',
            'gender' => 'required|string|in:male,female,other',
            'age' => 'required|integer|min:1',
            'phone' => 'required|string|max:20',
        ]);

        $user = Auth::user();
        $user->date_of_birth = $request->date_of_birth;
        $user->gender = $request->gender;
        $user->age = $request->age;
        $user->phone = $request->phone;
        $user->save();

        return redirect('/')->with('success', 'Profile updated successfully!');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit()
    {
        $user = Auth::user();
        return view('profile.edit', compact('user'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'date_of_birth' => 'nullable|date',
            'gender' => 'nullable|string|in:male,female,other',
            'age' => 'nullable|integer|min:1',
            'phone' => 'required|string|max:20',
        ]);

        $user = Auth::user();
        $user->name = $request->name;
        $user->date_of_birth = $request->date_of_birth;
        $user->gender = $request->gender;
        $user->age = $request->age;
        $user->phone = $request->phone;
        $user->save();

        return redirect()->route('profile.edit')->with('success', 'บันทึกข้อมูลส่วนตัวเรียบร้อยแล้ว');
    }
}
