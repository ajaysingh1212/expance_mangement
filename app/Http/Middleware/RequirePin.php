<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RequirePin
{
    /**
     * Handle an incoming request.
     *
     * ─ Flow ────────────────────────────────────────────────────────────────
     *
     *  Request hits admin route
     *       │
     *       ▼
     *  Auth::check()?  ─── No ──► redirect to login
     *       │ Yes
     *       ▼
     *  User has PIN enabled?  ─── No ──► pass through (no PIN screen needed)
     *       │ Yes
     *       ▼
     *  session('pin_verified') == true?  ─── Yes ──► pass through
     *       │ No
     *       ▼
     *  redirect to /pin   (PIN screen shown)
     *
     * ────────────────────────────────────────────────────────────────────────
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Not authenticated at all → let auth middleware handle it
        if (!Auth::check()) {
            return $next($request);
        }

        $user = Auth::user();

        // Skip if user hasn't set a PIN or has it disabled
        if (!$user->pin || !$user->pin_enabled) {
            return $next($request);
        }

        // Skip the PIN routes themselves to avoid redirect loop
        if ($request->routeIs('pin.*')) {
            return $next($request);
        }

        // PIN already verified this session
        if (session('pin_verified')) {
            return $next($request);
        }

        // Store the intended URL so we can redirect back after PIN entry
        return redirect()->route('pin.show');
    }
}
