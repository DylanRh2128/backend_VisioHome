<?php

namespace App\Auth;

use Illuminate\Auth\EloquentUserProvider;

/**
 * Custom User Provider que mapea el campo 'email' a 'correo'
 * para que el Password Broker de Laravel pueda encontrar usuarios
 * en la tabla `usuarios` donde el campo de correo se llama `correo`.
 */
class CorreoUserProvider extends EloquentUserProvider
{
    /**
     * Si el array de credenciales tiene la clave 'email'
     * (usada internamente por el Password Broker de Laravel),
     * la reemplazamos por 'correo' antes de consultar la BD.
     */
    public function retrieveByCredentials(array $credentials): mixed
    {
        if (isset($credentials['email']) && !isset($credentials['correo'])) {
            $credentials['correo'] = $credentials['email'];
            unset($credentials['email']);
        }

        return parent::retrieveByCredentials($credentials);
    }
}
