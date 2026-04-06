<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cita extends Model
{
    protected $table = 'citas';

    protected $primaryKey = 'idCita';

    const CREATED_AT = 'creado_en';
    public $timestamps = true;

    protected $fillable = [
        'idPropiedad',
        'docUsuario',
        'docAgente',
        'fecha',
        'estado',
        'canal',
        'notas'
    ];
}
