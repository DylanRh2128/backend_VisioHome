<?php

namespace App\Http\Controllers;

use App\Models\Disponibilidad;
use App\Models\Usuario;
use App\Models\Cita;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

use Carbon\Carbon;

class DisponibilidadController extends Controller
{

    // ─────────────────────────────────────────────
    // VER DISPONIBILIDAD DE UN AGENTE
    // GET /user/agentes/{docAgente}/disponibilidad
    // ─────────────────────────────────────────────

    public function show(Request $request, $docUsuario)
    {
        Log::info("Consultando disponibilidad agente", [
            'docAgente' => $docUsuario,
            'fecha' => $request->input('fecha')
        ]);

        $agente = Usuario::agentes()
            ->where('docUsuario', $docUsuario)
            ->first();

        if (!$agente) {

            Log::warning("Agente no encontrado", [
                'docAgente' => $docUsuario
            ]);

            return response()->json([
                'message' => 'Agente no encontrado'
            ], 404);
        }

        $disponibilidades = Disponibilidad::where('docAgente', $docUsuario)
            ->where('estado', 'disponible')
            ->orderBy('dia_semana')
            ->get();

        if ($request->filled('fecha')) {

            $fecha = $request->input('fecha');

            try {

                $carbonFecha = Carbon::parse($fecha);

            } catch (\Exception $e) {

                Log::error("Fecha inválida recibida", [
                    'fecha' => $fecha
                ]);

                return response()->json([
                    'message' => 'Fecha inválida'
                ], 422);
            }

            $diaSemanaBusqueda = $carbonFecha->dayOfWeek === 0 ? 7 : $carbonFecha->dayOfWeek;

            // Filtrar día
            $disponibilidades = $disponibilidades->filter(function ($d) use ($diaSemanaBusqueda) {
                return (int)$d->dia_semana === (int)$diaSemanaBusqueda;
            });

            // Buscar citas ocupadas
            $ocupadas = Cita::where('docAgente', $docUsuario)
                ->whereDate('fecha', $carbonFecha)
                ->where('estado', '!=', 'cancelada')
                ->get()
                ->map(function ($c) {

                    return Carbon::parse($c->fecha)->format('H:i');

                })
                ->toArray();

            Log::info("Horas ocupadas encontradas", $ocupadas);

            // Filtrar horas ocupadas
            $disponibilidades = $disponibilidades
                ->filter(function ($d) use ($ocupadas) {

                    $horaInicio = Carbon::parse($d->hora_inicio)->format('H:i');

                    return !in_array($horaInicio, $ocupadas);

                })
                ->values();
        }

        return response()->json([
            'docAgente' => $docUsuario,
            'nombre' => $agente->nombre,
            'disponibilidades' => $disponibilidades
        ]);
    }

    // ─────────────────────────────────────────────
    // LISTAR DISPONIBILIDAD DEL AGENTE
    // GET /agente/disponibilidades
    // ─────────────────────────────────────────────

    public function index(Request $request)
    {
        $agente = $request->user();

        if (!$agente) {

            Log::warning("Usuario no autenticado intentando ver disponibilidades");

            return response()->json([
                'message' => 'Usuario no autenticado'
            ], 401);
        }

        $diaNombres = [
            1 => 'Lunes',
            2 => 'Martes',
            3 => 'Miércoles',
            4 => 'Jueves',
            5 => 'Viernes',
            6 => 'Sábado',
            7 => 'Domingo',
        ];

        $disponibilidades = Disponibilidad::where('docAgente', $agente->docUsuario)
            ->orderBy('dia_semana')
            ->get()
            ->map(function ($d) use ($diaNombres) {

                $d->dia_nombre = $diaNombres[$d->dia_semana] ?? "Día {$d->dia_semana}";

                return $d;

            });

        return response()->json($disponibilidades);
    }

    // ─────────────────────────────────────────────
    // CREAR DISPONIBILIDAD
    // POST /agente/disponibilidades
    // ─────────────────────────────────────────────

    public function store(Request $request)
    {

        Log::info("Request crear disponibilidad", $request->all());

        $agente = $request->user();

        if (!$agente) {

            return response()->json([
                'message' => 'Usuario no autenticado'
            ], 401);
        }

        $validator = Validator::make($request->all(), [

            'dia_semana' => 'required|integer|between:1,7',

            'hora_inicio' => 'required|date_format:H:i',

            'hora_fin' => 'required|date_format:H:i|after:hora_inicio',

        ]);

        if ($validator->fails()) {

            Log::warning("Error validación disponibilidad", [
                'errors' => $validator->errors(),
                'request' => $request->all()
            ]);

            return response()->json([
                'message' => 'Error de validación',
                'errors' => $validator->errors()
            ], 422);
        }

        // ───────── Regla día duplicado ─────────

        $existe = Disponibilidad::where('docAgente', $agente->docUsuario)
            ->where('dia_semana', $request->dia_semana)
            ->exists();

        if ($existe) {

            Log::warning("Disponibilidad duplicada", [
                'docAgente' => $agente->docUsuario,
                'dia_semana' => $request->dia_semana
            ]);

            return response()->json([
                'message' => 'Ya tienes configurada disponibilidad para ese día.'
            ], 422);
        }

        // ───────── Máximo 5 días ─────────

        $totalDias = Disponibilidad::where('docAgente', $agente->docUsuario)->count();

        if ($totalDias >= 5) {

            Log::warning("Agente superó límite de días", [
                'docAgente' => $agente->docUsuario,
                'totalDias' => $totalDias
            ]);

            return response()->json([
                'message' => 'No puedes configurar más de 5 días.'
            ], 422);
        }

        // ───────── Máximo 8 horas ─────────

        $inicio = strtotime($request->hora_inicio);
        $fin = strtotime($request->hora_fin);

        $horas = ($fin - $inicio) / 3600;

        if ($horas > 8) {

            Log::warning("Bloque mayor a 8 horas", [
                'horas' => $horas
            ]);

            return response()->json([
                'message' => 'El bloque no puede superar 8 horas.'
            ], 422);
        }

        $disponibilidad = Disponibilidad::create([

            'docAgente' => $agente->docUsuario,
            'dia_semana' => $request->dia_semana,
            'hora_inicio' => $request->hora_inicio,
            'hora_fin' => $request->hora_fin,
            'estado' => 'disponible',
        ]);

        Log::info("Disponibilidad creada", [
            'id' => $disponibilidad->idDisponibilidad
        ]);

        return response()->json([
            'message' => 'Disponibilidad configurada correctamente',
            'disponibilidad' => $disponibilidad
        ], 201);
    }

    // ─────────────────────────────────────────────
    // ELIMINAR DISPONIBILIDAD
    // DELETE /agente/disponibilidades/{id}
    // ─────────────────────────────────────────────

    public function destroy(Request $request, $id)
    {
        $agente = $request->user();

        $disponibilidad = Disponibilidad::where('idDisponibilidad', $id)
            ->where('docAgente', $agente->docUsuario)
            ->first();

        if (!$disponibilidad) {

            Log::warning("Intento eliminar disponibilidad inexistente", [
                'id' => $id,
                'agente' => $agente->docUsuario
            ]);

            return response()->json([
                'message' => 'Disponibilidad no encontrada'
            ], 404);
        }

        $disponibilidad->delete();

        Log::info("Disponibilidad eliminada", [
            'id' => $id
        ]);

        return response()->json([
            'message' => 'Disponibilidad eliminada correctamente'
        ]);
    }
}