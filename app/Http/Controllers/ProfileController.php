<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

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
        ]);

        $user = Auth::user();
        $user->date_of_birth = $request->date_of_birth;
        $user->gender = $request->gender;
        $user->age = $request->age;
        $user->save();

        return redirect('/')->with('success', 'Profile updated successfully!');
    }
}
