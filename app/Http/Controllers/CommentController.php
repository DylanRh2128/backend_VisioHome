<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CommentController extends Controller
{
    /**
     * Obtener comentarios de una propiedad.
     * GET /api/user/comments/{idPropiedad}
     */
    public function getByProperty(Request $request, $idPropiedad)
    {
        $comments = Comment::where('idPropiedad', $idPropiedad)
            ->with(['usuario:docUsuario,nombre,avatar'])
            ->orderBy('fecha', 'desc')
            ->get()
            ->map(function ($c) {
                // Construir URL del avatar del usuario
                if ($c->usuario && $c->usuario->avatar) {
                    $c->usuario->avatar_url = url('storage/' . $c->usuario->avatar);
                }
                // Formatear fecha legible
                $c->fecha_legible = $c->fecha ? $c->fecha->diffForHumans() : 'Reciente';
                return $c;
            });

        return response()->json($comments);
    }

    /**
     * Crear un nuevo comentario.
     * POST /api/user/comments
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'idPropiedad' => 'required|exists:propiedades,idPropiedad',
            'comentario'  => 'required|string|min:3|max:1000',
            'puntuacion'  => 'required|integer|min:1|max:5',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Error de validación',
                'errors'  => $validator->errors()
            ], 422);
        }

        $user = $request->user();

        $comment = Comment::create([
            'docUsuario'  => $user->docUsuario,
            'idPropiedad' => $request->idPropiedad,
            'comentario'  => $request->comentario,
            'puntuacion'  => $request->puntuacion,
            'fecha'       => now(),
        ]);

        // Cargar relación para la respuesta
        $comment->load('usuario:docUsuario,nombre,avatar');
        $comment->fecha_legible = $comment->fecha ? $comment->fecha->diffForHumans() : 'Reciente';

        return response()->json($comment, 201);
    }

    /**
     * Eliminar un comentario (solo el autor puede hacerlo).
     * DELETE /api/user/comments/{id}
     */
    public function destroy(Request $request, $id)
    {
        $comment = Comment::findOrFail($id);

        if ($comment->docUsuario !== $request->user()->docUsuario) {
            return response()->json(['message' => 'No autorizado'], 403);
        }

        $comment->delete();

        return response()->json(['message' => 'Comentario eliminado']);
    }
}
