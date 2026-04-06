<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Propiedad;
use App\Models\Favorite;
use Illuminate\Http\Request;

class UserDashboardController extends Controller
{
    public function getDashboardData(Request $request)
    {
        $user = $request->user();
        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        // Citas próximas (si existe la tabla)
        $appointments = collect();
        try {
            $appointments = \App\Models\Cita::where('docUsuario', $user->docUsuario)
                ->with('propiedad')
                ->where('fecha', '>=', now())
                ->orderBy('fecha', 'asc')
                ->limit(3)
                ->get();
        } catch (\Exception $e) {
            // La tabla citas puede no existir aún
        }

        // Favoritos del usuario
        $favoritesCount = 0;
        try {
            $favoritesCount = Favorite::where('docUsuario', $user->docUsuario)->count();
        } catch (\Exception $e) {}

        // Total de propiedades disponibles
        $availableCount = Propiedad::where('estado', 'disponible')->count();

        // Propiedades destacadas (últimas 6 disponibles) con imágenes
        $featuredProperties = Propiedad::where('estado', 'disponible')
            ->orderBy('idPropiedad', 'desc')
            ->limit(6)
            ->get();

        // Proyectos Finalizados (últimas 3 vendidas)
        $soldProperties = Propiedad::where('estado', 'vendida')
            ->orderBy('idPropiedad', 'desc')
            ->limit(3)
            ->get();

        return response()->json([
            'user'                  => $user,
            'stats'                 => [
                'available_count'     => $availableCount,
                'appointments_count'  => $appointments->count(),
                'favorites_count'     => $favoritesCount,
                'cart_count'          => 0,
            ],
            'upcoming_appointments' => $appointments,
            'featured_properties'   => $featuredProperties,
            'sold_properties'       => $soldProperties,
        ]);
    }
}
