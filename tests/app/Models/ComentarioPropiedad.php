<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ComentarioPropiedad extends Model
{
    protected $table = 'comentarios_propiedad';
    protected $primaryKey = 'idComentario';
    
    // The table uses 'fecha' instead of 'created_at' and 'updated_at'
    public $timestamps = false;

    protected $fillable = [
        'idPropiedad',
        'docUsuario',
        'comentario',
        'puntuacion',
        'fecha'
    ];

    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'docUsuario', 'docUsuario');
    }

    public function propiedad()
    {
        return $this->belongsTo(Propiedad::class, 'idPropiedad', 'idPropiedad');
    }
}
