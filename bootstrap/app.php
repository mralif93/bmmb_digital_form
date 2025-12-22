<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Strip /eform prefix from requests
        $middleware->prependToGroup('web', \App\Http\Middleware\StripEformPrefix::class);

        $middleware->alias([
            'admin' => \App\Http\Middleware\EnsureUserIsAdmin::class,
            'admin-only' => \App\Http\Middleware\EnsureUserIsAdminOnly::class,
            'admin-or-hq' => \App\Http\Middleware\EnsureUserIsAdminOrHQ::class,
            'admin-or-iam' => \App\Http\Middleware\EnsureUserIsAdminOrIAM::class,
            'admin-or-hq-or-iam' => \App\Http\Middleware\EnsureUserIsAdminOrHQOrIAM::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
