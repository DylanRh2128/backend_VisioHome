<?php

namespace App\Http\Controllers;

use App\Models\Propiedad;
use Illuminate\Http\Request;

class PropiedadController extends Controller
{
    // GET /api/propiedades
    public function index()
    {
        $propiedades = Propiedad::with('imagenes')->orderBy('idPropiedad', 'desc')->get();
        
        $propiedades->transform(function ($p) {
            if ($p->imagen_principal) {
                $p->imagen_principal = url('storage/' . $p->imagen_principal);
            }
            return $p;
        });

        return response()->json($propiedades);
    }

    // POST /api/propiedades
    public function store(Request $request)
    {
        $data = $request->validate([
            'titulo' => 'required|string|max:200',
            'descripcion' => 'nullable|string',
            'ubicacion' => 'required|string|max:255',
            'latitud' => 'nullable|numeric',
            'longitud' => 'nullable|numeric',
            'habitaciones' => 'nullable|integer',
            'banos' => 'nullable|integer',
            'tamano_m2' => 'nullable|numeric',
            'precio' => 'required|numeric',
            'estado' => 'required|string',
            'tipo' => 'required|string',
            'nitInmobiliaria' => 'required|string',
            'imagen_principal' => 'nullable|string',
        ]);

        $data['creado_en'] = now();

        $propiedad = Propiedad::create($data);

        return response()->json($propiedad, 201);
    }

    // GET /api/propiedades/{id}
    public function show($id)
    {
        $propiedad = Propiedad::with('imagenes')->findOrFail($id);
        
        if ($propiedad->imagen_principal) {
            $propiedad->imagen_principal = url('storage/' . $propiedad->imagen_principal);
        }

        foreach($propiedad->imagenes as $img) {
            $img->urlImagen = url('storage/' . $img->urlImagen);
        }

        return response()->json($propiedad);
    }

    // PUT /api/propiedades/{id}
    public function update(Request $request, $id)
    {
        $propiedad = Propiedad::findOrFail($id);

        $data = $request->validate([
            'titulo' => 'sometimes|string|max:200',
            'descripcion' => 'nullable|string',
            'ubicacion' => 'sometimes|string|max:255',
            'latitud' => 'nullable|numeric',
            'longitud' => 'nullable|numeric',
            'habitaciones' => 'nullable|integer',
            'banos' => 'nullable|integer',
            'tamano_m2' => 'nullable|numeric',
            'precio' => 'sometimes|numeric',
            'estado' => 'sometimes|string',
            'tipo' => 'sometimes|string',
            'nitInmobiliaria' => 'sometimes|string',
            'imagen_principal' => 'nullable|string',
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
