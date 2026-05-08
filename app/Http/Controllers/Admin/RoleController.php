<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Role;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;

class RoleController extends Controller
{
    public function index()
    {
        $roles = Role::all();
        return view('admin.roles.index', compact('roles'));
    }

    public function create()
    {
        $permissions = Permission::all()->groupBy('module'); // 👈 important

        return view('admin.roles.create', compact('permissions'));
    }

    public function store(Request $request)
    {
        $role = Role::create($request->only('name','slug','description','color','icon'));

        // sync permissions
        if ($request->has('permissions')) {
            $permissions = Permission::whereIn('id', $request->permissions)->pluck('name');
            $role->syncPermissions($permissions);
        }

        return redirect()->route('admin.roles.index');
    }

    public function edit(Role $role)
    {
        $permissions = Permission::all()->groupBy('module'); // 👈 FIX

        $role->load('permissions'); // 👈 important for checkbox checked

        return view('admin.roles.edit', compact('role','permissions'));
    }

    public function update(Request $request, Role $role)
    {
        $role->update($request->only('name','slug','description','color','icon'));

        // update permissions
        if ($request->has('permissions')) {
            $permissions = Permission::whereIn('id', $request->permissions)->pluck('name');
            $role->syncPermissions($permissions);
        } else {
            $role->syncPermissions([]);
        }

        return back();
    }

    public function destroy(Role $role)
    {
        $role->delete();
        return back();
    }
}
