<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pago;
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{
    public function success(Request $request)
    {
        $frontend = rtrim(config('app.url_frontend'), '/');


        // 🔥 Datos que vienen desde MercadoPago
        $status = $request->get('status');
        $externalReference = $request->get('external_reference');

        Log::info("MP SUCCESS RAW", [
            "status" => $status,
            "external_reference" => $externalReference,
            "all" => $request->all()
        ]);

        // ✅ Validación directa desde URL (confiable en entorno dev)
        if ($status === 'approved' && $externalReference) {

            $pago = Pago::find($externalReference);

            if ($pago) {
                // Usamos las constantes definidas en el modelo Pago
                if ($pago->estado !== Pago::ESTADO_APROBADO) {
                    $pago->estado = Pago::ESTADO_APROBADO;
                    $pago->save();

                    // 🔥 Actualizar también la cita
                    if ($pago->cita) {
                        $pago->cita->estado = 'confirmada';
                        $pago->cita->save();
                    }

                    Log::info("Pago actualizado correctamente", [
                        "idPago" => $pago->idPago
                    ]);
                }
            } else {
                Log::error("Pago NO encontrado", [
                    "external_reference" => $externalReference
                ]);
            }
        } else {
            Log::warning("Pago NO aprobado o datos incompletos", [
                "status" => $status,
                "external_reference" => $externalReference
            ]);
        }

        // Redirect back to frontend
        return redirect($frontend . "/pago-exitoso?" . http_build_query($request->all()));
    }

    public function failure(Request $request)
    {
        $frontend = rtrim(config('app.url_frontend'), '/');

        return redirect($frontend . "/pago-error");
    }

    public function pending(Request $request)
    {
        $frontend = rtrim(config('app.url_frontend'), '/');

        return redirect($frontend . "/pago-pendiente");
    }
}
