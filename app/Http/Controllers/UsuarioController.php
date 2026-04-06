<?php

namespace App\Http\Controllers;

use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class UsuarioController extends Controller
{
    /**
     * Listar todos los usuarios
     */
    public function index(Request $request)
    {
        try {
            $query = Usuario::query(); // ❌ sin agenteProfile

            // Filtro por rol
            if ($request->has('idRol')) {
                $query->where('idRol', $request->idRol);
            }

            // Filtro por búsqueda
            if ($request->has('search')) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('nombre', 'like', "%{$search}%")
                      ->orWhere('correo', 'like', "%{$search}%")
                      ->orWhere('docUsuario', 'like', "%{$search}%");
                });
            }

            $usuarios = $query->orderBy('creado_en', 'desc')->get();

            // Agregar metadatos
            $usuarios->map(function($usuario) {
                $roles = [1 => 'Admin', 2 => 'Cliente', 3 => 'Agente'];
                $usuario->nombreRol = $roles[$usuario->idRol] ?? 'Desconocido';
                $usuario->estado = $usuario->activo ? 'Activo' : 'Inactivo';

                // Avatar
                $usuario->avatar = $usuario->avatar 
                    ? url('storage/' . $usuario->avatar)
                    : null;

                return $usuario;
            });

            return response()->json($usuarios, 200);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al obtener usuarios',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Crear usuario
     */
    public function store(Request $request)
    {
        DB::beginTransaction();

        try {
            $validator = Validator::make($request->all(), [
                'docUsuario' => 'required|string|max:20|unique:usuarios,docUsuario',
                'nombre'     => 'required|string|max:120',
                'correo'     => 'required|email|max:180|unique:usuarios,correo',
                'password'   => 'required|string|min:6',
                'idRol'      => 'required|integer|in:1,2,3',
                'ciudad'     => 'nullable|string|max:100',
                'genero'     => 'required|string|in:Masculino,Femenino,prefiero_no_decirlo',
                'activo'     => 'sometimes|boolean'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'message' => 'Error de validación',
                    'errors'  => $validator->errors()
                ], 422);
            }

            // 🔥 NO uses else, continúa directo
            $data = $request->except(['avatar']);

            if ($request->hasFile('avatar')) {
                $data['avatar'] = $request->file('avatar')->store('avatars', 'public');
            }

            $usuario = Usuario::create($data);

            DB::commit();

            return response()->json([
                'message' => 'Usuario creado exitosamente',
                'usuario' => $usuario
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'message' => 'Error al crear usuario',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtener usuario
     */
    public function show($id)
    {
        try {
            $usuario = Usuario::findOrFail($id);

            $roles = [1 => 'Admin', 2 => 'Cliente', 3 => 'Agente'];
            $usuario->nombreRol = $roles[$usuario->idRol] ?? 'Desconocido';
            $usuario->estado = $usuario->activo ? 'Activo' : 'Inactivo';

            $usuario->avatar = $usuario->avatar 
                ? url('storage/' . $usuario->avatar)
                : null;

            return response()->json($usuario, 200);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Usuario no encontrado',
                'error' => $e->getMessage()
            ], 404);
        }
    }

    /**
     * Actualizar usuario
     */
    public function update(Request $request, $id)
    {
        DB::beginTransaction();
        try {
            $usuario = Usuario::findOrFail($id);

            $validator = Validator::make($request->all(), [
                'nombre'   => 'sometimes|required|string|max:120',
                'password' => 'nullable|string|min:6',
                'idRol'    => 'sometimes|required|integer|in:1,2,3',
                'ciudad'   => 'nullable|string|max:100',
                'genero'   => 'sometimes|required|string|in:Masculino,Femenino,prefiero_no_decirlo',
                'activo'   => 'sometimes|boolean'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'message' => 'Error de validación',
                    'errors'  => $validator->errors()
                ], 422);
            }

            $data = $request->except(['docUsuario', 'correo', 'avatar']);

            if ($request->has('activo')) {
                $data['activo'] = (bool) $request->activo;
            }

            if (empty($data['password'])) {
                unset($data['password']);
            }

            if ($request->hasFile('avatar')) {
                $data['avatar'] = $request->file('avatar')->store('avatars', 'public');
            }

            $usuario->update($data);

            DB::commit();

            return response()->json([
                'message' => 'Usuario actualizado exitosamente',
                'usuario' => $usuario
            ], 200);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Error al actualizar usuario',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Eliminar usuario
     */
    public function destroy($id)
    {
        DB::beginTransaction();

        try {
            // Buscamos por la PK docUsuario (aunque Laravel lo mapee como $id)
            $usuario = Usuario::where('docUsuario', $id)->firstOrFail();

            // 🧹 1. Eliminar favoritos (pivot records)
            $usuario->favoritos()->delete();

            // 🧹 2. Eliminar comentarios
            $usuario->comentarios()->delete();

            // 🧹 3. Eliminar citas como cliente
            $usuario->citasCliente()->delete();

            // 🧹 4. Eliminar citas como agente
            $usuario->citasAgente()->delete();

            // 🧹 5. Eliminar disponibilidades (si es agente)
            if ($usuario->idRol == 3) {
                $usuario->disponibilidades()->delete();
                
                // Si existe el registro en la vieja tabla agentes, lo limpiamos también
                DB::table('agentes')->where('idUsuario', $usuario->docUsuario)->delete();
            }

            // 🧨 6. Finalmente, eliminar el usuario
            $usuario->delete();

            DB::commit();

            return response()->json([
                'message' => 'Usuario eliminado correctamente'
            ], 200);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'message' => 'Error al eliminar usuario',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Estadísticas
     */
    public function globalStats()
    {
        try {
            return response()->json([
                'total_usuarios' => Usuario::count(),
                'total_clientes' => Usuario::where('idRol', 2)->count(),
                'total_agentes' => Usuario::where('idRol', 3)->count(),
                'total_admins' => Usuario::where('idRol', 1)->count(),
                'total_activos' => Usuario::where('activo', 1)->count(),
                'total_bloqueados' => Usuario::where('activo', 0)->count()
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al obtener estadísticas',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}