<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PropiedadImagen extends Model
{
    protected $table = 'propiedad_imagenes';

    protected $primaryKey = 'idImagen';

    const CREATED_AT = 'creado_en';
    public $timestamps = true;

    protected $fillable = [
        'idPropiedad',
        'urlImagen',
        'orden'
    ];
}
