<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Models\Usuario;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function register(RegisterRequest $request)
    {
        $data = $request->validated();
        
        // El mutador en el modelo Usuario ya se encarga de Hash::make
        $usuario = Usuario::create([
            'docUsuario' => $data['docUsuario'],
            'nombre' => $data['nombre'],
            'correo' => $data['correo'],
            'telefono' => $data['telefono'] ?? null,
            'direccion' => $data['direccion'] ?? null,
            'password' => $data['password'],
            'idRol' => 2 // Default: cliente
        ]);

        return response()->json([
            'message' => 'Usuario registrado exitosamente',
            'usuario' => $usuario
        ], 201);
    }

    public function login(LoginRequest $request)
    {
        $data = $request->validated();
        $usuario = Usuario::where('correo', $data['correo'])->first();

        if (!$usuario) {
            return response()->json(['message' => 'Credenciales inválidas'], 401);
        }

        // Verificar si está bloqueado
        if ($usuario->bloqueadoHasta && Carbon::parse($usuario->bloqueadoHasta)->isFuture()) {
            return response()->json([
                'message' => 'Cuenta bloqueada temporalmente',
                'bloqueadoHasta' => $usuario->bloqueadoHasta,
                'minutosRestantes' => Carbon::now()->diffInMinutes($usuario->bloqueadoHasta)
            ], 423);
        }

        // Verificar password
        if (Hash::check($data['password'], $usuario->password)) {
            // Login exitoso: Resetear intentos
            $usuario->update([
                'intentosFallidos' => 0,
                'bloqueadoHasta' => null
            ]);

            $token = $usuario->createToken('auth_token')->plainTextToken;

            return response()->json([
                'message' => 'Login exitoso',
                'usuario' => $usuario,
                'token' => $token
            ], 200);
        }

        // Login fallido: Incrementar intentos
        $usuario->intentosFallidos += 1;
        
        if ($usuario->intentosFallidos >= 3) {
            $nivelBloqueo = $usuario->intentosFallidos - 2;
            $minutosBloqueo = 15 * $nivelBloqueo;
            $usuario->bloqueadoHasta = Carbon::now()->addMinutes($minutosBloqueo);
            
            $usuario->save();

            return response()->json([
                'message' => 'Demasiados intentos fallidos. Cuenta bloqueada.',
                'bloqueadoHasta' => $usuario->bloqueadoHasta,
                'intentosFallidos' => $usuario->intentosFallidos
            ], 423);
        }

        $usuario->save();

        return response()->json([
            'message' => 'Credenciales inválidas',
            'intentosRestantes' => 3 - $usuario->intentosFallidos
        ], 401);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json(['message' => 'Sesión cerrada'], 200);
    }
}
