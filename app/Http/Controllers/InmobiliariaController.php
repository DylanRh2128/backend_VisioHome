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

    public function uploadLogo(Request $request, $nitInmobiliaria)
    {
        $request->validate([
            'logo_light' => 'nullable|image|mimes:png,jpg,jpeg,svg|max:2048',
            'logo_dark' => 'nullable|image|mimes:png,jpg,jpeg,svg|max:2048',
        ]);

        $inmobiliaria = Inmobiliaria::findOrFail($nitInmobiliaria);

        if ($request->hasFile('logo_light')) {
            $path = $request->file('logo_light')->store('logos', 'public');
            $inmobiliaria->logo_light = $path;
        }

        if ($request->hasFile('logo_dark')) {
            $path = $request->file('logo_dark')->store('logos', 'public');
            $inmobiliaria->logo_dark = $path;
        }

        $inmobiliaria->save();

        return response()->json([
            'message' => 'Logos actualizados con éxito',
            'inmobiliaria' => $inmobiliaria
        ]);
    }
}
