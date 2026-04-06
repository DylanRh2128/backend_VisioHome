<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CartItem extends Model
{
    protected $table = 'cart_items';
    protected $fillable = ['idCart', 'idPropiedad', 'cantidad'];

    public function cart()
    {
        return $this->belongsTo(Cart::class, 'idCart', 'id');
    }

    public function propiedad()
    {
        return $this->belongsTo(Propiedad::class, 'idPropiedad', 'idPropiedad');
    }
}
