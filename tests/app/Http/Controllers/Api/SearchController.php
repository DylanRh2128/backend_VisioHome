<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Propiedad;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    public function search(Request $request)
    {
        $query = Propiedad::query()->where('estado', 'disponible');

        if ($request->has('query')) {
            $q = $request->input('query');
            $query->where(function($sub) use ($q) {
                $sub->where('titulo', 'like', "%{$q}%")
                    ->orWhere('descripcion', 'like', "%{$q}%")
                    ->orWhere('ubicacion', 'like', "%{$q}%");
            });
        }

        if ($request->has('min_price')) {
            $query->where('precio', '>=', $request->input('min_price'));
        }

        if ($request->has('max_price')) {
            $query->where('precio', '<=', $request->input('max_price'));
        }

        if ($request->has('type') && $request->input('type') != '') {
            $query->where('tipo', $request->input('type'));
        }

        if ($request->has('habitaciones') && $request->input('habitaciones') != '') {
            $query->where('habitaciones', '>=', $request->input('habitaciones'));
        }

        $results = $query->paginate(12);

        $results->getCollection()->transform(function ($p) {
            if ($p->imagen_principal) {
                $p->imagen_principal = url('storage/' . $p->imagen_principal);
            }
            return $p;
        });

        return response()->json($results);
    }
}
