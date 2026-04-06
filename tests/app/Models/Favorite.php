<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Favorite extends Model
{
    protected $fillable = ['docUsuario', 'idPropiedad'];

    public function propiedad()
    {
        return $this->belongsTo(Propiedad::class, 'idPropiedad', 'idPropiedad');
    }

    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'docUsuario', 'docUsuario');
    }
}
