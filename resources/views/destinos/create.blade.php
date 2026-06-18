@extends('layouts.app')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card shadow-sm p-4">
            <div class="d-flex align-items-center gap-3 mb-4">
                <div class="bg-success bg-opacity-10 text-success p-3 rounded-3 fs-3">
                    <i class="bi bi-file-earmark-plus-fill"></i>
                </div>
                <div>
                    <h3 class="fw-bold text-dark mb-0">Cadastrar Novo Destino</h3>
                    <p class="text-muted small mb-0">Insira as informações do local e vincule a uma categoria ativa.</p>
                </div>
            </div>

            {{-- Bloco para capturar e exibir erros de validação --}}
            @if ($errors->any())
                <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm rounded-3 mb-4" role="alert">
                    <h6 class="fw-bold mb-2"><i class="bi bi-exclamation-triangle-fill me-2"></i> Por favor, corrija os erros abaixo:</h6>
                    <ul class="mb-0 small">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <form action="{{ route('destinos.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold text-secondary">Cidade</label>
                        <input type="text" name="cidade" class="form-control" placeholder="Ex: Votuporanga" required>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold text-secondary">País</label>
                        <input type="text" name="pais" class="form-control" placeholder="Ex: Brasil" required>
                    </div>

                    <div class="col-md-12">
                        <label class="form-label fw-semibold text-secondary">Categoria de Turismo</label>
                        <select name="categoria_id" class="form-select" required>
                            <option value="" disabled selected>Selecione o estilo da viagem...</option>
                            @foreach($categorias as $categoria)
                                <option value="{{ $categoria->id }}">{{ $categoria->nome }}</option>
                            @endforeach
                        </select>
                        <div class="form-text text-muted">Caso a categoria não esteja aqui, cadastre-a no menu de Categorias.</div>
                    </div>

                    <div class="col-md-12">
                        <label class="form-label fw-semibold text-secondary">Descrição Completa</label>
                        <textarea name="descricao" class="form-control" rows="4" placeholder="Fale sobre os pontos turísticos, cultura local e detalhes do pacote..." required></textarea>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold text-secondary">Preço do Pacote (R$)</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light text-secondary">R$</span>
                            <input type="number" name="preco_pacote" step="0.01" class="form-control" placeholder="0,00" required>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold text-secondary">Duração (Dias)</label>
                        <input type="number" name="duracao_dias" class="form-control" min="1" placeholder="Ex: 5" required>
                    </div>

                    <div class="col-md-12">
                        <label class="form-label fw-semibold text-secondary">Foto de Capa (Upload de Imagem)</label>
                        <input type="file" name="imagem" class="form-control" accept="image/*" required>
                        <div class="form-text">Formatos recomendados: JPG ou PNG de alta resolução.</div>
                    </div>
                </div>

                <div class="mt-4 pt-3 border-top d-flex justify-content-end gap-2">
                    <a href="{{ route('destinos.admin') }}" class="btn btn-light px-4">Cancelar</a>
                    <button type="submit" class="btn btn-success px-4 fw-bold">Salvar Destino</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection