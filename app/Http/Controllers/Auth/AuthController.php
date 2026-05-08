<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function loginForm()
    {
        if (Auth::check()) return redirect()->route('admin.dashboard');
        return view('auth.login');
    }
    public function showLogin()
    {
        return view('auth.login'); // make sure this view exists
    }
    public function login(Request $request)
    {
        // dd($request->all());
        $credentials = $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email',$credentials['email'])->first();

        if (!$user) {
            return back()->withErrors(['email' => 'No account found with this email.'])->withInput();
        }

        if (!$user->is_active) {
            return back()->withErrors(['email' => 'Your account is deactivated. Contact administrator.'])->withInput();
        }

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();
            $user->update([
                'last_login_at' => now(),
                'last_login_ip' => $request->ip(),
            ]);
            ActivityLog::log('login', 'User logged in');
            return redirect()->intended(route('admin.dashboard'));
        }

        return back()->withErrors(['password' => 'Incorrect password.'])->withInput();
    }

    public function logout(Request $request)
    {
        ActivityLog::log('logout', 'User logged out');
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login')->with('success', 'Logged out successfully.');
    }

    public function showRegister()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $data = $request->validate([
            'name'     => 'required|string|max:100',
            'username' => 'required|string|max:50|unique:users|alpha_dash',
            'email'    => 'required|email|unique:users',
            'password' => 'required|min:8|confirmed',
        ]);

        $user = User::create([
            'name'     => $data['name'],
            'username' => $data['username'],
            'email'    => $data['email'],
            'password' => Hash::make($data['password']),
        ]);

        $user->assignRole('user');

        Auth::login($user);
        ActivityLog::log('created', 'New user registered', $user);

        return redirect()->route('admin.dashboard')->with('success', 'Welcome! Account created successfully.');
    }
}
