<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class EnsureUserIsAdminOnly
{
    /**
     * Handle an incoming request.
     * This middleware restricts access to admin-only features.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        // Only admin can access these features
        if (!Auth::user()->isAdmin()) {
            abort(403, 'This feature is restricted to administrators only.');
        }

        return $next($request);
    }
}
