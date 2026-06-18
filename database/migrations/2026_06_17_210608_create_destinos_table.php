<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
{
    Schema::create('destinos', function (Blueprint $table) {
        $table->id();
        $table->foreignId('categoria_id')->constrained('categorias')->onDelete('cascade');
        $table->string('cidade');
        $table->string('pais');
        $table->text('descricao');
        $table->decimal('preco_pacote', 10, 2);
        $table->integer('duracao_dias');
        $table->string('imagem');
        $table->string('status')->default('pendente');
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('destinos');
    }
};
