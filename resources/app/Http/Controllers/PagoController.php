<?php

namespace App\Http\Controllers;

use App\Models\Pago;
use Illuminate\Http\Request;

class PagoController extends Controller
{
    // GET /api/pagos
    public function index()
    {
        return response()->json(
            Pago::with('propiedad')
                ->orderBy('fecha', 'desc')
                ->get()
        );
    }

    // POST /api/pagos
    public function store(Request $request)
    {
        $data = $request->validate([
            'docUsuario' => 'required|string',
            'idPropiedad' => 'required|exists:propiedades,idPropiedad',
            'monto' => 'required|numeric',
            'metodoPago' => 'required|string',
            'estado' => 'required|string',
            'referencia' => 'nullable|string',
        ]);

        $data['fecha'] = now();

        $pago = Pago::create($data);

        return response()->json($pago, 201);
    }

    // GET /api/pagos/{id}
    public function show($id)
    {
        return response()->json(
            Pago::with('propiedad')->findOrFail($id)
        );
    }

    // PUT /api/pagos/{id}
    public function update(Request $request, $id)
    {
        $pago = Pago::findOrFail($id);

        $data = $request->validate([
            'monto' => 'sometimes|numeric',
            'metodoPago' => 'sometimes|string',
            'estado' => 'sometimes|string',
            'referencia' => 'nullable|string',
        ]);

        $pago->update($data);

        return response()->json($pago);
    }

    // DELETE /api/pagos/{id}
    public function destroy($id)
    {
        Pago::findOrFail($id)->delete();

        return response()->json([
            'message' => 'Pago eliminado correctamente'
        ]);
    }
}
