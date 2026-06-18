<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Categoria;
use App\Models\Destino;

class DestinoSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Cria as Categorias primeiro para podermos usar os IDs
        $internacional = Categoria::create([
            'nome' => 'Internacional',
            'descricao' => 'Destinos incríveis fora do país.'
        ]);

        $praia = Categoria::create([
            'nome' => 'Praia e Sol',
            'descricao' => 'Destinos tropicais com praias maravilhosas.'
        ]);

        // 2. Insere os Destinos Modelos Prontos
        Destino::create([
            'categoria_id' => $internacional->id,
            'cidade' => 'Paris',
            'pais' => 'França',
            'descricao' => 'Explore a Cidade Luz, visite a Torre Eiffel, caminhe pelas margens do Rio Sena e desfrute da culinária mais charmosa do mundo.',
            'preco_pacote' => 4500.00,
            'duracao_dias' => 7,
            'imagem' => 'paris.webp' // Nome do arquivo que colocaremos na pasta
        ]);

        Destino::create([
            'categoria_id' => $internacional->id,
            'cidade' => 'Roma',
            'pais' => 'Itália',
            'descricao' => 'Viaje no tempo conhecendo o Coliseu, o Fórum Romano e a Fontana di Trevi, além de saborear as massas e gelatos italianos autênticos.',
            'preco_pacote' => 3900.00,
            'duracao_dias' => 6,
            'imagem' => 'roma.webp'
        ]);

        Destino::create([
            'categoria_id' => $praia->id,
            'cidade' => 'Rio de Janeiro',
            'pais' => 'Brasil',
            'descricao' => 'Visite o Cristo Redentor, o Pão de Açúcar e curta os dias de sol nas praias de Copacabana e Ipanema com toda a energia carioca.',
            'preco_pacote' => 1500.00,
            'duracao_dias' => 5,
            'imagem' => 'rio.webp'
        ]);
    }
}