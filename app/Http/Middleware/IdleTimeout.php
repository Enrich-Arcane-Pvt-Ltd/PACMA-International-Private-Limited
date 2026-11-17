<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class IdleTimeout
{
    public function handle($request, Closure $next)
    {
        $guard = Auth::guard('web');
        if ($guard->check()) {
            $last = $request->session()->get('idle_last_activity');
            // A valid remember cookie can start a new session, not renew an expired one.
            if (!$last && $guard->viaRemember()) {
                $last = microtime(true);
                $request->session()->put('idle_last_activity', $last);
                $request->session()->put('idle_tab_key', bin2hex(random_bytes(16)));
            }
            if (!$last || microtime(true) - $last >= 300) {
                $guard->logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return $request->expectsJson()
                    ? response()->json(['message' => 'Session expired. Please sign in again.'], 401)
                    : redirect()->route('login')->with('error', 'Session expired. Please sign in again.');
            }
        }

        return $next($request);
    }
}
