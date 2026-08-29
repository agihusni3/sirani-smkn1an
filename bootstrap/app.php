<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->trustProxies(at: '*');
        $middleware->alias([
            'role'       => \App\Http\Middleware\RoleMiddleware::class,
            'role.admin' => \App\Http\Middleware\RoleAdminMiddleware::class,
        ]);
        // Kecualikan CSRF untuk endpoint login, face login, dan logout (mencegah error 419 jika tab terbuka lama / sesi expired)
        $middleware->validateCsrfTokens(except: [
            '/login',
            'login',
            '/login/face',
            'login/face',
            '/login/rfid',
            'login/rfid',
            '/login/*',
            'login/*',
            '/logout',
            'logout',
            '/api/*',
            'api/*',
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
