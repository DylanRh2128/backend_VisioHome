<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ComentarioPropiedad;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    public function getComments($propertyId)
    {
        $comments = ComentarioPropiedad::where('idPropiedad', $propertyId)
            ->with('usuario') // Si ComentarioPropiedad tiene relación 'usuario'
            ->orderBy('fecha', 'desc')
            ->get();

        return response()->json($comments);
    }

    public function store(Request $request)
    {
        $request->validate([
            'idPropiedad' => 'required|exists:propiedades,idPropiedad',
            'comentario' => 'required|string|max:1000',
            'puntuacion' => 'integer|min:1|max:5'
        ]);

        $user = $request->user();

        $comment = ComentarioPropiedad::create([
            'idPropiedad' => $request->idPropiedad,
            'docUsuario' => $user->docUsuario,
            'comentario' => $request->comentario,
            'puntuacion' => $request->puntuacion ?? 5,
            'fecha' => now()
        ]);

        return response()->json(['success' => true, 'comment' => $comment]);
    }
}
