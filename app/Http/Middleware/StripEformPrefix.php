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
        $path = $request->getPathInfo();

        // Strip /eform or /eform/ prefix from the path
        if (str_starts_with($path, '/eform/')) {
            $newPath = substr($path, 6); // Remove '/eform' keeping the rest
            $request->server->set('REQUEST_URI', $newPath ?: '/');
            $request->server->set('PATH_INFO', $newPath ?: '/');

            // Create a new request with the modified path
            $request = Request::create(
                $newPath ?: '/',
                $request->method(),
                $request->all(),
                $request->cookies->all(),
                $request->files->all(),
                $request->server->all(),
                $request->getContent()
            );

            // Copy over the user instance if authenticated
            if ($original = app('request')) {
                $request->setUserResolver($original->getUserResolver());
            }
        } elseif ($path === '/eform') {
            $newPath = '/';
            $request->server->set('REQUEST_URI', '/');
            $request->server->set('PATH_INFO', '/');

            $request = Request::create(
                '/',
                $request->method(),
                $request->all(),
                $request->cookies->all(),
                $request->files->all(),
                $request->server->all(),
                $request->getContent()
            );

            if ($original = app('request')) {
                $request->setUserResolver($original->getUserResolver());
            }
        }

        return $next($request);
    }
}
