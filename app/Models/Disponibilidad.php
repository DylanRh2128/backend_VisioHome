<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Disponibilidad extends Model
{
    protected $table      = 'disponibilidades';
    protected $primaryKey = 'idDisponibilidad';

    protected $fillable = [
        'docAgente',
        'dia_semana',
        'hora_inicio',
        'hora_fin',
        'estado',
    ];

    // ─── Relaciones ────────────────────────────────────────────────────────

    public function agente()
    {
        return $this->belongsTo(Usuario::class, 'docAgente', 'docUsuario');
    }
}
