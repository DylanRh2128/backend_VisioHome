<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Str;

class GoogleAuthController extends Controller
{
    public function redirect()
    {
        return Socialite::driver('google')->redirect();
    }

    public function handleGoogleCallback(Request $request)
{
    $frontendUrl = env('FRONTEND_URL', 'http://localhost:5173');

    try {
        if (env('APP_ENV') === 'local') {
            Socialite::driver('google')->setHttpClient(
                new \GuzzleHttp\Client(['verify' => false])
            );
        }

        $googleUser = Socialite::driver('google')->stateless()->user();
        $user = Usuario::where('correo', $googleUser->getEmail())->first();

        if ($user) {
            $updates = [];
            if (empty($user->google_id))         $updates['google_id']         = $googleUser->getId();
            if (empty($user->avatar))             $updates['avatar']             = $googleUser->getAvatar();
            if (empty($user->email_verified_at))  $updates['email_verified_at']  = now();
            if (!empty($updates)) $user->update($updates);
        } else {
            $user = Usuario::create([
                'docUsuario'        => (string) Str::uuid(),
                'nombre'            => $googleUser->getName(),
                'correo'            => $googleUser->getEmail(),
                'google_id'         => $googleUser->getId(),
                'avatar'            => $googleUser->getAvatar(),
                'email_verified_at' => now(),
                'password'          => \Hash::make(Str::random(16)),
                'idRol'             => 2,
                'genero'            => 'prefiero_no_decirlo',
                'departamento'      => 'Sin asignar',
                'activo'            => 1,
            ]);
        }

        $token = auth('api')->login($user);

        // ✅ Adjuntar el token como Cookie en la redirección
        // Params: name, value, minutes (1440 = 24h), path, domain, secure (false en dev), httpOnly (false para que JS lo pueda ver si es necesario)
        $cookie = cookie('jwt_token', $token, 1440, '/', null, false, false);

        \Log::info('OAuth token stored in cookie, redirecting...');

        // ✅ Redirect limpio sin token en URL, adjuntando la cookie
        return redirect("{$frontendUrl}/auth/google/success")->withCookie($cookie);

    } catch (\Throwable $e) {
        \Log::error('Google Auth Error', ['message' => $e->getMessage()]);
        return redirect("{$frontendUrl}/auth/google/error");
    }
}
}