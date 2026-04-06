<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Carbon;

class CheckInactivity
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user) {
            $token = $request->user()->currentAccessToken();
            
            // Si hay sesión (SPA) o token, verificamos inactividad
            if ($request->hasSession() || $token) {
                // Si es un TransientToken (SPA), no tiene last_used_at, usamos sesión
                $isTransient = $token instanceof \Laravel\Sanctum\TransientToken;
                $lastUsed = ($token && !$isTransient) ? $token->last_used_at : session('last_activity');

                if ($lastUsed && \Illuminate\Support\Carbon::parse($lastUsed)->diffInMinutes(now()) > 120) {
                    if ($token) {
                        $token->delete();
                    } else {
                        \Auth::logout();
                        $request->session()->invalidate();
                    }

                    return response()->json([
                        'message' => 'Session expired due to inactivity'
                    ], 401);
                }

                // Actualizar última actividad
                if ((!$token || $isTransient) && $request->hasSession()) {
                    session(['last_activity' => now()->toDateTimeString()]);
                }
            }
        }

        return $next($request);
    }
}
