<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Admin;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class AdminManagementController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $admins = Admin::all();
        return view('admin.admins.index', compact('admins'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.admins.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'username' => ['required', 'string', 'max:255', Rule::unique('admins')->whereNull('deleted_at')],
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|in:admin,superadmin',
        ]);

        // Generate unique 6-digit code
        do {
            $code = rand(100000, 999999);
        } while (Admin::where('admin_code', $code)->exists());

        Admin::create([
            'name' => $request->name,
            'username' => $request->username,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'admin_code' => $code,
        ]);

        return redirect()->route('admin.admins.index')->with('success', 'Admin created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Admin $admin)
    {
        // Not used for this resource
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Admin $admin)
    {
        return view('admin.admins.edit', compact('admin'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Admin $admin)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'username' => ['required', 'string', 'max:255', Rule::unique('admins')->ignore($admin->id)->whereNull('deleted_at')],
            'password' => 'nullable|string|min:8|confirmed',
            'role' => 'required|in:admin,superadmin',
        ]);

        $data = $request->only('name', 'username', 'role');
        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        // Prevent changing the role of the last superadmin
        if ($admin->role === 'superadmin' && $request->role !== 'superadmin' && Admin::where('role', 'superadmin')->count() === 1) {
            return redirect()->route('admin.admins.edit', $admin->id)->with('error', 'Cannot change the role of the last superadmin.');
        }

        $admin->update($data);

        return redirect()->route('admin.admins.index')->with('success', 'Admin updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Admin $admin)
    {
        // Prevent deleting the currently logged in user
        if ($admin->id === auth('admin')->id()) {
            return redirect()->route('admin.admins.index')->with('error', 'You cannot delete your own account.');
        }

        // Prevent deleting the last superadmin
        if ($admin->role === 'superadmin' && Admin::where('role', 'superadmin')->count() === 1) {
            return redirect()->route('admin.admins.index')->with('error', 'Cannot delete the last superadmin.');
        }

        $admin->delete();
        return redirect()->route('admin.admins.index')->with('success', 'Admin deleted successfully.');
    }
}
