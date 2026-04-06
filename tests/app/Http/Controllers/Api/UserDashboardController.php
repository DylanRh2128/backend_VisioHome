<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Propiedad;
use App\Models\Cita;
use App\Models\Cart;
use Illuminate\Http\Request;

class UserDashboardController extends Controller
{
    public function getDashboardData(Request $request)
    {
        $user = $request->user();
        
        // Citas próximas
        $appointments = Cita::where('docUsuario', $user->docUsuario)
            ->where('fecha', '>=', now())
            ->orderBy('fecha', 'asc')
            ->limit(3)
            ->get();

        // Items en carrito
        $cart = Cart::where('docUsuario', $user->docUsuario)->first();
        $cartCount = $cart ? $cart->items()->count() : 0;

        // Propiedades destacadas (limit 4 por ejemplo)
        $featuredProperties = Propiedad::where('estado', 'disponible')
            ->orderBy('idPropiedad', 'desc')
            ->limit(4)
            ->get();

        $featuredProperties->transform(function ($p) {
            if ($p->imagen_principal) {
                $p->imagen_principal = url('storage/' . $p->imagen_principal);
            }
            return $p;
        });

        return response()->json([
            'user' => $user,
            'stats' => [
                'appointments_count' => $appointments->count(),
                'cart_count' => $cartCount,
                'favorites_count' => 0, // Placeholder
            ],
            'upcoming_appointments' => $appointments,
            'featured_properties' => $featuredProperties
        ]);
    }
}
