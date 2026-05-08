<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    public function index()
    {
        $user = auth()->user()->load('roles', 'activityLogs');
        return view('admin.profile.index', compact('user'));
    }

    public function edit()
    {
        $user = auth()->user();
        return view('admin.profile.edit', compact('user'));
    }

    public function update(Request $request)
    {
        $user = auth()->user();

        $data = $request->validate([
            'name'         => 'required|string|max:100',
            'username'     => ['required','string','max:50','alpha_dash', \Illuminate\Validation\Rule::unique('users')->ignore($user->id)],
            'phone'        => 'nullable|string|max:20',
            'bio'          => 'nullable|string|max:500',
            'designation'  => 'nullable|string|max:100',
            'department'   => 'nullable|string|max:100',
            'date_of_birth'=> 'nullable|date',
            'gender'       => 'nullable|in:male,female,other',
            'address'      => 'nullable|string|max:255',
            'city'         => 'nullable|string|max:100',
            'state'        => 'nullable|string|max:100',
            'country'      => 'nullable|string|max:100',
            'postal_code'  => 'nullable|string|max:20',
            'facebook'     => 'nullable|url|max:255',
            'twitter'      => 'nullable|url|max:255',
            'linkedin'     => 'nullable|url|max:255',
            'instagram'    => 'nullable|url|max:255',
            'github'       => 'nullable|url|max:255',
            'website'      => 'nullable|url|max:255',
            'avatar'       => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'cover_photo'  => 'nullable|image|mimes:jpg,jpeg,png,webp|max:4096',
        ]);

        if ($request->hasFile('avatar')) {
            if ($user->avatar && $user->avatar !== 'default-avatar.png') {
                Storage::disk('public')->delete('avatars/' . $user->avatar);
            }
            $filename = 'avatar_' . $user->id . '_' . time() . '.' . $request->file('avatar')->extension();
            $request->file('avatar')->storeAs('avatars', $filename, 'public');
            $data['avatar'] = $filename;
        }

        if ($request->hasFile('cover_photo')) {
            if ($user->cover_photo && $user->cover_photo !== 'default-cover.jpg') {
                Storage::disk('public')->delete('covers/' . $user->cover_photo);
            }
            $filename = 'cover_' . $user->id . '_' . time() . '.' . $request->file('cover_photo')->extension();
            $request->file('cover_photo')->storeAs('covers', $filename, 'public');
            $data['cover_photo'] = $filename;
        }

        unset($data['avatar'], $data['cover_photo']);

        $updateData = collect($data)->except(['avatar', 'cover_photo'])->toArray();
        if (isset($data['avatar'])) $updateData['avatar'] = $data['avatar'];
        if (isset($data['cover_photo'])) $updateData['cover_photo'] = $data['cover_photo'];

        // handle file uploads separately
        if ($request->hasFile('avatar')) $updateData['avatar'] = $filename ?? $user->avatar;
        if ($request->hasFile('cover_photo')) $updateData['cover_photo'] = $coverFilename ?? $user->cover_photo;

        $user->update($updateData);

        ActivityLog::log('updated', 'Updated profile', $user);

        return back()->with('success', 'Profile updated successfully!');
    }

    public function updateAvatar(Request $request)
    {
        $request->validate(['avatar' => 'required|image|mimes:jpg,jpeg,png,webp|max:2048']);
        $user = auth()->user();

        if ($user->avatar && $user->avatar !== 'default-avatar.png') {
            Storage::disk('public')->delete('avatars/' . $user->avatar);
        }

        $filename = 'avatar_' . $user->id . '_' . time() . '.' . $request->file('avatar')->extension();
        $request->file('avatar')->storeAs('avatars', $filename, 'public');
        $user->update(['avatar' => $filename]);

        return back()->with('success', 'Profile photo updated!');
    }

    public function updateCover(Request $request)
    {
        $request->validate(['cover_photo' => 'required|image|mimes:jpg,jpeg,png,webp|max:4096']);
        $user = auth()->user();

        if ($user->cover_photo && $user->cover_photo !== 'default-cover.jpg') {
            Storage::disk('public')->delete('covers/' . $user->cover_photo);
        }

        $filename = 'cover_' . $user->id . '_' . time() . '.' . $request->file('cover_photo')->extension();
        $request->file('cover_photo')->storeAs('covers', $filename, 'public');
        $user->update(['cover_photo' => $filename]);

        return back()->with('success', 'Cover photo updated!');
    }

    public function changePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'password'         => 'required|min:8|confirmed|different:current_password',
        ]);

        $user = auth()->user();

        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'Current password is incorrect.']);
        }

        $user->update(['password' => Hash::make($request->password)]);
        ActivityLog::log('updated', 'Changed password', $user);

        return back()->with('success', 'Password changed successfully!');
    }
}
