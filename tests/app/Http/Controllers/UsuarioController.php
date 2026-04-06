<?php

namespace App\Http\Controllers;

use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UsuarioController extends Controller
{
    /**
     * Listar todos los usuarios
     */
    public function index(Request $request)
    {
        try {
            $query = Usuario::query();

            // Filtro por búsqueda
            if ($request->has('search')) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('nombre', 'like', "%{$search}%")
                      ->orWhere('correo', 'like', "%{$search}%")
                      ->orWhere('docUsuario', 'like', "%{$search}%");
                });
            }

            // Filtro por rol
            if ($request->has('idRol')) {
                $query->where('idRol', $request->idRol);
            }

            $usuarios = $query->orderBy('creado_en', 'desc')->get();

            // Agregar nombre del rol
            $usuarios->map(function($usuario) {
                $roles = [1 => 'Admin', 2 => 'Cliente', 3 => 'Agente'];
                $usuario->nombreRol = $roles[$usuario->idRol] ?? 'Desconocido';
                $usuario->estado = $usuario->bloqueadoHasta && $usuario->bloqueadoHasta > now() 
                    ? 'Inactivo' 
                    : 'Activo';
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
     * Crear nuevo usuario
     */
    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'docUsuario' => 'required|string|max:20|unique:usuarios,docUsuario',
                'nombre' => 'required|string|max:120',
                'correo' => 'required|email|max:180|unique:usuarios,correo',
                'telefono' => 'nullable|string|max:30',
                'direccion' => 'nullable|string|max:200',
                'password' => 'required|string|min:6',
                'idRol' => 'required|integer|in:1,2,3'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'message' => 'Error de validación',
                    'errors' => $validator->errors()
                ], 422);
            }

            $usuario = Usuario::create($request->all());

            return response()->json([
                'message' => 'Usuario creado exitosamente',
                'usuario' => $usuario
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al crear usuario',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtener un usuario específico
     */
    public function show($id)
    {
        try {
            $usuario = Usuario::findOrFail($id);
            
            $roles = [1 => 'Admin', 2 => 'Cliente', 3 => 'Agente'];
            $usuario->nombreRol = $roles[$usuario->idRol] ?? 'Desconocido';
            $usuario->estado = $usuario->bloqueadoHasta && $usuario->bloqueadoHasta > now() 
                ? 'Inactivo' 
                : 'Activo';

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
        try {
            $usuario = Usuario::findOrFail($id);

            $validator = Validator::make($request->all(), [
                'nombre' => 'sometimes|required|string|max:120',
                'correo' => 'sometimes|required|email|max:180|unique:usuarios,correo,' . $id . ',docUsuario',
                'telefono' => 'nullable|string|max:30',
                'direccion' => 'nullable|string|max:200',
                'password' => 'nullable|string|min:6',
                'idRol' => 'sometimes|required|integer|in:1,2,3',
                'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'message' => 'Error de validación',
                    'errors' => $validator->errors()
                ], 422);
            }

            $data = $request->except(['docUsuario', 'avatar']); // No permitir cambiar el documento directamente
            
            // Manejar la carga del avatar
            if ($request->hasFile('avatar')) {
                // Eliminar el antiguo si existe
                if ($usuario->avatar && file_exists(public_path($usuario->avatar))) {
                    unlink(public_path($usuario->avatar));
                }
                
                $file = $request->file('avatar');
                $filename = time() . '_' . $file->getClientOriginalName();
                $file->move(public_path('avatars'), $filename);
                $data['avatar'] = 'avatars/' . $filename;
            }

            // Solo actualizar password si se envía
            if (!$request->has('password') || empty($request->password)) {
                unset($data['password']);
            }

            $usuario->update($data);

            return response()->json([
                'message' => 'Usuario actualizado exitosamente',
                'usuario' => $usuario
            ], 200);
        } catch (\Exception $e) {
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
        try {
            $usuario = Usuario::findOrFail($id);
            $usuario->delete();

            return response()->json([
                'message' => 'Usuario eliminado exitosamente'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al eliminar usuario',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
