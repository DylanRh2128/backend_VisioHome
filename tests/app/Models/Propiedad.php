<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Propiedad extends Model
{
    protected $table = 'propiedades';
    protected $primaryKey = 'idPropiedad';
    public $timestamps = false;

    protected $fillable = [
        'titulo',
        'descripcion',
        'ubicacion',
        'latitud',
        'longitud',
        'habitaciones',
        'banos',
        'tamano_m2',
        'precio',
        'estado',
        'tipo',
        'imagen_principal',
        'nitInmobiliaria',
        'creado_en',
        'actualizado_en',
    ];

    // Relación: una propiedad tiene muchos pagos
    public function pagos()
    {
        return $this->hasMany(Pago::class, 'idPropiedad', 'idPropiedad');
    }

    // Relación: una propiedad tiene muchas imágenes
    public function imagenes()
    {
        return $this->hasMany(PropiedadImagen::class, 'idPropiedad', 'idPropiedad');
    }
}
