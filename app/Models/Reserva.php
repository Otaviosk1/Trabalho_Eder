<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reserva extends Model
{
    use HasFactory;

    // Garanta que o campo 'cliente' esteja listado aqui no fillable:
    protected $fillable = [
        'user_id',
        'destino_id',
        'data_viagem',
        'vagas',
        'status',
        'cliente' // ⬅️ ADICIONE ESTA LINHA SE ESTIVER FALTANDO!
    ];

    /**
     * Relacionamento: Uma reserva pertence a um destino
     */
    public function destino()
    {
        return $this->belongsTo(Destino::class);
    }

    /**
     * Relacionamento: Uma reserva pertence a um usuário
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}