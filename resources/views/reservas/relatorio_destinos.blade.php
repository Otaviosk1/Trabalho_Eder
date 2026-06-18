@extends('layouts.app')

@section('content')
<div class="container py-2">
    {{-- Topo do Painel --}}
    <div class="row mb-4 align-items-center justify-content-between area-nao-imprimivel">
        <div class="col-md-8">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-2">
                    <li class="breadcrumb-item"><a href="{{ route('reservas.index') }}" class="text-success text-decoration-none">Painel</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Relatório por Destino</li>
                </ol>
            </nav>
            <h2 class="fw-bold text-dark mb-1">
                <i class="bi bi-bar-chart-line-fill text-success"></i> Desempenho Analítico por Destino
            </h2>
            <p class="text-muted mb-0">Análise detalhada de faturamento, fluxo de passageiros e conversão de vendas por pacote.</p>
        </div>
        <div class="col-md-4 text-md-end mt-3 mt-md-0">
            <button onclick="window.print()" class="btn btn-outline-secondary btn-sm rounded-pill px-3 shadow-sm me-2">
                <i class="bi bi-printer-fill me-1"></i> Imprimir
            </button>
            <a href="{{ route('reservas.index') }}" class="btn btn-light btn-sm border rounded-pill px-3 shadow-sm">
                Voltar ao Painel
            </a>
        </div>
    </div>

    {{-- BARRA DE FILTROS POR PERÍODO DE DATA E BUSCA POR DESTINO --}}
    <div class="card shadow-sm border-0 rounded-3 p-3 mb-4 area-nao-imprimivel">
        <form action="{{ route('reservas.relatorio.destinos') }}" method="GET" class="row g-3 align-items-end">
            <div class="col-md-3">
                <label class="form-label text-secondary small fw-semibold">Buscar Destino:</label>
                <div class="input-group">
                    <span class="input-group-text bg-white border-end-0 text-muted"><i class="bi bi-search"></i></span>
                    <input type="text" name="busca_destino" class="form-control border-start-0 border shadow-sm py-2" placeholder="Ex: Roma ou Itália..." value="{{ request('busca_destino') }}">
                </div>
            </div>
            <div class="col-md-3">
                <label class="form-label text-secondary small fw-semibold">Data Inicial do Embarque:</label>
                <input type="date" name="data_inicio" class="form-control border shadow-sm py-2" value="{{ $dataInicio }}">
            </div>
            <div class="col-md-3">
                <label class="form-label text-secondary small fw-semibold">Data Final do Embarque:</label>
                <input type="date" name="data_fim" class="form-control border shadow-sm py-2" value="{{ $dataFim }}">
            </div>
            <div class="col-md-3 d-flex gap-2">
                <button type="submit" class="btn btn-success py-2 w-100 rounded-pill shadow-sm">
                    <i class="bi bi-filter-left me-1"></i> Filtrar
                </button>
                <a href="{{ route('reservas.relatorio.destinos') }}" class="btn btn-light border py-2 w-100 rounded-pill shadow-sm text-secondary">
                    Limpar
                </a>
            </div>
        </form>
    </div>

    {{-- Tabela de Desempenho --}}
    <div class="card shadow-sm border-0 rounded-3">
        <div class="card-header bg-dark text-white py-3">
            <h5 class="fw-bold mb-0"><i class="bi bi-grid-3x3-gap me-2"></i> Mapeamento de Vendas e Conversões</h5>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light text-secondary small fw-bold text-uppercase">
                    <tr>
                        <th class="ps-4">Destino</th>
                        <th class="text-center">Preço Unitário</th>
                        <th class="text-center">Total Passageiros</th>
                        <th class="text-center">Pedidos Recebidos</th>
                        <th class="text-center">Taxa de Aprovação</th>
                        <th class="text-end pe-4">Faturamento Bruto</th>
                    </tr>
                </thead>
                <tbody class="text-secondary">
                    @forelse($relatorio as $item)
                        <tr>
                            <td class="ps-4">
                                <div class="d-flex align-items-center gap-3">
                                    <img src="{{ asset('storage/' . $item['destino']->imagem) }}" class="rounded-2" style="width: 55px; height: 40px; object-fit: cover; border: 1px solid #e2e8f0;">
                                    <div>
                                        <span class="fw-bold text-dark d-block">{{ $item['destino']->cidade }}</span>
                                        <span class="text-muted small">{{ $item['destino']->pais }}</span>
                                    </div>
                                </div>
                            </td>
                            <td class="text-center fw-semibold text-dark">R$ {{ number_format($item['destino']->preco_pacote, 2, ',', '.') }}</td>
                            <td class="text-center fw-bold text-dark">
                                <span class="badge bg-light text-dark border px-3 py-2 rounded-pill">{{ $item['total_pessoas'] }}</span>
                            </td>
                            <td class="text-center text-muted">{{ $item['total_solicitacoes'] }}</td>
                            <td class="text-center">
                                <div class="d-flex align-items-center justify-content-center gap-2">
                                    <div class="progress" style="width: 60px; height: 6px;">
                                        <div class="progress-bar bg-success" role="progressbar" style="width: {{ $item['taxa_aprovacao'] }}%" aria-valuenow="{{ $item['taxa_aprovacao'] }}" aria-valuemin="0" aria-valuemax="100"></div>
                                    </div>
                                    <span class="fw-bold text-dark small">{{ number_format($item['taxa_aprovacao'], 1, ',', '.') }}%</span>
                                </div>
                            </td>
                            <td class="text-end pe-4 fw-bold text-success fs-5">
                                R$ {{ number_format($item['faturamento'], 2, ',', '.') }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-4 text-muted">
                                <i class="bi bi-folder-x fs-3 d-block mb-2"></i>
                                Nenhum destino encontrado para os filtros selecionados.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<style>
    @media print {
        .area-nao-imprimivel, .btn, .navbar, header, footer {
            display: none !important;
        }
        body { background-color: #fff !important; }
        .card { box-shadow: none !important; border: 1px solid #dee2e6 !important; }
    }
</style>
@endsection