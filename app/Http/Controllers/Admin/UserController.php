<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Role;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class UserController extends Controller
{
    use AuthorizesRequests; // ✅ THIS WAS MISSING

    public function index()
    {
        $user = auth()->user();

        if ($user->isSuperAdmin()) {
            $users = User::with('roles')->withTrashed()->latest()->paginate(15);
        } elseif ($user->isAdmin()) {
            $myUserIds = $user->createdUsers()->pluck('id')->push($user->id);
            $users = User::with('roles')->whereIn('id', $myUserIds)->latest()->paginate(15);
        } else {
            $users = User::with('roles')->where('id', $user->id)->paginate(15);
        }

        return view('admin.users.index', compact('users'));
    }

    public function create()
    {
        $this->authorize('users.create');
        $roles = Role::all();
        return view('admin.users.create', compact('roles'));
    }

    public function store(Request $request)
    {
        $this->authorize('users.create');

        $data = $request->validate([
            'name'       => 'required|string|max:100',
            'username'   => 'required|string|max:50|unique:users|alpha_dash',
            'email'      => 'required|email|unique:users',
            'password'   => 'required|min:8|confirmed',
            'phone'      => 'nullable|string|max:20',
            'designation'=> 'nullable|string|max:100',
            'department' => 'nullable|string|max:100',
            'is_active'  => 'boolean',
            'roles'      => 'required|array',
            'roles.*'    => 'exists:roles,id',
        ]);

        $user = User::create([
            'name'       => $data['name'],
            'username'   => $data['username'],
            'email'      => $data['email'],
            'password'   => Hash::make($data['password']),
            'phone'      => $data['phone'] ?? null,
            'designation'=> $data['designation'] ?? null,
            'department' => $data['department'] ?? null,
            'is_active'  => $request->boolean('is_active', true),
            'created_by' => auth()->id(),
        ]);

        $roles = Role::whereIn('id', $data['roles'])->pluck('name');
        $user->syncRoles($roles);

        ActivityLog::log('created', "Created user: {$user->name}", $user);

        return redirect()->route('admin.users.index')
            ->with('success', "User '{$user->name}' created successfully!");
    }

    public function show(User $user)
    {
        $this->authorizeUserAccess($user);

        $user->load('roles', 'creator', 'createdUsers', 'activityLogs');

        return view('admin.users.show', compact('user'));
    }

    public function edit(User $user)
    {
        $this->authorize('users.edit');
        $this->authorizeUserAccess($user);

        $roles = Role::all();
        $user->load('roles');

        return view('admin.users.edit', compact('user', 'roles'));
    }

    public function update(Request $request, User $user)
    {
        $this->authorize('users.edit');
        $this->authorizeUserAccess($user);

        $data = $request->validate([
            'name'       => 'required|string|max:100',
            'username'   => ['required','string','max:50','alpha_dash', Rule::unique('users')->ignore($user->id)],
            'email'      => ['required','email', Rule::unique('users')->ignore($user->id)],
            'password'   => 'nullable|min:8|confirmed',
            'phone'      => 'nullable|string|max:20',
            'designation'=> 'nullable|string|max:100',
            'department' => 'nullable|string|max:100',
            'is_active'  => 'boolean',
            'roles'      => 'required|array',
            'roles.*'    => 'exists:roles,id',
        ]);

        $updateData = [
            'name'       => $data['name'],
            'username'   => $data['username'],
            'email'      => $data['email'],
            'phone'      => $data['phone'] ?? null,
            'designation'=> $data['designation'] ?? null,
            'department' => $data['department'] ?? null,
            'is_active'  => $request->boolean('is_active', true),
        ];

        if (!empty($data['password'])) {
            $updateData['password'] = Hash::make($data['password']);
        }

        $user->update($updateData);

        $roles = Role::whereIn('id', $data['roles'])->pluck('name');
        $user->syncRoles($roles);

        ActivityLog::log('updated', "Updated user: {$user->name}", $user);

        return redirect()->route('admin.users.index')
            ->with('success', "User '{$user->name}' updated successfully!");
    }

    public function destroy(User $user)
    {
        $this->authorize('users.delete');

        if ($user->id === auth()->id()) {
            return back()->with('error', 'You cannot delete your own account!');
        }

        if ($user->isSuperAdmin()) {
            return back()->with('error', 'Cannot delete super admin!');
        }

        ActivityLog::log('deleted', "Deleted user: {$user->name}", $user);

        $user->delete();

        return redirect()->route('admin.users.index')
            ->with('success', "User deleted successfully.");
    }

    public function restore($id)
    {
        $this->authorize('users.delete');

        $user = User::withTrashed()->findOrFail($id);
        $user->restore();

        ActivityLog::log('restored', "Restored user: {$user->name}", $user);

        return back()->with('success', "User '{$user->name}' restored successfully.");
    }

    public function toggleStatus(User $user)
    {
        $this->authorize('users.edit');

        $user->update(['is_active' => !$user->is_active]);

        $status = $user->is_active ? 'activated' : 'deactivated';

        ActivityLog::log('updated', "User {$status}: {$user->name}", $user);

        return back()->with('success', "User {$status} successfully.");
    }

    protected function authorizeUserAccess(User $user)
    {
        $auth = auth()->user();

        if ($auth->isSuperAdmin()) return;

        if ($auth->isAdmin()) {
            $myUserIds = $auth->createdUsers()->pluck('id')->push($auth->id);
            if (!$myUserIds->contains($user->id)) abort(403);
            return;
        }

        if ($auth->id !== $user->id) abort(403);
    }
}
