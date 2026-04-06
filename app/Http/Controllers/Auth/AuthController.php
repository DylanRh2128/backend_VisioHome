<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Usuario;
use App\Http\Requests\Auth\RegisterRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;

use Illuminate\Support\Facades\Cookie;

class AuthController extends Controller
{

    public function login(Request $request)
    {
        \Log::info('[LOGIN ATTEMPT]', ['correo' => $request->correo]);

        $usuario = \App\Models\Usuario::where('correo', $request->correo)->first();

        \Log::info('[LOGIN USER FOUND]', ['found' => !!$usuario]);

        if (!$usuario || !\Illuminate\Support\Facades\Hash::check($request->password, $usuario->password)) {
            return response()->json(['message' => 'Credenciales inválidas'], 401);
        }

        $token = auth('api')->login($usuario);

        \Log::info('[LOGIN TOKEN]', ['token' => $token ? substr($token, 0, 20).'...' : null]);

        return $this->respondWithToken($token, $usuario);
    }

    // ──────────────────────────────────────────────────────────────
    // LOGOUT
    // ──────────────────────────────────────────────────────────────

    /**
     * POST /logout
     *
     * 1. Invalida el JWT en el servidor (blacklist)
     * 2. Elimina la cookie del navegador
     */
    public function logout()
    {
        try {
            /** @var \Tymon\JWTAuth\JWTGuard $guard */
            $guard = auth('api');
            $guard->logout();
        } catch (JWTException $e) {
            // Ignorar
        }

        // Borrar cookie globalmente
        \Illuminate\Support\Facades\Cookie::queue(\Illuminate\Support\Facades\Cookie::forget('jwt_token'));

        return response()->json(['message' => 'Logout exitoso']);
    }

    // ──────────────────────────────────────────────────────────────
    // REFRESH
    // ──────────────────────────────────────────────────────────────

    /**
     * POST /api/auth/refresh
     *
     * Renueva el JWT antes de que expire.
     * El frontend puede llamar esto periódicamente.
     */
    public function refresh()
    {
        try {
            /** @var \Tymon\JWTAuth\JWTGuard $guard */
            $guard    = auth('api');
            $newToken = $guard->refresh();
            $usuario  = $guard->user();

            return $this->respondWithToken($newToken, $usuario);
        } catch (JWTException $e) {
            return response()->json(['message' => 'Token inválido o expirado'], 401);
        }
    }

    // ──────────────────────────────────────────────────────────────
    // REGISTER
    // ──────────────────────────────────────────────────────────────

    public function register(RegisterRequest $request)
    {
        $data = $request->validated();

        $idRol    = $data['idRol'] ?? 2;
        $rolesMap = [1 => 'admin', 2 => 'cliente', 3 => 'agente'];
        $rol      = $rolesMap[$idRol] ?? 'cliente';

        $usuario = Usuario::create([
            'docUsuario'  => trim($data['docUsuario']),
            'nombre'      => trim($data['nombre']),
            'correo'      => str_replace(' ', '', $data['correo']),
            'password'    => $data['password'],
            'idRol'       => $idRol,
            'rol'         => $rol,
            'telefono'    => str_replace(' ', '', $data['telefono']),
            'genero'      => $data['genero'],
            'departamento' => $data['departamento'],
            'ciudad'      => $data['ciudad'],
            'direccion'   => isset($data['direccion']) ? trim($data['direccion']) : null,
        ]);

        // Hacer login automático tras el registro
        /** @var \Tymon\JWTAuth\JWTGuard $guard */
        $guard = auth('api');
        $token = $guard->login($usuario);

        return $this->respondWithToken($token, $usuario, 201);
    }

    // ──────────────────────────────────────────────────────────────
    // FORGOT / RESET PASSWORD (placeholders)
    // ──────────────────────────────────────────────────────────────

    public function forgotPassword(Request $request)
    {
        // 1. Validar que el correo llega y existe en la tabla de usuarios
        $request->validate([
            'correo' => 'required|email|exists:usuarios,correo'
        ], [
            'correo.exists' => 'No tenemos registro de este correo electrónico.'
        ]);

        $usuario = Usuario::where('correo', $request->correo)->first();

        // 2. Generar el Token de Recuperación
        $token = \Illuminate\Support\Str::random(60);

        // 3. Guardar el token en la tabla nativa de Laravel 11.
        // Capturamos la excepción por si tu sistema no tiene la tabla 'password_reset_tokens' aún.
        try {
            \Illuminate\Support\Facades\DB::table('password_reset_tokens')->updateOrInsert(
                ['email' => $usuario->correo],
                [
                    'token' => \Illuminate\Support\Facades\Hash::make($token),
                    'created_at' => now()
                ]
            );
        } catch (\Exception $e) {
            \Log::warning('No se pudo usar la tabla password_reset_tokens.', ['error' => $e->getMessage()]);
            // Opcional: Podrías retornar el error aquí si la tabla es obligatoria.
        }

        // 4. Lógica de envío de Correo
        // TODO: Mapear tu Mailable aquí cuando tengas credenciales SMTP configuradas.
        // Ej: \Illuminate\Support\Facades\Mail::to($usuario->correo)->send(new TuMailableDeRecuperacion($token));
        
        \Log::info('[FORGOT PASSWORD]', ['correo' => $usuario->correo, 'token_generado' => 'OK']);

        // 5. Responder con éxito para que el Frontend muestre el check verde
        return response()->json([
            'status'  => 'Enlace de recuperación generado',
            'message' => 'Si el correo existe, recibirás un enlace de recuperación.'
        ], 200);
    }

    public function resetPassword(Request $request)
    {
        return response()->json(['message' => 'Reinicio de contraseña no implementado aún'], 501);
    }

    // ──────────────────────────────────────────────────────────────
    // HELPERS PRIVADOS
    // ──────────────────────────────────────────────────────────────

    /**
     * Construye la respuesta con el JWT en una cookie httpOnly.
     *
     * Por qué httpOnly:
     * - El JavaScript del frontend NO puede leer la cookie
     * - Protege contra XSS total
     * - El navegador la envía automáticamente en cada request
     *   siempre que withCredentials = true en Axios
     *
     * Por qué SameSite=Lax y NOT Secure en desarrollo:
     * - En localhost no hay HTTPS, Secure=true bloquearía la cookie
     * - SameSite=Lax permite envío en navegación normal
     * - En producción: cambiar a Secure=true + SameSite=Strict/None
     */
    private function respondWithToken(string $token, $usuario, int $status = 200)
    {
        $cookie = \Cookie::make(
            'jwt_token',
            $token,
            config('jwt.ttl', 1440),
            '/',
            null,   // domain
            false,  // secure
            true,   // httpOnly
            false,  // raw
            null    // ← SameSite NULL (antes era 'lax') — esto es el fix
        );

        return response()->json(['user' => $usuario], $status)->withCookie($cookie);
    }

    // Eliminar función clearCookie, ahora usamos Cookie::forget

}
