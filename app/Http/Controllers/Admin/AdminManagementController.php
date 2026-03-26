<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Http\Controllers\Admin\Traits\LogsActivity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class AdminManagementController extends Controller
{
    use LogsActivity;

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $admins = Admin::with('role')->get();

        return view('admin.admins.index', compact('admins'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $roles = \App\Models\Role::orderBy('id', 'asc')->get();
        return view('admin.admins.create', compact('roles'));
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
            'role_id' => 'required|exists:roles,id',
            'is_active' => 'required|boolean',
        ]);

        // Generate unique 6-digit code
        do {
            $code = rand(100000, 999999);
        } while (Admin::where('admin_code', $code)->exists());

        $newAdmin = Admin::create([
            'name' => $request->name,
            'username' => $request->username,
            'password' => Hash::make($request->password),
            'role_id' => $request->role_id,
            'admin_code' => $code,
            'is_active' => $request->is_active,
        ]);

        $this->logActivity($newAdmin, 'created');

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
        $roles = \App\Models\Role::orderBy('id', 'asc')->get();
        return view('admin.admins.edit', compact('admin', 'roles'));
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
            'role_id' => 'required|exists:roles,id',
            'is_active' => 'required|boolean',
        ]);

        $originalData = $admin->toArray();
        $data = $request->only('name', 'username', 'role_id', 'is_active');
        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        // Prevent changing the role of the last superadmin
        $superAdminRole = \App\Models\Role::where('role_key', 'superadmin')->first();
        if ($superAdminRole && $admin->role_id == $superAdminRole->id && $request->role_id != $superAdminRole->id && Admin::where('role_id', $superAdminRole->id)->count() === 1) {
            return redirect()->route('admin.admins.edit', $admin->id)->with('error', 'Cannot change the role of the last superadmin.');
        }

        $admin->update($data);

        $this->logActivity($admin, 'updated', $originalData, $admin->toArray());

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
        $superAdminRole = \App\Models\Role::where('role_key', 'superadmin')->first();
        if ($superAdminRole && $admin->role_id == $superAdminRole->id && Admin::where('role_id', $superAdminRole->id)->count() === 1) {
            return redirect()->route('admin.admins.index')->with('error', 'Cannot delete the last superadmin.');
        }

        $this->logActivity($admin, 'deleted');
        $admin->delete();

        return redirect()->route('admin.admins.index')->with('success', 'Admin deleted successfully.');
    }
}
