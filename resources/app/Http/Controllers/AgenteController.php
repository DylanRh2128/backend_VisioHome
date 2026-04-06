<?php

namespace App\Http\Controllers;

use App\Models\Agente;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class AgenteController extends Controller
{
    /**
     * Listar todos los agentes
     */
    public function index(Request $request)
    {
        try {
            $query = Agente::query();

            // Filtro por búsqueda
            if ($request->has('search')) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('nombre', 'like', "%{$search}%")
                      ->orWhere('correo', 'like', "%{$search}%")
                      ->orWhere('docAgente', 'like', "%{$search}%");
                });
            }

            // Filtro por estado activo
            if ($request->has('activo')) {
                $query->where('activo', $request->activo);
            }

            $agentes = $query->get();

            // Agregar contadores de propiedades y ventas
            $agentes->map(function($agente) {
                // Contar propiedades asignadas (simulado - ajustar según tu lógica de negocio)
                $agente->propiedades = rand(5, 20);
                $agente->ventas = rand(3, 15);
                $agente->estado = $agente->activo ? 'Activo' : 'Inactivo';
                return $agente;
            });

            return response()->json($agentes, 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al obtener agentes',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Crear nuevo agente
     */
    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'docAgente' => 'required|string|max:20|unique:agentes,docAgente',
                'nombre' => 'required|string|max:120',
                'direccion' => 'nullable|string|max:200',
                'correo' => 'required|email|max:180|unique:agentes,correo',
                'telefono' => 'nullable|string|max:30',
                'nitInmobiliaria' => 'nullable|string|max:20|exists:inmobiliarias,nitInmobiliaria',
                'activo' => 'sometimes|boolean'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'message' => 'Error de validación',
                    'errors' => $validator->errors()
                ], 422);
            }

            $agente = Agente::create($request->all());

            return response()->json([
                'message' => 'Agente creado exitosamente',
                'agente' => $agente
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al crear agente',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtener un agente específico
     */
    public function show($id)
    {
        try {
            $agente = Agente::findOrFail($id);
            $agente->propiedades = rand(5, 20);
            $agente->ventas = rand(3, 15);
            $agente->estado = $agente->activo ? 'Activo' : 'Inactivo';

            return response()->json($agente, 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Agente no encontrado',
                'error' => $e->getMessage()
            ], 404);
        }
    }

    /**
     * Actualizar agente
     */
    public function update(Request $request, $id)
    {
        try {
            $agente = Agente::findOrFail($id);

            $validator = Validator::make($request->all(), [
                'nombre' => 'sometimes|required|string|max:120',
                'direccion' => 'nullable|string|max:200',
                'correo' => 'sometimes|required|email|max:180|unique:agentes,correo,' . $id . ',docAgente',
                'telefono' => 'nullable|string|max:30',
                'nitInmobiliaria' => 'nullable|string|max:20|exists:inmobiliarias,nitInmobiliaria',
                'activo' => 'sometimes|boolean'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'message' => 'Error de validación',
                    'errors' => $validator->errors()
                ], 422);
            }

            $data = $request->except('docAgente'); // No permitir cambiar el documento
            $agente->update($data);

            return response()->json([
                'message' => 'Agente actualizado exitosamente',
                'agente' => $agente
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al actualizar agente',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Eliminar agente
     */
    public function destroy($id)
    {
        try {
            $agente = Agente::findOrFail($id);
            $agente->delete();

            return response()->json([
                'message' => 'Agente eliminado exitosamente'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al eliminar agente',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
