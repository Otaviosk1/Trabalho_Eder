@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="fw-bold text-dark">Gerenciar Destinos Turísticos</h2>
        <p class="text-muted mb-0">Painel de controle dos locais cadastrados no sistema.</p>
    </div>
    <a href="{{ route('destinos.create') }}" class="btn btn-success rounded-pill px-3"><i class="bi bi-plus-circle"></i> Novo Destino</a>
</div>

<div class="card shadow-sm border-0 bg-white rounded-3">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>Foto</th>
                    <th>Cidade / País</th>
                    <th>Duração</th>
                    <th>Preço do Pacote</th>
                    <th>Status</th>
                    <th class="text-end">Ações</th>
                </tr>
            </thead>
            <tbody>
                @foreach($destinos as $destino)
                <tr>
                    <td>
                        <img src="{{ url('storage/' . $destino->imagem) }}" alt="{{ $destino->cidade }}" class="img-thumbnail rounded" style="width: 80px; height: 50px; object-fit: cover;">
                    </td>
                    <td>
                        <div class="fw-bold text-dark">{{ $destino->cidade }}</div>
                        <span class="text-muted small">{{ $destino->pais }}</span>
                    </td>
                    <td>{{ $destino->duracao_dias }} dias</td>
                    <td class="text-success fw-bold">R$ {{ number_format($destino->preco_pacote, 2, ',', '.') }}</td>
                    <td>
                        {{-- Selos visuais para identificar o status da sugestão --}}
                        @if(($destino->status ?? 'aprovado') == 'pendente')
                            <span class="badge bg-warning text-dark rounded-pill px-2.5 py-1.5"><i class="bi bi-clock-history"></i> Pendente</span>
                        @elseif(($destino->status ?? 'aprovado') == 'rejeitado')
                            <span class="badge bg-danger rounded-pill px-2.5 py-1.5"><i class="bi bi-x-circle"></i> Rejeitado</span>
                        @else
                            <span class="badge bg-success rounded-pill px-2.5 py-1.5"><i class="bi bi-check-circle"></i> Aprovado</span>
                        @endif
                    </td>
                    <td class="text-end">
                        {{-- Botões de Moderação: Só aparecem se o destino estiver Pendente --}}
                        @if(($destino->status ?? 'aprovado') == 'pendente')
                            <form action="{{ route('destinos.status', [$destino->id, 'aprovado']) }}" method="POST" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-outline-success btn-sm me-1 rounded-circle" title="Aprovar Sugestão">
                                    <i class="bi bi-check-lg"></i>
                                </button>
                            </form>

                            <form action="{{ route('destinos.status', [$destino->id, 'rejeitado']) }}" method="POST" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-outline-secondary btn-sm me-3 rounded-circle" title="Rejeitar Sugestão">
                                    <i class="bi bi-ban"></i>
                                </button>
                            </form>
                        @endif

                        {{-- Botões padrão de Editar e Excluir --}}
                        <a href="{{ route('destinos.edit', $destino->id) }}" class="btn btn-outline-warning btn-sm me-1 rounded-circle" title="Editar">
                            <i class="bi bi-pencil-square"></i>
                        </a>
                        
                        <form action="{{ route('destinos.destroy', $destino->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Tem certeza que deseja excluir este destino?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-outline-danger btn-sm rounded-circle" title="Excluir">
                                <i class="bi bi-trash"></i>
                            </button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection