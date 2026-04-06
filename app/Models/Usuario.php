<?php

namespace App\Models;

use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Usuario extends Authenticatable implements JWTSubject
{
    use Notifiable, CanResetPassword, HasFactory;

    // ─────────────────────────────────────────────────────────────
    // GÉNERO - Opciones exactas de base de datos
    // ─────────────────────────────────────────────────────────────
    public const GENERO_MASCULINO = 'Masculino';
    public const GENERO_FEMENINO = 'Femenino';
    public const GENERO_PREFIERO_NO_DECIRLO = 'prefiero_no_decirlo';

    protected $table = 'usuarios';

    protected $primaryKey = 'docUsuario';
    public $incrementing = false;

    protected $keyType = 'string';

    const CREATED_AT = 'creado_en';
    const UPDATED_AT = 'actualizado_en';

    protected $fillable = [
        'docUsuario',
        'nombre',
        'correo',
        'telefono',
        'direccion',
        'password',
        'idRol',
        'rol',
        'genero',
        'departamento',
        'avatar',
        'ciudad',
        'activo',
        'login_count',
        'intentosFallidos',
        'bloqueadoHasta',
        'google_id',
        'email_verified_at',
        'cv_path',
        'especialidad',
        'biografia',
        'carrera',
        'experiencia_anos',
        'nitInmobiliaria'
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'bloqueadoHasta'    => 'datetime',
        'activo'            => 'boolean',
    ];

    protected $hidden = [
        'password',
        'remember_token'
    ];

    public function favorites()
    {
        return $this->belongsToMany(Propiedad::class, 'favorites', 'docUsuario', 'idPropiedad')->withTimestamps();
    }

    public function favoritos()
    {
        return $this->hasMany(Favorite::class, 'docUsuario', 'docUsuario');
    }

    public function comentarios()
    {
        return $this->hasMany(Comment::class, 'docUsuario', 'docUsuario');
    }

    public function citasCliente()
    {
        return $this->hasMany(Cita::class, 'docUsuario', 'docUsuario');
    }

    public function citasAgente()
    {
        return $this->hasMany(Cita::class, 'docAgente', 'docUsuario');
    }

    public function disponibilidades()
    {
        return $this->hasMany(Disponibilidad::class, 'docAgente', 'docUsuario');
    }

    public function scopeAdmins($query)
    {
        return $query->where('rol', 'admin');
    }

    public function scopeClientes($query)
    {
        return $query->where('rol', 'cliente');
    }

    public function scopeAgentes($query)
    {
        return $query->where('rol', 'agente');
    }

    /**
     * Mutador de Contraseña — Encriptación Automática
     */
    public function setPasswordAttribute($value)
    {
        if (empty($value)) return;

        // Evitar doble encriptación
        if (\Illuminate\Support\Facades\Hash::needsRehash($value)) {
            $this->attributes['password'] = \Illuminate\Support\Facades\Hash::make($value);
        } else {
            $this->attributes['password'] = $value;
        }
    }

    /**
     * Get the e-mail address where password reset links are sent.
     *
     * @return string
     */
   /**
 * Indica a Laravel que use la columna 'correo' para la recuperación de contraseña.
 */
    public function getEmailForPasswordReset()
    {
        return $this->correo;
    }

    /**
     * Route notifications for the mail channel.
     * Laravel busca 'email' por defecto; aquí apuntamos a 'correo'.
     */
    public function routeNotificationForMail($notification = null)
    {
        return $this->correo;
    }

    /**
     * Overrride para apuntar el enlace de reset al frontend React
     * usando la notificación personalizada.
     */
    public function sendPasswordResetNotification($token): void
    {
        \Illuminate\Support\Facades\Log::info('Password reset notification triggered for: ' . $this->correo);

        $this->notify(new \App\Notifications\ResetPasswordNotification($token));
    }

    // ─────────────────────────────────────────────────────────────
    // Boot — sincroniza idRol ↔ rol
    // ─────────────────────────────────────────────────────────────

    protected static function boot()
    {
        parent::boot();

        static::saving(function ($usuario) {
            if ($usuario->isDirty('idRol')) {
                $rolesMap = [1 => 'admin', 2 => 'cliente', 3 => 'agente'];
                $usuario->rol = $rolesMap[(int)$usuario->idRol] ?? 'cliente';
            }
        });
    }

    // ─────────────────────────────────────────────────────────────
    // JWT Subject (requerido por tymon/jwt-auth)
    // ─────────────────────────────────────────────────────────────

    // Usuario.php — reemplaza getAuthIdentifierName()
    // ─────────────────────────────────────────────────────────────
// JWT Subject (requerido por tymon/jwt-auth)
// ─────────────────────────────────────────────────────────────


    public function getAuthPassword(): string
    {
        return $this->password;
    }

    public function getJWTIdentifier(): mixed
    {
        return $this->getKey(); // retorna docUsuario ← FALTA EN TU ARCHIVO ACTUAL
    }

    public function getJWTCustomClaims(): array
    {
        return [
            'rol' => $this->rol,
        ];
    }
}
