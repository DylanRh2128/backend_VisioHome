<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Favorite;

class FavoriteController extends Controller
{
    /**
     * Retorna todos los favoritos del usuario autenticado
     */
    public function index(Request $request)
    {
        $user = $request->user();
        if (!$user) {
            return response()->json(['error' => 'No autorizado'], 401);
        }

        $favorites = Favorite::with('propiedad')
            ->where('docUsuario', $user->docUsuario)
            ->get();

        return response()->json($favorites);
    }

    /**
     * Hace toggle de un favorito
     */
    public function toggle(Request $request, $id)
    {
        $user = $request->user();
        if (!$user) {
            return response()->json(['error' => 'No autorizado'], 401);
        }

        $favorite = Favorite::where('docUsuario', $user->docUsuario)
            ->where('idPropiedad', $id)
            ->first();

        if ($favorite) {
            $favorite->delete();
            return response()->json([
                'is_favorite' => false,
                'message' => 'Eliminado de favoritos'
            ]);
        } else {
            Favorite::create([
                'docUsuario' => $user->docUsuario,
                'idPropiedad' => $id
            ]);
            return response()->json([
                'is_favorite' => true,
                'message' => 'Agregado a favoritos'
            ]);
        }
    }

    /**
     * Verifica si una propiedad específica está en favoritos
     */
    public function check(Request $request, $id)
    {
        $user = $request->user();
        if (!$user) {
            return response()->json(['error' => 'No autorizado'], 401);
        }

        $exists = Favorite::where('docUsuario', $user->docUsuario)
            ->where('idPropiedad', $id)
            ->exists();

        return response()->json([
            'is_favorite' => $exists
        ]);
    }
}
