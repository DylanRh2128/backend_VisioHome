<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Propiedad extends Model
{
    protected $table = 'propiedades';
    protected $primaryKey = 'idPropiedad';
    public $timestamps = false;

    /**
     * Accesor para la imagen de la propiedad.
     * Detecta si es una URL externa o local para evitar duplicidad de dominios.
     */
    public function getImagenAttribute($value)
    {
        if (empty($value)) {
            return asset('assets/placeholder-home.png');
        }

        if (str_starts_with($value, 'http')) {
            return $value;
        }

        $clean = ltrim($value, '/');

        if (str_starts_with($clean, 'storage/')) {
            $clean = substr($clean, 8);
        }

        return url("api/files/{$clean}");
    }

    public function getModelo3dPathAttribute($value)
    {
        if (empty($value)) {
            return null;
        }

        if (str_starts_with($value, 'http')) {
            return $value;
        }

        $clean = ltrim($value, '/');

        if (str_starts_with($clean, 'storage/')) {
            $clean = substr($clean, 8);
        }

        return url("api/files/{$clean}");
    }

    protected static function booted()
    {
        static::created(function ($propiedad) {
            $directory = "properties/propiedad_{$propiedad->idPropiedad}";
            if (!\Illuminate\Support\Facades\Storage::disk('public')->exists($directory)) {
                \Illuminate\Support\Facades\Storage::disk('public')->makeDirectory($directory);
            }
        });
    }

    /**
     * Clasifica una ciudad en las categorías: principal, secundaria o terciaria.
     * @param string|null $ciudad
     * @return string
     */
    public static function clasificarCiudad($ciudad)
    {
        if (!$ciudad) return 'terciaria';

        $ciudad = strtolower(trim($ciudad));

        // Normalización básica (reemplazo de tildes omitido para simplicidad si se usan ambas formas en el array)
        $principales = [
            'bogota', 'bogotá', 'medellin', 'medellín',
            'cali', 'barranquilla', 'cartagena'
        ];

        $secundarias = [
            'bucaramanga', 'pereira', 'ibague', 'ibagué',
            'santa marta', 'villavicencio', 'cucuta', 'cúcuta',
            'manizales', 'monteria', 'montería', 'pasto', 'armenia'
        ];

        if (in_array($ciudad, $principales)) return 'principal';
        if (in_array($ciudad, $secundarias)) return 'secundaria';

        return 'terciaria';
    }

    protected $fillable = [
        'titulo',
        'descripcion',
        'ubicacion',
        'ciudad',
        'tamano_m2',
        'precio',
        'estado',
        'tipo',
        'nitInmobiliaria',
        'imagen',
        'modelo_3d_path',
        'categoria_ciudad',
        'creado_en',
        'actualizado_en',
    ];

    // Relación: una propiedad tiene muchos pagos
    public function pagos()
    {
        return $this->hasMany(Pago::class, 'idPropiedad', 'idPropiedad');
    }

    // Relación: una propiedad tiene muchos favoritos
    public function favoritos()
    {
        return $this->hasMany(Favorite::class, 'idPropiedad', 'idPropiedad');
    }

    // Relación: una propiedad tiene muchos comentarios
    public function comentarios()
    {
        return $this->hasMany(Comment::class, 'idPropiedad', 'idPropiedad');
    }

    // Relación: una propiedad tiene muchas citas
    public function citas()
    {
        return $this->hasMany(Cita::class, 'idPropiedad', 'idPropiedad');
    }
}
