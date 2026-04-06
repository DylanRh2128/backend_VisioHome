<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Inmobiliaria extends Model
{
    protected $table = 'inmobiliarias';

    protected $primaryKey = 'nitInmobiliaria';
    public $incrementing = false;
    protected $keyType = 'string';

    public $timestamps = false;

    protected $fillable = [
        'nitInmobiliaria',
        'nombre',
        'correo',
        'telefono',
        'direccion',
        'objetivo',
        'logo_light',
        'logo_dark'
    ];
}
