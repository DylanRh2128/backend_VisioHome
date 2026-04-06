<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Cita;
use App\Models\Disponibilidad;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

use App\Models\Configuration;
use App\Models\Propiedad;
use App\Models\Pago;
use App\Services\MercadoPagoService;
use Illuminate\Support\Str;

class CitaController extends Controller
{
    /* ======================================================
     *  USUARIO - LISTAR MIS CITAS
     * ====================================================== */
    public function index(Request $request)
    {
        $user = $request->user();

        $query = Cita::where('docUsuario', $user->docUsuario)
            ->with(['propiedad', 'agente', 'pago']);

        // 📌 Filtro por estado
        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }

        // 📆 Filtro por fecha específica
        if ($request->filled('fecha')) {
            $query->whereDate('fecha', $request->fecha);
        }

        // 🔍 Filtro por propiedad (título)
        if ($request->filled('propiedad')) {
            $query->whereHas('propiedad', function ($q) use ($request) {
                $q->where('titulo', 'like', "%{$request->propiedad}%");
            });
        }

        $citas = $query->orderBy('fecha', 'desc')->get();

        // 🕒 Lógica dinámica de estado 'finalizada'
        $now = now();
        $citas->transform(function ($cita) use ($now) {
            if ($cita->fecha < $now && !in_array($cita->estado, ['cancelada', 'finalizada'])) {
                $cita->estado = 'finalizada';
            }
            return $cita;
        });

        return response()->json($citas);
    }

    /**
     * USUARIO - CREAR CITA (RESERVA)
     */
    public function store(Request $request)
    {
        \Log::info("CitaController@store payload recibido", $request->all());

        $validator = Validator::make($request->all(), [
            'idPropiedad'      => 'required|exists:propiedades,idPropiedad',
            'docAgente'        => 'required|exists:usuarios,docUsuario',
            'idDisponibilidad' => 'required|exists:disponibilidades,idDisponibilidad',
            'fecha'            => 'required|date_format:Y-m-d|after_or_equal:today',
            'notas'            => 'nullable|string'
        ], [
            'fecha.date_format' => 'La fecha debe tener el formato YYYY-MM-DD (ej: 2026-03-05)',
            'fecha.after_or_equal' => 'No puedes agendar en una fecha pasada',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Error de validación',
                'errors'  => $validator->errors()
            ], 422);
        }

        $user = $request->user();

        // 1. Verificar disponibilidad exacta
        $disponibilidad = Disponibilidad::where('idDisponibilidad', $request->idDisponibilidad)
            ->where('docAgente', $request->docAgente)
            ->first();

        if (!$disponibilidad) {
            return response()->json(['message' => 'Disponibilidad no válida'], 422);
        }

        if ($disponibilidad->estado !== 'disponible') {
            return response()->json(['message' => 'El horario solicitado ya no está disponible'], 400);
        }

        // 2. Extraer solo la FECHA del request para evitar confusiones de formato
        $fechaSolo = Carbon::parse($request->fecha)->format('Y-m-d');
        $carbonFecha = Carbon::parse($fechaSolo);

        // 3. Validar que el día de la semana de la fecha enviada sea el mismo que el del bloque
        // Carbon: 0=Dom, 1=Lun. Nuestro estándar: 1=Lun, 7=Dom.
        $diaSemanaRequest = $carbonFecha->dayOfWeek === 0 ? 7 : $carbonFecha->dayOfWeek;

        if ((int)$diaSemanaRequest !== (int)$disponibilidad->dia_semana) {
            return response()->json([
                'message' => "La fecha seleccionada no corresponde al día de disponibilidad configurado para este bloque."
            ], 422);
        }

        // 4. Construir fecha completa (Datetime) con la hora de la disponibilidad
        $fechaHoraSeleccionada = Carbon::parse($fechaSolo . ' ' . $disponibilidad->hora_inicio);

        if ($fechaHoraSeleccionada->isPast()) {
            return response()->json(['message' => 'No puedes agendar en el pasado'], 422);
        }

        return DB::transaction(function () use ($request, $user, $fechaHoraSeleccionada, $fechaSolo, $disponibilidad) {
            
            // 🔒 Bloquear para evitar doble reserva concurrente
            $existe = Cita::where('idDisponibilidad', $request->idDisponibilidad)
                ->whereDate('fecha', $fechaSolo)
                ->where('estado', '!=', 'cancelada')
                ->lockForUpdate()
                ->exists();

            if ($existe) {
                return response()->json(['message' => 'El horario ya no está disponible para esta propiedad'], 422);
            }

            // 🔒 El agente no puede tener otra cita en el mismo bloque EXACTO (mismo datetime)
            $agenteOcupado = Cita::where('docAgente', $request->docAgente)
                ->where('fecha', $fechaHoraSeleccionada)
                ->where('estado', '!=', 'cancelada')
                ->lockForUpdate()
                ->exists();

            if ($agenteOcupado) {
                return response()->json(['message' => 'El agente ya tiene una cita programada en este horario'], 422);
            }

            // 2. Obtener Propiedad para determinar el precio por categoría
            $propiedad = Propiedad::findOrFail($request->idPropiedad);

            if ($propiedad->estado !== 'disponible') {
                return response()->json(['message' => 'La propiedad no está disponible'], 400);
            }
            
            // Determinar tipo de ciudad (priorizar campo manual, luego clasificación automática)
            $tipoCiudad = $propiedad->categoria_ciudad ?? Propiedad::clasificarCiudad($propiedad->ciudad);
            
            // Estandarizado a precio_cita_... según requerimiento
            $keyConfig = "precio_cita_" . strtolower($tipoCiudad);

            // 3. Buscar precio en configurations con fallback a cita_precio_base y luego a 50000
            $precio = Configuration::getValue($keyConfig) 
                      ?? Configuration::getValue('cita_precio_base', 50000);

            // 4. Crear Cita (Pendiente de pago)
            $cita = Cita::create([
                'idPropiedad'      => $request->idPropiedad,
                'docUsuario'       => $user->docUsuario,
                'docAgente'        => $request->docAgente,
                'idDisponibilidad' => $request->idDisponibilidad,
                'fecha'            => $fechaHoraSeleccionada,
                'estado'           => 'pendiente', 
                'canal'            => 'presencial',
                'precio'           => (float) $precio,
                'notas'            => $request->notas
            ]);

            // Bloquear horario para que nadie más lo use
            $disponibilidad->update(['estado' => 'reservada']);

            // 5. Crear Registro de Pago (Pendiente)
            $pago = Pago::create([
                'docUsuario'  => $user->docUsuario,
                'idPropiedad' => $request->idPropiedad,
                'idCita'      => $cita->idCita,
                'monto'       => (float) $precio,
                'metodoPago'  => 'mercadopago',
                'estado'      => 'pendiente',
                'external_reference' => Str::uuid()->toString(),
                'fecha'       => now()
            ]);

            // 6. Generar Preferencia de MercadoPago si los pagos están habilitados
            $initPoint = null;
            $sandboxInitPoint = null;
            if (config('app.enable_payments', false)) {
                try {
                    $mpService = new MercadoPagoService();
                    $preference = $mpService->createPreference($pago);
                    
                    $pago->update(['mp_preference_id' => $preference->id]);
                    $initPoint = $preference->init_point;
                    $sandboxInitPoint = $preference->sandbox_init_point;

                    \Log::info("Preferencia creada exitosamente (store)", [
                        "id" => $preference->id,
                        "init_point" => $initPoint
                    ]);
                } catch (\Exception $e) {
                    \Log::error("Error creando preferencia MP: " . $e->getMessage());
                }
            }

            return response()->json([
                'message'   => 'Cita agendada. Pendiente de pago.',
                'cita'      => $cita,
                'pago'      => $pago,
                'init_point' => $initPoint,
                'sandbox_init_point' => $sandboxInitPoint
            ], 201);
        });
    }

    /**
     * Obtener el link de pago para una cita que está pendiente de pago.
     */
   public function getPaymentLink($id)
    {
        $user = auth()->user();

        $cita = Cita::where('idCita', $id)
            ->where('docUsuario', $user->docUsuario)
            ->firstOrFail();

        if ($cita->estado !== 'pendiente') {
            return response()->json([
                'message' => 'La cita ya no está pendiente de pago.'
            ], 400);
        }

        $pago = Pago::where('idCita', $cita->idCita)->first();

        if (!$pago) {
            return response()->json([
                'message' => 'No se encontró registro de pago.'
            ], 404);
        }

        if (!config('app.enable_payments', false)) {
            return response()->json([
                'message' => 'El sistema de pagos está desactivado.'
            ], 403);
        }

        try {

            \Log::info("Generando preferencia MercadoPago", [
                "cita" => $cita->idCita,
                "pago" => $pago->idPago,
                "monto" => $pago->monto
            ]);

            $mpService = new MercadoPagoService();
            $preference = $mpService->createPreference($pago);

            \Log::info("Preferencia creada exitosamente", [
                "id" => $preference->id,
                "init_point" => $preference->init_point,
                "sandbox_init_point" => $preference->sandbox_init_point
            ]);

            $pago->update([
                'mp_preference_id' => $preference->id
            ]);

            return response()->json([
                "init_point" => $preference->init_point,
                "sandbox_init_point" => $preference->sandbox_init_point
            ]);

        } catch (\Exception $e) {

            \Log::error("Error en getPaymentLink", [
                "message" => $e->getMessage(),
                "trace" => $e->getTraceAsString()
            ]);

            return response()->json([
                "message" => "Error generando link de pago",
                "error" => $e->getMessage()
            ], 500);
        }
    }
    /* ======================================================
     *  USUARIO - CANCELAR CITA
     * ====================================================== */
    public function cancel($id)
    {
        $user = auth()->user();

        $cita = Cita::where('idCita', $id)
            ->where('docUsuario', $user->docUsuario)
            ->first();

        if (!$cita) {
            return response()->json(['message' => 'Cita no encontrada'], 404);
        }

        if (!in_array($cita->estado, ['pendiente', 'confirmada', 'pago'])) {
            return response()->json([
                'message' => 'No se puede cancelar una cita en estado: ' . $cita->estado
            ], 400);
        }

        if ($cita->estado === 'pago') {
            $cita->update(['estado' => 'reagendar']);
            return response()->json([
                'message' => 'Cita pagada movida a reagendar. Debes seleccionar una nueva fecha.',
                'status' => 'reagendar'
            ]);
        }

        $cita->update(['estado' => 'cancelada']);

        return response()->json([
            'message' => 'Cita cancelada correctamente',
            'status' => 'cancelada'
        ]);
    }

    public function reschedule(Request $request, $id)
    {
        $request->validate([
            'fecha' => 'required|date|after_or_equal:today',
            'hora' => 'required|date_format:H:i'
        ]);

        $user = auth()->user();

        $cita = Cita::where('idCita', $id)
            ->where('docUsuario', $user->docUsuario)
            ->first();

        if (!$cita) {
            return response()->json(['message' => 'Cita no encontrada'], 404);
        }

        if ($cita->estado !== 'reagendar') {
            return response()->json([
                'message' => 'Solo puedes reagendar citas en estado "reagendar". Estado actual: ' . $cita->estado
            ], 400);
        }

        $cita->update([
            'fecha' => $request->fecha,
            'hora' => $request->hora,
            'estado' => 'pago', // Se devuelve a 'pago' y se confía en el agente para confirmar
            'notas' => 'Rescheduled: ' . ($cita->notas ?: '')
        ]);

        return response()->json([
            'message' => 'Cita reagendada correctamente',
            'cita' => $cita
        ]);
    }

    /* ======================================================
     *  AGENTE - LISTAR MIS CITAS
     * ====================================================== */
    public function agenteIndex(Request $request)
    {
        $agente = $request->user();

        $query = Cita::where('docAgente', $agente->docUsuario)
            ->with(['propiedad', 'usuario', 'pago']);

        // 📌 Filtro por estado
        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }

        // 📆 Filtro por fecha específica
        if ($request->filled('fecha')) {
            $query->whereDate('fecha', $request->fecha);
        }

        $citas = $query->orderBy('fecha', 'desc')->get();

        // 🕒 Lógica dinámica de estado 'finalizada'
        $now = now();
        $citas->transform(function ($cita) use ($now) {
            if ($cita->fecha < $now && !in_array($cita->estado, ['cancelada', 'finalizada', 'reagendar'])) {
                $cita->estado = 'finalizada';
            }
            return $cita;
        });

        return response()->json($citas);
    }

    /* ======================================================
     *  AGENTE - CONFIRMAR CITA
     * ====================================================== */
    public function confirmarCita($id)
    {
        $agente = auth()->user();

        $cita = Cita::where('idCita', $id)
            ->where('docAgente', $agente->docUsuario)
            ->first();

        if (!$cita) {
            return response()->json(['message' => 'Cita no encontrada'], 404);
        }

        if ($cita->estado !== 'pendiente') {
            return response()->json([
                'message' => 'Solo se pueden confirmar citas pendientes'
            ], 400);
        }

        $cita->update(['estado' => 'confirmada']);

        return response()->json([
            'message' => 'Cita confirmada exitosamente',
            'cita'    => $cita
        ]);
    }

    /* ======================================================
     *  AGENTE - CANCELAR CITA
     * ====================================================== */
    public function cancelarPorAgente($id)
    {
        $agente = auth()->user();

        $cita = Cita::where('idCita', $id)
            ->where('docAgente', $agente->docUsuario)
            ->first();

        if (!$cita) {
            return response()->json(['message' => 'Cita no encontrada'], 404);
        }

        if ($cita->estado === 'cancelada') {
            return response()->json([
                'message' => 'La cita ya está cancelada'
            ], 400);
        }

        if ($cita->estado === 'pago') {
            $cita->update(['estado' => 'reagendar']);
            return response()->json([
                'message' => 'La cita estaba pagada, se movió a reagendar para el cliente',
                'status' => 'reagendar',
                'cita' => $cita
            ]);
        }

        $cita->update(['estado' => 'cancelada']);

        return response()->json([
            'message' => 'Cita cancelada por el agente',
            'status' => 'cancelada',
            'cita'    => $cita
        ]);
    }
}