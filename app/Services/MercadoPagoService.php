<?php

namespace App\Services;

use MercadoPago\MercadoPagoConfig;
use MercadoPago\Client\Preference\PreferenceClient;
use App\Models\Pago;

class MercadoPagoService
{
    public function __construct()
    {
        $token = config('services.mercadopago.access_token');
        \Log::debug("Iniciando MercadoPagoService con token: " . substr($token, 0, 10) . "...");
        
        if (empty($token)) {
            \Log::error("MERCADOPAGO_ACCESS_TOKEN no encontrado en configuración.");
        }

        MercadoPagoConfig::setAccessToken($token);
    }

    public function createPreference(Pago $pago)
    {
        // 1. Validaciones obligatorias
        if (!$pago->monto || $pago->monto <= 0) {
            throw new \Exception("Precio inválido para pago: " . $pago->monto);
        }

        // 2. Cargar propiedad para el título si no está cargada
        if (!$pago->relationLoaded('propiedad')) {
            $pago->load('propiedad');
        }
        $titulo = $pago->propiedad ? "Reserva: " . $pago->propiedad->titulo : "Reserva de Cita - VisioHome";

        // Usar env() directamente porque config('app.url_frontend') puede retornar null si no está en config/app.php
        $frontend = env('APP_URL_FRONTEND', 'http://localhost:5173');
        $backend = env('APP_URL', 'http://localhost:8000');
        
        // Limpiar posibles slashes duplicados
        $frontend = rtrim($frontend, '/');
        $backend = rtrim($backend, '/');

        \Log::info("Generando preferencia MercadoPago", [
            "cita" => $pago->idCita,
            "pago" => $pago->idPago,
            "monto" => $pago->monto,
            "frontend_url" => $frontend,
            "notification_url" => "{$backend}/api/webhooks/mercadopago"
        ]);

        // Forzar el uso de NGROK_URL si existe, ya que MercadoPago bloquea auto_return hacia localhost
        $backend = rtrim(env('NGROK_URL', env('APP_URL', 'http://localhost:8000')), '/');

        $payload = [
            "items" => [
                [
                    "title" => "Cita VisioHome - " . $titulo,
                    "quantity" => 1,
                    "unit_price" => (float) $pago->monto
                ]
            ],
            "external_reference" => (string) $pago->idPago,
            "back_urls" => [
                "success" => $backend . "/payments/success",
                "failure" => $backend . "/payments/failure",
                "pending" => $backend . "/payments/pending"
            ],
            "notification_url" => $backend . "/api/webhooks/mercadopago",
            "auto_return" => "approved"
        ];

        \Log::info("MP Payload Enviado", ["json" => json_encode($payload)]);

        $client = new PreferenceClient();

        try {
            $preference = $client->create($payload);

            \Log::info("Preferencia creada en MP con ID: " . $preference->id);
            return $preference;
        } catch (\MercadoPago\Exceptions\MPApiException $e) {
            $response = $e->getApiResponse();
            $content = $response->getContent();
            
            \Log::error("Error API MercadoPago:", [
                "status_code" => $response->getStatusCode(),
                "content" => $content,
                "message" => $e->getMessage()
            ]);
            
            throw new \Exception("MercadoPago API Error: " . json_encode($content));
        } catch (\Exception $e) {
            \Log::error("Error GENERAL en SDK de MercadoPago: " . $e->getMessage());
            throw $e;
        }
    }
}