<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;

class PermissionController extends Controller
{
    public function index()
    {
        $permissions = Permission::orderBy('module')->orderBy('group')->get()->groupBy('module');
        return view('admin.permissions.index', compact('permissions'));
    }

    public function create()
    {
        $modules = Permission::distinct()->pluck('module')->filter()->sort()->values();
        return view('admin.permissions.create', compact('modules'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'        => 'required|string|max:100|unique:permissions',
            'module'      => 'required|string|max:50',
            'group'       => 'required|string|max:50',
            'description' => 'nullable|string|max:255',
        ]);

        $permission = Permission::create([
            'name'        => $data['name'],
            'guard_name'  => 'web',
            'module'      => $data['module'],
            'group'       => $data['group'],
            'description' => $data['description'] ?? null,
        ]);

        ActivityLog::log('created', "Created permission: {$permission->name}", $permission);

        return redirect()->route('admin.permissions.index')->with('success', "Permission '{$permission->name}' created successfully!");
    }

    public function edit(Permission $permission)
    {
        $modules = Permission::distinct()->pluck('module')->filter()->sort()->values();
        return view('admin.permissions.edit', compact('permission', 'modules'));
    }

    public function update(Request $request, Permission $permission)
    {
        $data = $request->validate([
            'name'        => ['required','string','max:100', \Illuminate\Validation\Rule::unique('permissions')->ignore($permission->id)],
            'module'      => 'required|string|max:50',
            'group'       => 'required|string|max:50',
            'description' => 'nullable|string|max:255',
        ]);

        $permission->update($data);

        ActivityLog::log('updated', "Updated permission: {$permission->name}", $permission);

        return redirect()->route('admin.permissions.index')->with('success', "Permission updated successfully!");
    }

    public function destroy(Permission $permission)
    {
        ActivityLog::log('deleted', "Deleted permission: {$permission->name}", $permission);
        $permission->delete();
        return redirect()->route('admin.permissions.index')->with('success', "Permission deleted successfully.");
    }
}
