<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class DebugAuthMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle($request, Closure $next)
    {
        \Log::info('AUTH DEBUG', [
            'url' => $request->fullUrl(),
            'method' => $request->method(),
            'ip' => $request->ip(),
            'origin' => $request->headers->get('origin'),
            'referer' => $request->headers->get('referer'),
            'has_session' => $request->hasSession(),
            'session_id' => session()->getId(),
            'session_data' => session()->all(),
            'cookies' => $request->cookies->all(),
            'auth_check' => \Auth::check(),
            'user' => \Auth::user() ? \Auth::user()->correo : null,
            'auth_id' => \Auth::id(),
            'default_driver' => auth()->getDefaultDriver(),
        ]);

        return $next($request);
    }
}