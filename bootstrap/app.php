<?php

use App\Http\Middleware\MobileAuth;
use App\Http\Middleware\MobileGuest;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )

    ->withMiddleware(function (Middleware $middleware) {

        // ⬇️ DAFTAR ALIAS MIDDLEWARE DI SINI
        $middleware->alias([
            'mobile.auth' => MobileAuth::class,
            'mobile.guest' => MobileGuest::class,
        ]);

    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
