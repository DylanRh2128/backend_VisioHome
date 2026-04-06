<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class ProfileController extends Controller
{
    /**
     * Obtener el perfil del usuario autenticado
     */
    public function show(Request $request)
    {
        $user = $request->user();
        
        $roles = [1 => 'Admin', 2 => 'Cliente', 3 => 'Agente'];
        $user->nombreRol = $roles[$user->idRol] ?? 'Usuario';

        // Construir URL pública del avatar
        if ($user->avatar) {
            $user->avatar_url = url('api/files/' . $user->avatar);
        }

        return response()->json($user);
    }

    /**
     * Actualizar datos del perfil (nombre, teléfono, dirección, contraseña)
     * El correo NO se puede modificar.
     */
    public function update(Request $request)
    {
        $user = $request->user();

        $validator = Validator::make($request->all(), [
            'nombre'    => 'sometimes|required|string|max:120',
            'telefono'  => 'nullable|string|max:30',
            'direccion' => 'nullable|string|max:255',
            'password'  => 'nullable|string|min:6|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Error de validación',
                'errors'  => $validator->errors()
            ], 422);
        }

        // Permitir campos de clientes y campos de agentes
        $data = $request->only(['nombre', 'telefono', 'direccion', 'especialidad', 'biografia', 'carrera', 'experiencia_anos']);

        // Solo actualizar contraseña si se envió y no está vacía
        if ($request->filled('password')) {
            $data['password'] = $request->password; // El mutator del modelo hace el Hash
        }

        $user->update($data);

        $roles = [1 => 'Admin', 2 => 'Cliente', 3 => 'Agente'];
        $user->nombreRol = $roles[$user->idRol] ?? 'Usuario';

        // Include avatar_url so frontend can sync it globally
        if ($user->avatar) {
            $user->avatar_url = url('api/files/' . $user->avatar);
        }

        return response()->json([
            'message' => 'Perfil actualizado correctamente',
            'user'    => $user
        ]);
    }

    /**
     * Subir o reemplazar el avatar del usuario.
     * Recibe multipart/form-data con campo 'avatar'.
     */
    public function uploadAvatar(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'avatar' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:3072',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Archivo inválido',
                'errors'  => $validator->errors()
            ], 422);
        }

        $user = $request->user();

        // Eliminar avatar anterior si existe
        if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
            Storage::disk('public')->delete($user->avatar);
        }

        // Guardar nuevo avatar en storage/app/public/avatars/
        $path = $request->file('avatar')->store('avatars', 'public');

        $user->avatar = $path;
        $user->save();

        return response()->json([
            'message'    => 'Avatar actualizado correctamente',
            'avatar'     => $path,
            'avatar_url' => url('api/files/' . $path),
        ]);
    }

    /**
     * Subir o reemplazar el CV del agente.
     * Recibe multipart/form-data con campo 'cv'.
     */
    public function uploadCV(Request $request)
    {
        $user = $request->user();
        if ($user->idRol != 3 && $user->idRol != 1) { // 1 = Admin, 3 = Agente
            return response()->json(['message' => 'Solo agentes o administradores pueden subir CV'], 403);
        }

        $validator = Validator::make($request->all(), [
            'cv' => 'required|mimes:pdf|max:5120', // 5MB limit
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Archivo inválido',
                'errors'  => $validator->errors()
            ], 422);
        }

        if ($user->cv_path && Storage::disk('public')->exists($user->cv_path)) {
            Storage::disk('public')->delete($user->cv_path);
        }

        $path = $request->file('cv')->store('agentes_cv', 'public');

        $user->cv_path = $path;
        $user->save();

        return response()->json([
            'message' => 'CV subido correctamente',
            'cv_path' => $path,
            'cv_url'  => url('api/files/' . $path)
        ]);
    }
}
