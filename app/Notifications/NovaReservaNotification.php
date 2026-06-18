<?php

namespace App\Notifications;

use App\Models\Reserva;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class NovaReservaNotification extends Notification
{
    use Queueable;

    protected $reserva;

    public function __construct(Reserva $reserva)
    {
        $this->reserva = $reserva;
    }

    public function via($notifiable)
    {
        return ['database']; // Salva direto no banco
    }

    public function toArray($notifiable)
    {
        return [
            'mensagem' => 'Nova intenção de viagem registrada por ' . ($this->reserva->user->name ?? 'Cliente') . ' para ' . $this->reserva->destino->cidade . '.',
            'link' => route('reservas.index'),
            'icone' => 'bi-plane-fill text-success'
        ];
    }
}