<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Destino extends Model
{
    use HasFactory;

    // Define quais campos o formulário pode preencher diretamente no banco
    protected $fillable = [
    'categoria_id', 
    'cidade', 
    'pais', 
    'descricao', 
    'preco_pacote', 
    'duracao_dias', 
    'imagem'
];

    /**
     * Relacionamento: Um destino pertence a uma categoria específica
     */
    public function categoria()
    {
        return $this->belongsTo(Categoria::class);
    }

    /**
     * Relacionamento: Um destino pode ter muitas avaliações
     */
    public function avaliacoes()
    {
        return $this->hasMany(Avaliacao::class);
    }

    /**
     * Relacionamento: Um destino possui muitas reservas (sua tabela original)
     */
    public function reservas()
    {
        return $this->hasMany(Reserva::class);
    }
}