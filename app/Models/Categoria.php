<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Categoria extends Model
{
    use HasFactory;

    // Permite salvar esses campos via formulário ou seeder
    protected $fillable = ['nome', 'descricao'];

    /**
     * Relacionamento: Uma categoria possui muitos destinos
     */
    public function destinos()
    {
        return $this->hasMany(Destino::class);
    }
}