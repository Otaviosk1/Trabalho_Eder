<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Avaliacao extends Model
{
    use HasFactory;

    // Define o nome correto da tabela no plural
    protected $table = 'avaliacoes';

    // Permite preencher em massa esses campos no banco
    protected $fillable = [
        'user_id', 
        'destino_id', 
        'nome_usuario', 
        'nota', 
        'comentario'
    ];

    /**
     * Relacionamento Inverso: Uma avaliação pertence a um destino
     */
    public function destino()
    {
        return $this->belongsTo(Destino::class);
    }
}