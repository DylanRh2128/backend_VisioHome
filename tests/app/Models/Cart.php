<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    protected $table = 'carts';
    protected $fillable = ['docUsuario'];

    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'docUsuario', 'docUsuario');
    }

    public function items()
    {
        return $this->hasMany(CartItem::class, 'idCart', 'id');
    }
}
