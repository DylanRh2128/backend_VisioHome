<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class JwtFromCookie
{
    public function handle(Request $request, Closure $next): Response
    {
        // 🔥 1. Leer cookie JWT
        $token = $request->cookie('jwt_token');

        // 🔍 DEBUG REAL (CRÍTICO PARA SABER SI LLEGA)
        \Log::info('[JWT COOKIE DEBUG]', [
            'has_cookie' => !!$token,
            'cookie_value' => $token ? substr($token, 0, 20) . '...' : null,
            'has_auth_header_before' => $request->hasHeader('Authorization'),
        ]);

        // 🔥 2. Inyectar Authorization SOLO si existe token
        if ($token && !$request->hasHeader('Authorization')) {
            $request->headers->set('Authorization', 'Bearer ' . $token);

            \Log::info('[JWT INJECTED]', [
                'auth_header' => 'Bearer ' . substr($token, 0, 20) . '...'
            ]);
        }

        // 🔥 3. Continuar request
        $response = $next($request);

        return $response;
    }
}