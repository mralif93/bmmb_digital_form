<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class StripEformPrefix
{
    /**
     * Handle an incoming request and strip /eform prefix if present.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $uri = $request->getRequestUri();

        // Strip /eform or /eform/ prefix from the URI
        if (str_starts_with($uri, '/eform/')) {
            $newUri = substr($uri, 6); // Remove '/eform' keeping the trailing slash
            $request->server->set('REQUEST_URI', $newUri ?: '/');
        } elseif ($uri === '/eform') {
            $request->server->set('REQUEST_URI', '/');
        }

        return $next($request);
    }
}
