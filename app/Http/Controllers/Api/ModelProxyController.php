<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\Response;

class ModelProxyController extends Controller
{
    /**
     * Serves 3D model files with correct CORS headers and Content-Type.
     * 
     * @param string $path
     * @return \Illuminate\Http\Response
     */
    public function serve($path)
    {
        // Security check: ensure the path is within the allowed directory
        // We only allow files inside 'public/propiedades/modelos/'
        if (!str_contains($path, 'propiedades/modelos/')) {
            return response()->json(['error' => 'Acceso no permitido'], 403);
        }

        // The path coming from the route might have leading slashes
        $cleanPath = ltrim($path, '/');

        if (!Storage::disk('public')->exists($cleanPath)) {
            return response()->json(['error' => 'Archivo no encontrado', 'path' => $cleanPath], 404);
        }

        $file = Storage::disk('public')->get($cleanPath);
        $mimeType = Storage::disk('public')->mimeType($cleanPath);

        // Map GLTF specific mime types if not detected correctly
        if (str_ends_with($cleanPath, '.gltf')) {
            $mimeType = 'model/gltf+json';
        } elseif (str_ends_with($cleanPath, '.bin')) {
            $mimeType = 'application/octet-stream';
        }

        return response($file, 200)
            ->header('Content-Type', $mimeType)
            ->header('Access-Control-Allow-Origin', '*')
            ->header('Access-Control-Allow-Methods', 'GET, OPTIONS')
            ->header('Access-Control-Allow-Headers', 'Origin, Content-Type, Accept, Authorization, X-Requested-With')
            ->header('Cache-Control', 'public, max-age=3600');
    }
}
