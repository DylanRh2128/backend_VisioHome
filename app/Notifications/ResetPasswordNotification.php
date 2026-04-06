<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class ResetPasswordNotification extends Notification
{
    public $token;

    public function __construct($token)
    {
        $this->token = $token;
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $frontendUrl = config('app.url_frontend', 'http://localhost:5173');
        // El frontend recibirá token y correo para validar la recuperación
        $url = "{$frontendUrl}/reset-password?token={$this->token}&correo=" . urlencode($notifiable->correo);

        \Illuminate\Support\Facades\Log::info('[ResetPasswordNotification] Building mail', [
            'to'    => $notifiable->correo,
            'token' => substr($this->token, 0, 8) . '...',
            'url'   => $url,
        ]);

return (new MailMessage)
    ->subject('Restablecer Contraseña | VisioHome')
    ->greeting('Hola, ' . ($notifiable->nombre ?? 'Usuario'))
    ->line('Recibimos una solicitud para restablecer tu contraseña en la plataforma VisioHome.')
    ->action('Restablecer Acceso', $url)
    ->line('Si no realizaste esta solicitud, puedes ignorar este correo de seguridad.')
    ->salutation('El equipo de VisioHome');
    }
}
