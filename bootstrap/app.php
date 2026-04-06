<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {

        // Solo agregar JwtFromCookie — lo demás ya está en el grupo api por defecto
        $middleware->api(prepend: [
            \App\Http\Middleware\JwtFromCookie::class,
        ]);

        // CSRF excepciones
        $middleware->validateCsrfTokens(except: [
            '/api/webhooks/mercadopago',
        ]);

        // No encriptar el JWT (viaja en cookie httpOnly pero sin encriptación Laravel)
        $middleware->encryptCookies(except: [
            'jwt_token',
        ]);

        // Aliases
        $middleware->alias([
            'role'       => \App\Http\Middleware\RoleMiddleware::class,
            'inactivity' => \App\Http\Middleware\CheckInactivity::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })
    ->create();