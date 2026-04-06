<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class PasswordResetController extends Controller
{
    /**
     * Enviar enlace de recuperación de contraseña
     */
    public function sendResetLink(Request $request)
    {
        $request->validate([
            'correo' => ['required', 'email', 'exists:usuarios,correo']
        ]);

        Log::info('Password reset request received', [
            'correo' => $request->correo,
            'ip' => $request->ip()
        ]);

        // Usamos el broker configurado en auth.php que mapea a la tabla password_reset_tokens y el provider users
        $status = Password::broker('usuarios')->sendResetLink([
            'correo' => $request->correo
        ]);

        if ($status === Password::RESET_LINK_SENT) {

            Log::info('Password reset email sent', [
                'correo' => $request->correo
            ]);

            return response()->json([
                'success' => true,
                'message' => __($status)
            ], 200);
        }

        Log::warning('Password reset failed', [
            'correo' => $request->correo,
            'status' => $status
        ]);

        return response()->json([
            'success' => false,
            'message' => __($status)
        ], 400);
    }

    /**
     * Restablecer contraseña
     */
    public function resetPassword(Request $request)
    {
        $request->validate([
            'token' => ['required'],
            'correo' => ['required', 'email', 'exists:usuarios,correo'],
            'password' => ['required', 'min:8', 'confirmed']
        ]);

        Log::info('Password reset attempt', [
            'correo' => $request->correo,
            'ip' => $request->ip()
        ]);

        $status = Password::broker('usuarios')->reset(
            [
                'correo' => $request->correo,
                'password' => $request->password,
                'password_confirmation' => $request->password_confirmation,
                'token' => $request->token,
            ],
            function ($user) use ($request) {

                $user->forceFill([
                    'password' => Hash::make($request->password)
                ])->save();

                $user->setRememberToken(Str::random(60));

                // invalidar sesiones previas
                Auth::logoutOtherDevices($request->password);

                // iniciar sesión automáticamente
                Auth::login($user);

                Log::info('Password successfully updated', [
                    'user_id' => $user->docUsuario,
                    'correo' => $user->correo
                ]);
            }
        );

        if ($status === Password::PASSWORD_RESET) {

            return response()->json([
                'success' => true,
                'message' => 'Contraseña restablecida correctamente'
            ], 200);
        }

        Log::warning('Password reset token invalid or expired', [
            'correo' => $request->correo,
            'status' => $status
        ]);

        return response()->json([
            'success' => false,
            'message' => __($status)
        ], 400);
    }
}