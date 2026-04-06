<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Auth;
use App\Auth\CorreoUserProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // El override aquí no funciona por culpa del provider diferido.
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->app->extend('auth.password', function ($manager, $app) {
            return new class($app) extends \Illuminate\Auth\Passwords\PasswordBrokerManager {
                protected function createTokenRepository(array $config)
                {
                    $key = $this->app['config']['app.key'];
                    if (\Illuminate\Support\Str::startsWith($key, 'base64:')) {
                        $key = base64_decode(substr($key, 7));
                    }
                    $connection = $config['connection'] ?? null;
                    return new \App\Auth\CorreoTokenRepository(
                        $this->app['db']->connection($connection),
                        $this->app['hash'],
                        $config['table'],
                        $key,
                        $config['expire'],
                        $config['throttle'] ?? 0
                    );
                }
            };
        });

        // Debug Sanctum/Sessions en el inicio de la petición
        \Log::info('[Boot] Request Debug:', [
            'url' => request()->fullUrl(),
            'cookies' => request()->cookies->all(),
            'has_session' => request()->hasSession(),
        ]);

        // Registrar proveedor custom que mapea 'email' -> 'correo' en tabla usuarios
        Auth::provider('correo_eloquent', function ($app, array $config) {
            return new CorreoUserProvider($app['hash'], $config['model']);
        });
    }
}
