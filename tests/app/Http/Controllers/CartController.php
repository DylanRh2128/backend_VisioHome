<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Propiedad;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $cart = Cart::with(['items.propiedad' => function($q) {
            $q->with('imagenes');
        }])->firstOrCreate(['docUsuario' => $user->docUsuario]);

        foreach ($cart->items as $item) {
            $p = $item->propiedad;
            if ($p && $p->imagen_principal) {
                $p->imagen_principal = url('storage/' . $p->imagen_principal);
            }
        }

        return response()->json($cart);
    }

    public function add(Request $request)
    {
        $request->validate([
            'idPropiedad' => 'required|exists:propiedades,idPropiedad',
            'cantidad' => 'integer|min:1'
        ]);

        $user = $request->user();
        $cart = Cart::firstOrCreate(['docUsuario' => $user->docUsuario]);

        $item = CartItem::where('idCart', $cart->id)
            ->where('idPropiedad', $request->idPropiedad)
            ->first();

        if ($item) {
            $item->cantidad += $request->input('cantidad', 1);
            $item->save();
        } else {
            CartItem::create([
                'idCart' => $cart->id,
                'idPropiedad' => $request->idPropiedad,
                'cantidad' => $request->input('cantidad', 1)
            ]);
        }

        return response()->json(['success' => true, 'message' => 'Agregado al carrito']);
    }

    public function remove(Request $request, $id)
    {
        $user = $request->user();
        $cart = Cart::where('docUsuario', $user->docUsuario)->first();
        
        if ($cart) {
            CartItem::where('idCart', $cart->id)->where('idPropiedad', $id)->delete();
        }

        return response()->json(['success' => true]);
    }
}
