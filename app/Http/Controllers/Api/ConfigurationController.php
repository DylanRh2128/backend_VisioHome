<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Configuration;
use Illuminate\Http\Request;

class ConfigurationController extends Controller
{
    /**
     * Listar todas las configuraciones.
     */
    public function index()
    {
        return response()->json(Configuration::all());
    }

    public function getStatus()
    {
        $inmobiliaria = \App\Models\Inmobiliaria::first();

        // Helper para asegurar que la ruta al storage sea correcta y absoluta
        $getLogoUrl = function($path) {
            if (!$path) return null;
            // Si el path ya es una URL completa, devolverla
            if (filter_var($path, FILTER_VALIDATE_URL)) return $path;
            // Asegurar que comience con storage/ si no lo tiene
            $cleanPath = str_replace(['public/', 'storage/'], '', $path);
            return asset('storage/' . $cleanPath);
        };

        return response()->json([
            'payments_enabled' => (bool) env('APP_ENABLE_PAYMENTS', true),
            'precios_citas' => [
                'principal' => Configuration::getValue('precio_cita_principal', 80000),
                'secundaria' => Configuration::getValue('precio_cita_secundaria', 50000),
                'terciaria' => Configuration::getValue('precio_cita_terciaria', 30000),
            ],
            'fallback' => Configuration::getValue('cita_precio_base', 50000),
            'branding' => [
                'nombre' => $inmobiliaria ? $inmobiliaria->nombre : 'VisioHome',
                'logo_dark' => $getLogoUrl($inmobiliaria?->logo_dark),
                'logo_light' => $getLogoUrl($inmobiliaria?->logo_light),
            ]
        ]);
    }

    /**
     * Actualizar una configuración específica.
     */
    public function update(Request $request, $key)
    {
        $request->validate([
            'value' => 'required'
        ]);

        $config = Configuration::updateOrCreate(
            ['key' => $key],
            ['value' => $request->value]
        );

        return response()->json([
            'message' => 'Configuración actualizada',
            'config' => $config
        ]);
    }
}
