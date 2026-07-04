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
    // ════════════════════════════════════════════════════════════════════
    //  LOGIN
    // ════════════════════════════════════════════════════════════════════

    public function showLogin()
    {
        if (Auth::check()) {
            // Already remembered — go to PIN check (middleware will decide)
            return redirect()->route('admin.dashboard');
        }
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $credentials['email'])->first();

        if (!$user) {
            return back()
                ->withErrors(['email' => 'No account found with this email.'])
                ->withInput();
        }

        if (!$user->is_active) {
            return back()
                ->withErrors(['email' => 'Your account is deactivated. Contact administrator.'])
                ->withInput();
        }

        // ──────────────────────────────────────────────────────────────
        //  ALWAYS pass remember: true — this sets the long-lived cookie
        //  (90 days by default, configurable in config/session.php).
        //  The PIN screen acts as the second-factor on browser reopen.
        // ──────────────────────────────────────────────────────────────
        if (Auth::attempt($credentials, remember: true)) {
            $request->session()->regenerate();

            $user->update([
                'last_login_at' => now(),
                'last_login_ip' => $request->ip(),
            ]);

            // Mark PIN as verified for this fresh login session
            // (User just proved identity with password — no need to ask PIN again now)
            session(['pin_verified' => true]);

            ActivityLog::log('login', 'User logged in');

            return redirect()->intended(route('admin.dashboard'));
        }

        return back()
            ->withErrors(['password' => 'Incorrect password.'])
            ->withInput();
    }

    // ════════════════════════════════════════════════════════════════════
    //  LOGOUT
    // ════════════════════════════════════════════════════════════════════

    public function logout(Request $request)
    {
        ActivityLog::log('logout', 'User logged out');
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login')->with('success', 'Logged out successfully.');
    }

    // ════════════════════════════════════════════════════════════════════
    //  REGISTER
    // ════════════════════════════════════════════════════════════════════

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

        // Auto remember on register too
        Auth::login($user, remember: true);
        session(['pin_verified' => true]);

        ActivityLog::log('created', 'New user registered', $user);

        return redirect()->route('admin.dashboard')
            ->with('success', 'Welcome! Account created. Set a PIN in your profile for quick access.');
    }
}
