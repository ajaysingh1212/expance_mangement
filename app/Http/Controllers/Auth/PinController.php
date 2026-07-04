<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class PinController extends Controller
{
    // ── How it works ────────────────────────────────────────────────────────
    //
    //  1. User logs in normally (AuthController) → Laravel sets a long-lived
    //     "remember_me" cookie (90 days) via Auth::attempt(..., remember: true).
    //
    //  2. Browser is closed, but the remember cookie stays on disk.
    //
    //  3. Next visit: Laravel auto-authenticates the user via the remember cookie,
    //     so Auth::check() is true.  BUT we add a `pin_verified` flag to the
    //     session (which resets when the browser tab/window lifecycle ends).
    //
    //  4. RequirePinMiddleware checks every request:
    //       - Not logged in?           → normal login page
    //       - Logged in + pin not set? → skip PIN screen (first-time users)
    //       - Logged in + pin set + session has `pin_verified`? → all good
    //       - Logged in + pin set + no `pin_verified`?          → PIN screen
    //
    //  5. User enters 4-digit PIN → verified → session['pin_verified'] = true
    //     → proceed to dashboard.
    //
    //  Cache/logout scenario: clearing cookies destroys the remember token, so
    //  the user must do a full login again.
    // ────────────────────────────────────────────────────────────────────────

    // ══════════════════════════════════════════════════════════════════════
    //  SHOW PIN SCREEN
    // ══════════════════════════════════════════════════════════════════════

    public function showPin()
    {
        // Already verified this session → go to intended URL
        if (session('pin_verified')) {
            return redirect()->intended(route('admin.dashboard'));
        }

        $user = Auth::user();

        // If user has no PIN set, mark verified and continue
        if (!$user->pin || !$user->pin_enabled) {
            session(['pin_verified' => true]);
            return redirect()->intended(route('admin.dashboard'));
        }

        return view('auth.pin', [
            'userName'   => $user->name,
            'userAvatar' => $user->avatar ?? null,
        ]);
    }

    // ══════════════════════════════════════════════════════════════════════
    //  VERIFY PIN
    // ══════════════════════════════════════════════════════════════════════

    public function verifyPin(Request $request)
    {
        $request->validate([
            'pin' => ['required', 'digits:4'],
        ]);

        $user = Auth::user();

        if (!Hash::check($request->pin, $user->pin)) {
            // Track attempts to prevent brute-force
            $attempts = session('pin_attempts', 0) + 1;
            session(['pin_attempts' => $attempts]);

            // Lock out after 5 wrong attempts → full logout
            if ($attempts >= 5) {
                session()->forget('pin_attempts');
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();
                ActivityLog::log('pin_lockout', 'Account locked after 5 wrong PIN attempts');
                return redirect()->route('login')
                    ->with('error', 'Too many wrong PIN attempts. Please log in again.');
            }

            $remaining = 5 - $attempts;

            throw ValidationException::withMessages([
                'pin' => "Incorrect PIN. {$remaining} attempt" . ($remaining === 1 ? '' : 's') . ' remaining.',
            ]);
        }

        // Success — mark this session as PIN-verified
        session()->forget('pin_attempts');
        session(['pin_verified' => true]);

        ActivityLog::log('pin_verified', 'PIN verification successful');

        return redirect()->intended(route('admin.dashboard'));
    }

    // ══════════════════════════════════════════════════════════════════════
    //  SETUP / UPDATE PIN  (from profile page)
    // ══════════════════════════════════════════════════════════════════════

    public function setupPin(Request $request)
    {
        $request->validate([
            'current_password' => ['required'],
            'pin'              => ['required', 'digits:4', 'confirmed'],   // pin_confirmation field
            'pin_enabled'      => ['nullable', 'boolean'],
        ]);

        $user = Auth::user();

        if (!\Illuminate\Support\Facades\Hash::check($request->current_password, $user->password)) {
            throw ValidationException::withMessages([
                'current_password' => 'Your current password is incorrect.',
            ]);
        }

        $user->update([
            'pin'         => Hash::make($request->pin),
            'pin_enabled' => (bool) $request->boolean('pin_enabled', true),
        ]);

        // Mark current session as verified after setup
        session(['pin_verified' => true]);

        ActivityLog::log('pin_updated', 'PIN updated successfully');

        return back()->with('success', 'PIN set successfully. It will be required on your next session.');
    }

    // ══════════════════════════════════════════════════════════════════════
    //  DISABLE PIN
    // ══════════════════════════════════════════════════════════════════════

    public function disablePin(Request $request)
    {
        $request->validate([
            'current_password' => ['required'],
        ]);

        $user = Auth::user();

        if (!Hash::check($request->current_password, $user->password)) {
            throw ValidationException::withMessages([
                'current_password' => 'Your current password is incorrect.',
            ]);
        }

        $user->update([
            'pin'         => null,
            'pin_enabled' => false,
        ]);

        ActivityLog::log('pin_disabled', 'PIN lock disabled');

        return back()->with('success', 'PIN lock has been disabled.');
    }

    // ══════════════════════════════════════════════════════════════════════
    //  SWITCH ACCOUNT  (logout current PIN session, go to login)
    // ══════════════════════════════════════════════════════════════════════

    public function switchAccount(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login');
    }
}
