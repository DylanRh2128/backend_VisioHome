<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    protected $table = 'comentarios_propiedad';
    protected $primaryKey = 'idComentario';
    public $timestamps = false;

    const CREATED_AT = 'fecha';

    protected $fillable = [
        'docUsuario',
        'idPropiedad',
        'comentario',
        'puntuacion',
        'fecha',
    ];

    protected $casts = [
        'fecha' => 'datetime',
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
