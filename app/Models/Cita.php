<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cita extends Model
{
    protected $table = 'citas';
    protected $primaryKey = 'idCita';
    public $timestamps = false;

    protected $fillable = [
        'idPropiedad',
        'docUsuario',
        'docAgente',
        'idDisponibilidad',
        'fecha',
        'estado',
        'canal',
        'precio',
        'notas'
    ];

    public function propiedad()
    {
        return $this->belongsTo(Propiedad::class, 'idPropiedad', 'idPropiedad');
    }

    public function agente()
    {
        return $this->belongsTo(Usuario::class, 'docAgente', 'docUsuario');
    }

    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'docUsuario', 'docUsuario');
    }

    public function pago()
    {
        return $this->hasOne(Pago::class, 'idCita', 'idCita');
    }
}