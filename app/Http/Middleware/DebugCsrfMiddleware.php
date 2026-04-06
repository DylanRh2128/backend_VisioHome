<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class DebugCsrfMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        Log::info('CSRF DEBUG BEFORE', [
            'session_id' => session()->getId(),
            'session_token' => session()->token(),
            'header_token' => $request->header('X-XSRF-TOKEN'),
            'header_token_alt' => $request->header('X-CSRF-TOKEN'),
            'cookie_token' => $request->cookie('XSRF-TOKEN'),
            'all_cookies' => $request->cookies->all(),
            'has_session' => $request->hasSession(),
        ]);

        return $next($request);
    }
}
