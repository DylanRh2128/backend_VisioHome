<?php

namespace App\Http\Controllers;

use App\Models\Inmobiliaria;
use Illuminate\Http\Request;

class InmobiliariaController extends Controller
{
    public function index()
    {
        return response()->json(Inmobiliaria::all());
    }

    public function store(Request $request)
    {
        $inmobiliaria = Inmobiliaria::create($request->all());
        return response()->json($inmobiliaria, 201);
    }

    public function show($nitInmobiliaria)
    {
        return response()->json(
            Inmobiliaria::findOrFail($nitInmobiliaria)
        );
    }

    public function update(Request $request, $nitInmobiliaria)
    {
        $inmobiliaria = Inmobiliaria::findOrFail($nitInmobiliaria);
        $inmobiliaria->update($request->all());

        return response()->json($inmobiliaria);
    }

    public function destroy($nitInmobiliaria)
    {
        Inmobiliaria::destroy($nitInmobiliaria);
        return response()->json(null, 204);
    }
}
