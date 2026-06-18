<?php

namespace App\Notifications;

use App\Models\Reserva;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class StatusReservaNotification extends Notification
{
    use Queueable;

    protected $reserva;

    public function __construct(Reserva $reserva)
    {
        $this->reserva = $reserva;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toArray($notifiable)
    {
        $status = $this->reserva->status;
        $icone = $status == 'aprovada' ? 'bi-check-circle-fill text-success' : ($status == 'realizada' ? 'bi-bookmark-check-fill text-primary' : 'bi-x-circle-fill text-danger');

        return [
            'mensagem' => 'Sua solicitação para ' . $this->reserva->destino->cidade . ' foi ' . $status . '!',
            'link' => route('reservas.index'),
            'icone' => $icone
        ];
    }
}