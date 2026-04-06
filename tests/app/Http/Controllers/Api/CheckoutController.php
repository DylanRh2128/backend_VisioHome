<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\Pago;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CheckoutController extends Controller
{
    public function process(Request $request)
    {
        $user = $request->user();
        $cart = Cart::with('items.propiedad')->where('docUsuario', $user->docUsuario)->first();

        if (!$cart || $cart->items->isEmpty()) {
            return response()->json(['error' => 'El carrito está vacío'], 400);
        }

        return DB::transaction(function() use ($user, $cart) {
            $totalMonto = 0;
            foreach ($cart->items as $item) {
                // La reserva es el 5% del precio de la propiedad
                $totalMonto += ($item->propiedad->precio * 0.05);
            }

            // Simulación de Pago/Orden
            $pago = Pago::create([
                'docUsuario' => $user->docUsuario,
                'monto' => $totalMonto,
                'fecha' => now(),
                'estado' => 'aprobado',
                'metodo' => 'simulado',
                'notas' => 'Reserva del 5% realizada desde el módulo usuario'
            ]);

            // Vaciar carrito
            $cart->items()->delete();

            return response()->json([
                'success' => true,
                'message' => 'Reserva realizada con éxito',
                'order_id' => $pago->idPago ?? $pago->id
            ]);
        });
    }
}
