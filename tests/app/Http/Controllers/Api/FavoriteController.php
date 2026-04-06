<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Favorite;
use App\Models\Propiedad;
use Illuminate\Http\Request;

class FavoriteController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $favorites = Favorite::where('docUsuario', $user->docUsuario)
            ->with(['propiedad' => function($q) {
                $q->with('imagenes');
            }])
            ->get();

        $favorites->transform(function ($f) {
            $p = $f->propiedad;
            if ($p && $p->imagen_principal) {
                $p->imagen_principal = url('storage/' . $p->imagen_principal);
            }
            if ($p && $p->imagenes) {
                foreach($p->imagenes as $img) {
                    $img->urlImagen = url('storage/' . $img->urlImagen);
                }
            }
            return $f;
        });

        return response()->json($favorites);
    }

    public function toggle(Request $request, $id)
    {
        $user = $request->user();
        $favorite = Favorite::where('docUsuario', $user->docUsuario)
            ->where('idPropiedad', $id)
            ->first();

        if ($favorite) {
            $favorite->delete();
            return response()->json(['message' => 'Eliminado de favoritos', 'is_favorite' => false]);
        } else {
            Favorite::create([
                'docUsuario' => $user->docUsuario,
                'idPropiedad' => $id
            ]);
            return response()->json(['message' => 'Agregado a favoritos', 'is_favorite' => true]);
        }
    }

    public function check(Request $request, $id)
    {
        $isFavorite = Favorite::where('docUsuario', $request->user()->docUsuario)
            ->where('idPropiedad', $id)
            ->exists();
        
        return response()->json(['is_favorite' => $isFavorite]);
    }
}
