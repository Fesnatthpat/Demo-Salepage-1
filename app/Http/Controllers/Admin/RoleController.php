<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class RoleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $roles = Role::all();
        return view('admin.roles.index', compact('roles'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $availablePermissions = Role::getAvailablePermissions();
        return view('admin.roles.create', compact('availablePermissions'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|unique:roles,name',
            'role_key' => 'nullable|string|unique:roles,role_key',
            'permissions' => 'nullable|array',
        ]);

        $roleKey = $request->role_key ?: Str::slug($request->name);

        Role::create([
            'name' => $request->name,
            'role_key' => $roleKey,
            'permissions' => $request->permissions,
        ]);

        return redirect()->route('admin.roles.index')->with('success', 'ระดับสิทธิ์ถูกสร้างเรียบร้อยแล้ว');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Role $role)
    {
        $availablePermissions = Role::getAvailablePermissions();
        return view('admin.roles.edit', compact('role', 'availablePermissions'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Role $role)
    {
        $request->validate([
            'name' => 'required|string|unique:roles,name,' . $role->id,
            'role_key' => 'required|string|unique:roles,role_key,' . $role->id,
            'permissions' => 'nullable|array',
        ]);

        $role->update([
            'name' => $request->name,
            'role_key' => $request->role_key,
            'permissions' => $request->permissions,
        ]);

        return redirect()->route('admin.roles.index')->with('success', 'แก้ไขระดับสิทธิ์เรียบร้อยแล้ว');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Role $role)
    {
        if ($role->role_key === 'superadmin') {
            return back()->with('error', 'ไม่สามารถลบ Superadmin ได้');
        }

        if ($role->admins()->count() > 0) {
            return back()->with('error', 'ไม่สามารถลบได้เนื่องจากยังมี Admin ใช้งานระดับสิทธิ์นี้อยู่');
        }

        $role->delete();
        return redirect()->route('admin.roles.index')->with('success', 'ลบระดับสิทธิ์เรียบร้อยแล้ว');
    }
}
