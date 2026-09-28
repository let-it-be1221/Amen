<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request - check that the authenticated user has one of the allowed roles.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  ...$roles
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        $user = $request->user();

        // If user is inactive, log them out
        if (!$user->is_active) {
            auth()->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            return redirect()->route('login')->withErrors([
                'email' => 'Your account has been deactivated. Please contact the administrator.',
            ]);
        }

        // Admin can access everything
        if ($user->isAdmin()) {
            return $next($request);
        }

        if (empty($roles)) {
            return $next($request);
        }

        if (!in_array($user->role, $roles, true)) {
            abort(403, 'You do not have permission to access this page.');
        }

        return $next($request);
    }
}
