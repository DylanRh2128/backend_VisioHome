<?php

namespace App\Http\Controllers;

use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'correo' => 'required|email',
            'password' => 'required'
        ]);

        $usuario = Usuario::where('correo', $request->correo)->first();

        if (!$usuario || !Hash::check($request->password, $usuario->password)) {
            return response()->json([
                'message' => 'Credenciales inválidas'
            ], 401);
        }

        // 🔐 Revocar tokens anteriores (seguridad)
        $usuario->tokens()->delete();

        // 🔑 Crear token Sanctum
        $token = $usuario->createToken('visiohome-api')->plainTextToken;

        return response()->json([
            'message' => 'Login exitoso',
            'token' => $token,
            'usuario' => $usuario
        ]);
    }

    public function register(Request $request)
    {
        $request->validate([
            'docUsuario' => 'required|unique:usuarios',
            'nombre' => 'required',
            'correo' => 'required|email|unique:usuarios',
            'password' => 'required|min:6',
            'idRol' => 'required'
        ]);

        $usuario = Usuario::create([
            'docUsuario' => $request->docUsuario,
            'nombre' => $request->nombre,
            'correo' => $request->correo,
            'password' => $request->password, // mutator hace el hash
            'idRol' => $request->idRol,
        ]);

        return response()->json([
            'message' => 'Usuario registrado',
            'usuario' => $usuario
        ], 201);
    }

    // 🚪 Logout por token
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Token revocado'
        ]);
    }
}
