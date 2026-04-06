<?php

namespace App\Http\Controllers;

use App\Models\Propiedad;
use Illuminate\Http\Request;
use ZipArchive;
use Illuminate\Support\Facades\Storage;

class PropiedadController extends Controller
{
    // GET /api/propiedades
    public function index()
    {
        return response()->json(
            Propiedad::where('estado', 'disponible')
                ->orderBy('creado_en', 'desc')
                ->get()
        );
    }

    // POST /api/propiedades
    public function store(Request $request)
    {
        $rules = [
            'titulo' => 'required|string|max:200',
            'descripcion' => 'nullable|string',
            'ubicacion' => 'required|string|max:255',
            'ciudad' => 'nullable|string|max:255',
            'tamano_m2' => 'nullable|numeric',
            'precio' => 'required|numeric',
            'estado' => 'required|string',
            'tipo' => 'required|string',
            'nitInmobiliaria' => 'required|string',
        ];

        // Validar imagen: archivo o string (URL externa)
        if ($request->hasFile('imagen')) {
            $rules['imagen'] = 'image|mimes:jpg,jpeg,png,webp|max:2048';
        } else {
            $rules['imagen'] = 'nullable|string|max:500';
        }

        $data = $request->validate($rules);

        // Procesar subida de imagen si existe
        if ($request->hasFile('imagen')) {
            $path = $request->file('imagen')->store('propiedades', 'public');
            $data['imagen'] = $path;
        }

        $data['creado_en'] = now();

        $propiedad = Propiedad::create($data);

        return response()->json($propiedad, 201);
    }

    // GET /api/propiedades/{id}
    public function show($id)
    {
        try {
            $propiedad = Propiedad::findOrFail($id);

            return response()->json([
                'propiedad' => $propiedad,
                'agente' => null // 🔥 temporalmente desactivado
            ]);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'error'   => 'Propiedad no encontrada',
                'message' => "No existe una propiedad con id={$id}",
            ], 404);

        } catch (\Exception $e) {
            \Log::error('Error en PropiedadController::show', [
                'id'      => $id,
                'message' => $e->getMessage(),
            ]);

            return response()->json([
                'error'   => 'Error interno del servidor',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    // GET /api/propiedades/{id}/modelo3d
    public function getModelo3D($id)
    {
        $propiedad = Propiedad::where('idPropiedad', $id)->firstOrFail();

        \Illuminate\Support\Facades\Log::info("DEBUG MODELO 3D", [
            'id_recibido' => $id,
            'id_propiedad' => $propiedad->idPropiedad,
            'path_bd' => $propiedad->modelo_3d_path
        ]);

        if (empty($propiedad->modelo_3d_path)) {
            return response()->json([
                "model_url" => null,
                "message" => "Modelo 3D no disponible"
            ], 200);
        }

        // El accesor en Propiedad.php ya nos devuelve la URL absoluta lista (http...)
        $url = $propiedad->modelo_3d_path;

        \Log::info("URL GENERADA", [
            'url' => $url
        ]);

        return response()->json([
            "model_url" => $url
        ]);
    }

    /**
     * Upload 3D model (.glb, .gltf)
     */
    public function upload3D(\App\Http\Requests\Upload3DRequest $request, $id)
    {
        $propiedad = Propiedad::findOrFail($id);

        if ($request->hasFile('modelo_3d')) {
            $file = $request->file('modelo_3d');
            $mime = $file->getMimeType();
            $extension = strtolower($file->getClientOriginalExtension());
            
            // Logging para debug solicitado por el usuario
            \Illuminate\Support\Facades\Log::info('Nombre original: ' . $file->getClientOriginalName());
            \Illuminate\Support\Facades\Log::info('MIME detectado: ' . $mime);
            \Illuminate\Support\Facades\Log::info('Extensión original: ' . $extension);
            
            \Illuminate\Support\Facades\Log::info("Subiendo modelo 3D - ID Propiedad: $id", [
                'mime' => $mime,
                'extension' => $extension,
                'size' => $file->getSize()
            ]);

            // Validación manual de extensión
            $allowedExtensions = ['glb', 'gltf'];
            if (!in_array($extension, $allowedExtensions)) {
                return response()->json([
                    'success' => false, 
                    'message' => 'Solo se admiten modelos en formato .glb o .gltf.'
                ], 422);
            }

            // Determinar directorio por ID de propiedad para organizar mejor los archivos .bin/texturas
            $directory = "propiedades/modelos/propiedad_{$id}";
            $filename = 'model_' . time() . '.' . $extension; // Mantener nombre original (ej: scene.gltf o casa.glb)
            
            // Store the file using the requested method - ensuring correct extension and path
            $path = $file->storeAs($directory, $filename, 'public');

            \Illuminate\Support\Facades\Log::info('Path final guardado en disco: ' . $path);

            // Update database with the FULL relative path including filename
            $propiedad->update(['modelo_3d_path' => $path]);

            return response()->json([
                'success' => true,
                'message' => 'Modelo 3D subido correctamente.',
                'path' => $path,
                'full_url' => asset('storage/' . $path)
            ]);
        }

        return response()->json(['success' => false, 'message' => 'No se subió ningún archivo.'], 400);
    }

    /**
     * Upload 3D model ZIP (.zip containing .gltf, .bin, textures) - Robust and Recursive detection
     */
    public function upload3DZip(Request $request, $id)
    {
        try {
            \Log::info("🚀 INICIO upload3DZip", ['id' => $id]);

            // 0. Validar ZipArchive
            if (!class_exists('ZipArchive')) {
                throw new \Exception("ZipArchive no está habilitado en PHP");
            }

            // 1. Validar request
            $request->validate([
                'file' => 'required|file|mimes:zip|max:51200'
            ]);

            \Log::info("✅ Validación de archivo correcta");

            $propiedad = Propiedad::where('idPropiedad', $id)->firstOrFail();
            $zipFile = $request->file('file');

            $directory = "propiedades/modelos/propiedad_{$id}";
            $storagePath = storage_path("app/public/{$directory}");

            \Log::info("📂 Rutas generadas", [
                'directory' => $directory,
                'storagePath' => $storagePath
            ]);

            // 2. Crear carpeta si no existe
            if (!file_exists($storagePath)) {
                \Log::info("📁 Carpeta no existe, intentando crearla...");
                if (!mkdir($storagePath, 0777, true) && !is_dir($storagePath)) {
                    throw new \Exception("No se pudo crear el directorio");
                }
            }

            \Log::info("✅ Carpeta lista");

            // 3. Guardar ZIP
            $zipPath = $zipFile->storeAs($directory, 'model_upload.zip', 'public');
            $zipFullPath = storage_path("app/public/{$zipPath}");

            \Log::info("📦 ZIP guardado", [
                'zipPath' => $zipPath,
                'zipFullPath' => $zipFullPath
            ]);

            // 4. Abrir ZIP
            $zip = new \ZipArchive;
            $res = $zip->open($zipFullPath);

            if ($res !== TRUE) {
                throw new \Exception("Error al abrir ZIP, código: " . $res);
            }

            \Log::info("✅ ZIP abierto correctamente");

            // 5. Extraer ZIP
            \Log::info("⏳ Intentando extraer ZIP...");
            if (!$zip->extractTo($storagePath)) {
                throw new \Exception("Error al extraer el ZIP");
            }

            $zip->close();
            \Log::info("📂 ZIP extraído con éxito");

            // 6. Eliminar ZIP (seguro)
            try {
                Storage::disk('public')->delete($zipPath);
                \Log::info("🗑️ ZIP eliminado");
            } catch (\Exception $e) {
                \Log::warning("⚠️ No se pudo borrar ZIP", [
                    'error' => $e->getMessage()
                ]);
            }

            // 7. Leer archivos recursivamente
            \Log::info("🔍 Escaneando archivos...");
            $allFiles = [];

            $iterator = new \RecursiveIteratorIterator(
                new \RecursiveDirectoryIterator($storagePath, \FilesystemIterator::SKIP_DOTS)
            );

            foreach ($iterator as $file) {
                if ($file->isFile()) {
                    $allFiles[] = $file->getPathname();
                }
            }

            \Log::info("📁 Archivos detectados", [
                'total' => count($allFiles)
            ]);

            if (count($allFiles) === 0) {
                throw new \Exception("El ZIP está vacío o no contiene archivos válidos");
            }

            // 8. Detectar modelo
            $modelFile = null;

            foreach ($allFiles as $file) {
                if (str_ends_with(strtolower($file), '.glb')) {
                    $modelFile = $file;
                    break;
                }
            }

            if (!$modelFile) {
                foreach ($allFiles as $file) {
                    if (str_ends_with(strtolower($file), '.gltf')) {
                        $modelFile = $file;
                        break;
                    }
                }
            }

            if (!$modelFile) {
                throw new \Exception("No se encontró modelo 3D (.glb o .gltf) en el ZIP");
            }

            \Log::info("🎯 Modelo detectado", [
                'file' => $modelFile
            ]);

            // 9. Normalizar ruta correctamente
            $storageBase = str_replace('\\', '/', storage_path('app/public')) . '/';
            $normalizedModel = str_replace('\\', '/', $modelFile);

            if (!str_starts_with($normalizedModel, $storageBase)) {
                throw new \Exception("Error al calcular la ruta relativa del modelo");
            }

            $relativePath = str_replace($storageBase, '', $normalizedModel);

            \Log::info("📍 Ruta relativa calculada", [
                'relativePath' => $relativePath
            ]);

            // 10. Guardar en BD
            $propiedad->update([
                'modelo_3d_path' => $relativePath
            ]);

            \Log::info("💾 Modelo guardado en BD");

            return response()->json([
                'success' => true,
                'message' => 'Modelo 3D cargado correctamente',
                'model_url' => asset('storage/' . $relativePath)
            ]);

        } catch (\Exception $e) {

            \Log::error("❌ ERROR upload3DZip", [
                'message' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // PUT /api/propiedades/{id}
    public function update(Request $request, $id)
    {
        $propiedad = Propiedad::findOrFail($id);

        $rules = [
            'titulo' => 'sometimes|string|max:200',
            'descripcion' => 'nullable|string',
            'ubicacion' => 'sometimes|string|max:255',
            'ciudad' => 'nullable|string|max:255',
            'tamano_m2' => 'nullable|numeric',
            'precio' => 'sometimes|numeric',
            'estado' => 'sometimes|string',
            'tipo' => 'sometimes|string',
            'nitInmobiliaria' => 'sometimes|string',
        ];

        // Validar imagen: archivo o string (URL externa)
        if ($request->hasFile('imagen')) {
            $rules['imagen'] = 'image|mimes:jpg,jpeg,png,webp|max:2048';
        } else {
            $rules['imagen'] = 'nullable|string|max:500';
        }

        $data = $request->validate($rules);

        // Procesar subida de imagen nueva si existe
        if ($request->hasFile('imagen')) {
            // Opcionalmente: borrar imagen vieja si no era una URL externa
            $path = $request->file('imagen')->store('propiedades', 'public');
            $data['imagen'] = $path;
        }

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

