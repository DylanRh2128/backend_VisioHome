<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ValoracionAgente extends Model
{
    protected $table = 'valoraciones_agentes';
    protected $primaryKey = 'idValoracion';
    public $timestamps = false;

    protected $fillable = [
        'docAgente',
        'docUsuario',
        'puntuacion',
        'comentario',
        'creado_en'
    ];

    public function agente()
    {
        return $this->belongsTo(Usuario::class, 'docAgente', 'docUsuario');
    }

    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'docUsuario', 'docUsuario');
    }
}
