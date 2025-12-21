<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class EnsureUserIsAdminOrIAM
{
    /**
     * Handle an incoming request.
     *
     * This middleware restricts access to admin and IAM roles only.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();

        // Allow admin and IAM roles
        if (!$user->isAdmin() && !$user->isIAM()) {
            abort(403, 'Unauthorized action. This feature is only available to Administrators and IAM users.');
        }

        return $next($request);
    }
}

