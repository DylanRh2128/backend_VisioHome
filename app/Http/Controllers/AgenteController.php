<?php

namespace App\Http\Controllers;

use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class AgenteController extends Controller
{
    /**
     * Listar todos los agentes
     */
    public function index(Request $request)
    {
        try {
            $query = Usuario::agentes()
                ->select('docUsuario', 'nombre', 'avatar', 'telefono', 'ciudad', 'activo', 'idRol');

            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('nombre', 'like', "%{$search}%")
                      ->orWhere('correo', 'like', "%{$search}%")
                      ->orWhere('docUsuario', 'like', "%{$search}%");
                });
            }

            if ($request->filled('activo')) {
                $query->where('activo', (bool) $request->activo);
            }

            $agentes = $query->get();

            $agentes->map(function ($usuario) {
                $usuario->idRol = 3;
                $usuario->ciudad = $usuario->ciudad ?? null;
                $usuario->activo = (bool) $usuario->activo;
                $usuario->estado = $usuario->activo ? 'Activo' : 'Inactivo';
                return $usuario;
            });

            return response()->json($agentes, 200);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al obtener agentes',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Crear nuevo agente
     */
    public function store(Request $request)
    {
        DB::beginTransaction();

        try {
            $validator = Validator::make($request->all(), [
                'docUsuario' => 'required|string|max:20|unique:usuarios,docUsuario',
                'nombre' => 'required|string|max:120',
                'direccion' => 'nullable|string|max:200',
                'correo' => 'required|email|max:180|unique:usuarios,correo',
                'telefono' => 'nullable|string|max:30',
                'genero' => 'nullable|string|in:Masculino,Femenino,Otro',
                'password' => 'required|string|min:8',
                'activo' => 'sometimes|boolean',
                'ciudad' => 'nullable|string|max:100',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'message' => 'Error de validación',
                    'errors' => $validator->errors()
                ], 422);
            }

            $data = $request->except(['avatar']);
            $data['idRol'] = 3;
            $data['rol'] = 'agente';

            if ($request->hasFile('avatar')) {
                $path = $request->file('avatar')->store('avatars', 'public');
                $data['avatar'] = $path;
            }

            $usuario = Usuario::create($data);

            DB::commit();

            return response()->json([
                'message' => 'Agente creado exitosamente',
                'usuario' => $usuario
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'message' => 'Error al crear agente',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtener un agente específico
     */
    public function show($id)
    {
        try {
            $agente = Usuario::agentes()->findOrFail($id);

            $agente->ciudad = $agente->ciudad ?? null;
            $agente->activo = (bool) $agente->activo;
            $agente->estado = $agente->activo ? 'Activo' : 'Inactivo';

            return response()->json($agente, 200);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Agente no encontrado',
                'error' => $e->getMessage()
            ], 404);
        }
    }

    /**
     * Actualizar agente
     */
    public function update(Request $request, $id)
    {
        DB::beginTransaction();

        try {
            $usuario = Usuario::agentes()->findOrFail($id);

            $validator = Validator::make($request->all(), [
                'nombre' => 'sometimes|required|string|max:120',
                'direccion' => 'nullable|string|max:200',
                'telefono' => 'nullable|string|max:30',
                'genero' => 'nullable|string|in:Masculino,Femenino,Otro',
                'password' => 'nullable|string|min:8',
                'ciudad' => 'nullable|string|max:100',
                'activo' => 'sometimes|boolean',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'message' => 'Error de validación',
                    'errors' => $validator->errors()
                ], 422);
            }

            $userData = $request->only([
                'nombre', 'telefono', 'direccion', 'genero', 'ciudad'
            ]);

            if ($request->has('activo')) {
                $userData['activo'] = (bool) $request->activo;
            }

            if ($request->filled('password')) {
                $userData['password'] = bcrypt($request->password);
            }

            if ($request->hasFile('avatar')) {
                $path = $request->file('avatar')->store('avatars', 'public');
                $userData['avatar'] = $path;
            }

            $usuario->update($userData);

            DB::commit();

            return response()->json([
                'message' => 'Agente actualizado exitosamente',
                'usuario' => $usuario
            ], 200);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'message' => 'Error al actualizar agente',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Eliminar agente
     */
    public function destroy($id)
    {
        DB::beginTransaction();

        try {
            // ✅ FIX CLAVE: usar findOrFail (NO docUsuario)
            $usuario = Usuario::agentes()->findOrFail($id);

            $usuario->favoritos()->delete();
            $usuario->comentarios()->delete();
            $usuario->citasCliente()->delete();
            $usuario->citasAgente()->delete();
            $usuario->disponibilidades()->delete();

            DB::table('agentes')->where('idUsuario', $usuario->docUsuario)->delete();

            $usuario->delete();

            DB::commit();

            return response()->json([
                'message' => 'Agente eliminado correctamente'
            ], 200);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'message' => 'Error al eliminar agente',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}