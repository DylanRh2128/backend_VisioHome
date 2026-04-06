<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next, string $role): Response
    {
        if (!$request->user()) {
            return response()->json(['error' => 'No autenticado'], 401);
        }

        // ✅ Mapeo de seguridad (idRol -> name)
        $rolesMap = [1 => 'admin', 2 => 'cliente', 3 => 'agente'];
        
        $user = $request->user();
        $userRoleName = $user->rol;
        $userRoleId = (int) $user->idRol;

        // ✅ Validar por nombre O por ID (para robustez con datos antiguos)
        $hasRoleByName = ($userRoleName === $role);
        $hasRoleById = (isset($rolesMap[$userRoleId]) && $rolesMap[$userRoleId] === $role);

        if (!$hasRoleByName && !$hasRoleById) {
            return response()->json(['error' => 'Acceso no autorizado'], 403);
        }

        return $next($request);
    }
}
