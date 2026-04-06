<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Cita;
use App\Models\Pago;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class WebhookController extends Controller
{
    /**
     * Webhook MercadoPago
     */
    public function handleMercadoPago(Request $request)
    {
        Log::info('📩 Webhook RAW:', $request->all());

        $type = $request->input('type') ?: $request->input('topic');

        if ($type !== 'payment') {
            return response()->json(['status' => 'ignored'], 200);
        }

        $paymentId = $request->input('data.id') ?: $request->input('id');

        if (!$paymentId) {
            return response()->json(['error' => 'No payment id found'], 400);
        }

        try {
            $token = config('services.mercadopago.access_token');
            \MercadoPago\MercadoPagoConfig::setAccessToken($token);

            $client = new \MercadoPago\Client\Payment\PaymentClient();
            $payment = $client->get($paymentId);

            if (!$payment) {
                Log::error("❌ Pago {$paymentId} no encontrado en MP");
                return response()->json(['error' => 'Payment not found'], 404);
            }

            $idPago = $payment->external_reference;

            if (!$idPago) {
                Log::warning("⚠️ Pago {$paymentId} sin external_reference (idPago)");
                return response()->json(['status' => 'no_reference'], 200);
            }

            // 🔍 Buscar el PAGO existente por idPago
            $pago = Pago::find($idPago);

            if (!$pago) {
                Log::error("❌ No se encontró registro de Pago en DB para idPago: {$idPago}");
                return response()->json(['error' => 'Local payment record not found'], 404);
            }

            // 🔒 Idempotencia: Si ya está marcado como aprobado con esta misma referencia, ignorar
            if ($pago->estado === 'aprobado' && $pago->referencia == $payment->id) {
                return response()->json(['status' => 'already_processed'], 200);
            }

            // 4. Procesar según estado de MP
            Log::info("📊 Procesando estado MP: {$payment->status} para Pago: {$idPago}");

            switch ($payment->status) {
                case 'approved':
                    return $this->procesarPagoAprobado($pago, $payment);
                
                case 'rejected':
                case 'cancelled':
                case 'refunded':
                    return $this->procesarPagoFallido($pago, $payment);

                case 'in_process':
                case 'pending':
                    $pago->update(['estado' => 'pendiente', 'referencia' => (string)$payment->id]);
                    return response()->json(['status' => 'pending_sync'], 200);

                default:
                    Log::info("ℹ️ Estado MP no manejado específicamente: {$payment->status}");
                    return response()->json(['status' => 'ignored_status'], 200);
            }

        } catch (\Exception $e) {
            Log::error("🔥 ERROR Webhook MP: " . $e->getMessage());
            return response()->json(['error' => 'Internal error'], 500);
        }
    }

    /**
     * Procesar Pago Exitoso
     */
    private function procesarPagoAprobado($pago, $payment)
    {
        return DB::transaction(function () use ($pago, $payment) {
            $cita = $pago->cita; // Relación ya definida en el modelo Pago

            if (!$cita) {
                Log::error("❌ Cita {$pago->idCita} no encontrada para el pago {$pago->idPago}");
                return response()->json(['error' => 'Cita relation not found'], 404);
            }

            // Si ya está confirmada, solo actualizamos el registro de pago por si acaso
            if ($cita->estado === 'confirmada') {
                $pago->update(['estado' => 'aprobado', 'referencia' => (string)$payment->id]);
                return response()->json(['status' => 'already_confirmed_sync'], 200);
            }

            // ✅ Actualizar Pago
            $pago->update([
                'estado' => 'aprobado',
                'referencia' => (string)$payment->id,
                'metodoPago' => $payment->payment_type_id ?? 'MercadoPago'
            ]);

            // ✅ Confirmar Cita
            $cita->update(['estado' => 'confirmada']);

            Log::info("✅ Cita {$cita->idCita} confirmada vía Webhook.");

            return response()->json(['status' => 'success'], 200);
        });
    }

    /**
     * Procesar Pago Fallido
     */
    private function procesarPagoFallido($pago, $payment)
    {
        $pago->update([
            'estado' => 'rechazado',
            'referencia' => (string)$payment->id
        ]);

        // Opcional: Podrías cancelar la cita aquí si quieres liberar el bloque inmediatamente,
        // pero a veces es mejor dejarla 'pendiente' para que el usuario intente pagar de nuevo.
        // En este caso, la dejamos pendiente según el flujo de la app.
        
        Log::warning("❌ Pago rechazado/fallido para Cita: {$pago->idCita}. Estado MP: {$payment->status}");

        return response()->json(['status' => 'payment_failed_recorded'], 200);
    }
}