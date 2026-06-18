@extends('layouts.app')

@section('content')
<div class="row justify-content-center my-4">
    <div class="col-md-8">
        <div class="card shadow-sm border-0 p-4 bg-white rounded-3">
            <div class="d-flex align-items-center gap-3 mb-4">
                <a href="{{ route('destinos.admin') }}" class="btn btn-outline-secondary btn-sm rounded-circle"><i class="bi bi-arrow-left"></i></a>
                <div>
                    <h3 class="fw-bold text-dark mb-0">Editar Destino</h3>
                    <p class="text-muted small mb-0">Altere as informações necessárias do local.</p>
                </div>
            </div>

            <form action="{{ route('destinos.update', $destino->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold text-secondary">Cidade</label>
                        <input type="text" name="cidade" class="form-control" value="{{ $destino->cidade }}" required>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold text-secondary">País</label>
                        <input type="text" name="pais" class="form-control" value="{{ $destino->pais }}" required>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold text-secondary">Preço do Pacote (R$)</label>
                        <input type="number" step="0.01" name="preco_pacote" class="form-control" value="{{ $destino->preco_pacote }}" required>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold text-secondary">Duração (Dias)</label>
                        <input type="number" name="duracao_dias" class="form-control" value="{{ $destino->duracao_dias }}" required>
                    </div>

                    <div class="col-md-12">
                        <label class="form-label fw-semibold text-secondary">Categoria</label>
                        <select name="categoria_id" class="form-select" required>
                            @foreach($categorias as $categoria)
                                <option value="{{ $categoria->id }}" {{ $destino->categoria_id == $categoria->id ? 'selected' : '' }}>
                                    {{ $categoria->nome }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-12">
                        <label class="form-label fw-semibold text-secondary">Descrição do Local</label>
                        <textarea name="descricao" class="form-control" rows="4" required>{{ $destino->descricao }}</textarea>
                    </div>

                    <div class="col-md-12">
                        <label class="form-label fw-semibold text-secondary">Alterar Imagem (Opcional)</label>
                        <input type="file" name="imagem" class="form-control mb-2">
                        <small class="text-muted d-block">Imagem atual:</small>
                        <img src="{{ url('storage/' . $destino->imagem) }}" class="img-thumbnail rounded" style="width: 120px; height: 75px; object-fit: cover;">
                    </div>
                </div>

                <div class="text-end mt-4">
                    <a href="{{ route('destinos.admin') }}" class="btn btn-light rounded-pill px-4 me-2">Cancelar</a>
                    <button type="submit" class="btn btn-success rounded-pill px-4">Salvar Alterações</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection