<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class EnsureUserIsAdminOrHQ
{
    /**
     * Handle an incoming request.
     *
     * This middleware restricts access to admin and HQ roles only.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();

        // Allow admin and HQ roles
        if (!$user->isAdmin() && !$user->isHQ()) {
            abort(403, 'Unauthorized action. This feature is only available to Administrators and Headquarters.');
        }

        return $next($request);
    }
}

