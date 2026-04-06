<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class AgenteController extends Controller
{
    /**
     * Reemplazar completamente la lógica de “agentes” para que funcione con el modelo Usuario.
     * Método GET (listar agentes)
     */
    public function index(Request $request)
    {
        try {
            $search = $request->query('search');

            $query = Usuario::where('rol', 'agente');

            if (!empty($search)) {
                $query->where(function ($q) use ($search) {
                    $q->where('nombre', 'like', "%{$search}%")
                      ->orWhere('correo', 'like', "%{$search}%");
                });
            }

            return response()->json($query->get());

        } catch (\Exception $e) {
            Log::error('Error al listar agentes', [
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'error' => 'Error interno del servidor'
            ], 500);
        }
    }

    /**
     * Método POST (crear agente)
     */
    public function store(Request $request)
    {
        try {
            $data = $request->validate([
                'docUsuario' => 'required|string|unique:usuarios,docUsuario', // Se añadió docUsuario ya que es la PK y no es auto-incremental
                'nombre'     => 'required|string|max:255',
                'correo'     => 'required|email|unique:usuarios,correo',
                'password'   => 'required|string|min:6',
            ]);

            $data['password'] = bcrypt($data['password']);
            $data['rol'] = 'agente';
            $data['activo'] = true;

            $usuario = Usuario::create($data);

            return response()->json($usuario, 201);

        } catch (\Exception $e) {
            Log::error('Error al crear agente', [
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'error' => 'Error interno del servidor',
                'details' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get details of a specific agent (Simple version for new architecture)
     */
    public function show($id)
    {
        $agente = Usuario::where('rol', 'agente')
            ->where('docUsuario', $id)
            ->first();

        if (!$agente) {
            return response()->json(['message' => 'Agente no encontrado'], 404);
        }

        return response()->json($agente);
    }
}
