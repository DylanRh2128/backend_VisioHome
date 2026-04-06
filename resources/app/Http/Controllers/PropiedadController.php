<?php

namespace App\Http\Controllers;

use App\Models\Propiedad;
use Illuminate\Http\Request;

class PropiedadController extends Controller
{
    // GET /api/propiedades
    public function index()
    {
        return response()->json(
            Propiedad::orderBy('creado_en', 'desc')->get()
        );
    }

    // POST /api/propiedades
    public function store(Request $request)
    {
        $data = $request->validate([
            'titulo' => 'required|string|max:200',
            'descripcion' => 'nullable|string',
            'ubicacion' => 'required|string|max:255',
            'tamano_m2' => 'nullable|numeric',
            'precio' => 'required|numeric',
            'estado' => 'required|string',
            'tipo' => 'required|string',
            'nitInmobiliaria' => 'required|string',
        ]);

        $data['creado_en'] = now();

        $propiedad = Propiedad::create($data);

        return response()->json($propiedad, 201);
    }

    // GET /api/propiedades/{id}
    public function show($id)
    {
        return response()->json(
            Propiedad::findOrFail($id)
        );
    }

    // PUT /api/propiedades/{id}
    public function update(Request $request, $id)
    {
        $propiedad = Propiedad::findOrFail($id);

        $data = $request->validate([
            'titulo' => 'sometimes|string|max:200',
            'descripcion' => 'nullable|string',
            'ubicacion' => 'sometimes|string|max:255',
            'tamano_m2' => 'nullable|numeric',
            'precio' => 'sometimes|numeric',
            'estado' => 'sometimes|string',
            'tipo' => 'sometimes|string',
            'nitInmobiliaria' => 'sometimes|string',
        ]);

        $data['actualizado_en'] = now();

        $propiedad->update($data);

        return response()->json($propiedad);
    }

    // DELETE /api/propiedades/{id}
    public function destroy($id)
    {
        Propiedad::findOrFail($id)->delete();

        return response()->json([
            'message' => 'Propiedad eliminada correctamente'
        ]);
    }
}
