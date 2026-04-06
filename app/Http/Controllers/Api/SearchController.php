<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Propiedad;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    public function search(Request $request)
    {
        try {
            $query = Propiedad::query()
                ->where('estado', 'disponible');

            // 🔎 Búsqueda general
            if ($request->filled('search')) {
                $s = $request->input('search');
                $query->where(function($sub) use ($s) {
                    $sub->where('titulo', 'like', "%{$s}%")
                        ->orWhere('descripcion', 'like', "%{$s}%")
                        ->orWhere('ubicacion', 'like', "%{$s}%");
                        // ❌ quitar ciudad si no existe la columna
                });
            }

            // 💰 Filtros
            if ($request->filled('precio_min')) {
                $query->where('precio', '>=', $request->input('precio_min'));
            }

            if ($request->filled('precio_max')) {
                $query->where('precio', '<=', $request->input('precio_max'));
            }

            if ($request->filled('type')) {
                $query->where('tipo', $request->input('type'));
            }

            if ($request->filled('habitaciones')) {
                $query->where('habitaciones', '>=', $request->input('habitaciones'));
            }

            // ❌ quitar ciudad si no existe
            // if ($request->filled('ciudad')) {
            //     $query->where('ciudad', $request->input('ciudad'));
            // }

            // Orden
            $orderBy = $request->input('order_by', 'creado_en');
            $orderDir = $request->input('order_dir', 'desc');
            $query->orderBy($orderBy, $orderDir);

            $results = $query->paginate(12);

            return response()->json($results);

        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
