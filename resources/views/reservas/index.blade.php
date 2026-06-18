@extends('layouts.app')

@section('content')
<div class="container py-2 position-relative">

    {{-- NOTIFICAÇÃO FLUTUANTE (Toast de Feedback) --}}
    @if(session('success') || session('error'))
        <div class="position-fixed top-0 end-0 p-3" style="z-index: 1080;">
            <div id="liveToast" class="card border-0 shadow-lg rounded-3 text-white {{ session('success') ? 'bg-success' : 'bg-danger' }}" role="alert" aria-live="assertive" aria-atomic="true" style="min-width: 300px;">
                <div class="card-body d-flex align-items-center justify-content-between py-3 px-4">
                    <div class="d-flex align-items-center gap-2">
                        <i class="bi {{ session('success') ? 'bi-check-circle-fill' : 'bi-exclamation-triangle-fill' }} fs-5"></i>
                        <span class="fw-semibold">{{ session('success') ?? session('error') }}</span>
                    </div>
                    <button type="button" class="btn-close btn-close-white" onclick="fecharToast()" aria-label="Close"></button>
                </div>
            </div>
        </div>
    @endif

    {{-- Topo do Painel --}}
    <div class="row mb-4 area-nao-imprimivel">
        <div class="col d-flex align-items-center justify-content-between flex-wrap gap-3">
            <div>
                <h2 class="fw-bold text-dark mb-1">
                    @if(Auth::user()->is_admin)
                        <i class="bi bi-shield-lock-fill text-success"></i> Painel de Moderação
                    @else
                        <i class="bi bi-compass-fill text-success"></i> Meu Histórico de Viagens
                    @endif
                </h2>
                <p class="text-muted mb-0">Acompanhe o fluxo e o status das intenções de viagem de forma organized.</p>
            </div>
            
            {{-- Botões de Ação do Topo (Apenas Admin) --}}
            @if(Auth::user()->is_admin)
                <div class="d-flex gap-2">
                    <a href="{{ route('reservas.relatorio.destinos') }}" class="btn btn-success btn-sm rounded-pill px-4 shadow-sm d-flex align-items-center gap-2">
                        <i class="bi bi-bar-chart-line-fill"></i> Ver Relatório por Destino
                    </a>
                    <button onclick="window.print()" class="btn btn-outline-secondary btn-sm rounded-pill px-3 shadow-sm d-flex align-items-center gap-2">
                        <i class="bi bi-printer-fill"></i> Imprimir Relatório
                    </button>
                </div>
            @endif
        </div>
    </div>

    {{-- BARRA DE PESQUISA RÁPIDA (Exclusiva para o Administrador) --}}
    @if(Auth::user()->is_admin)
        <div class="row mb-4 area-nao-imprimivel">
            <div class="col-md-6">
                <div class="input-group shadow-sm rounded-3">
                    <span class="input-group-text bg-white border-0 text-muted"><i class="bi bi-search"></i></span>
                    <input type="text" id="inputBuscaReservas" class="form-control border-0 py-2.5" placeholder="Filtrar por nome do viajante ou cidade de destino..." onkeyup="filtrarPainelReservas()">
                </div>
            </div>
        </div>
    @endif

    {{-- CONTADORES ESTATÍSTICOS (Exclusivo para Administrador) --}}
    @if(Auth::user()->is_admin)
        <div class="row g-3 mb-5">
            {{-- Card 1: Faturamento --}}
            <div class="col-md-4">
                <div class="card shadow-sm border-0 bg-white rounded-3 p-3">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <span class="text-muted small fw-semibold d-block text-uppercase mb-1">Total Faturado</span>
                            <h3 class="fw-bold text-success mb-0">R$ {{ number_format($totalFaturado, 2, ',', '.') }}</h3>
                        </div>
                        <div class="bg-success bg-opacity-10 text-success rounded-circle p-3 fs-4 d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                            <i class="bi bi-cash-stack"></i>
                        </div>
                    </div>
                    <span class="text-muted text-xs mt-2 d-block" style="font-size: 0.75rem;"><i class="bi bi-info-circle me-1"></i> Pacotes aprovados e realizados</span>
                </div>
            </div>

            {{-- Card 2: Assentos Ocupados --}}
            <div class="col-md-4">
                <div class="card shadow-sm border-0 bg-white rounded-3 p-3">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <span class="text-muted small fw-semibold d-block text-uppercase mb-1">Vagas Preenchidas</span>
                            <h3 class="fw-bold text-dark mb-0">{{ $vagasReservadas }}</h3>
                        </div>
                        <div class="bg-primary bg-opacity-10 text-primary rounded-circle p-3 fs-4 d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                            <i class="bi bi-people-fill"></i>
                        </div>
                    </div>
                    <span class="text-muted text-xs mt-2 d-block" style="font-size: 0.75rem;"><i class="bi bi-info-circle me-1"></i> Contagem total de passageiros</span>
                </div>
            </div>

            {{-- Card 3: Taxa de Conversão --}}
            <div class="col-md-4">
                <div class="card shadow-sm border-0 bg-white rounded-3 p-3">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <span class="text-muted small fw-semibold d-block text-uppercase mb-1">Taxa de Aprovação</span>
                            <h3 class="fw-bold text-primary mb-0">{{ number_format($taxaAprovacao, 1, ',', '.') }}%</h3>
                        </div>
                        <div class="bg-info bg-opacity-10 text-info rounded-circle p-3 fs-4 d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                            <i class="bi bi-graph-up-arrow"></i>
                        </div>
                    </div>
                    <span class="text-muted text-xs mt-2 d-block" style="font-size: 0.75rem;"><i class="bi bi-info-circle me-1"></i> Percentual de pedidos aceitos</span>
                </div>
            </div>
        </div>
    @endif

    {{-- ===================================================================================== --}}
    {{-- SEÇÃO 1: SOLICITAÇÕES PENDENTES --}}
    {{-- ===================================================================================== --}}
    <div class="card shadow-sm border-0 rounded-3 mb-5">
        <div class="card-header bg-warning bg-opacity-10 border-0 py-3 d-flex align-items-center justify-content-between">
            <h5 class="fw-bold text-warning-emphasis mb-0"><i class="bi bi-clock-history me-2"></i> 1. Aguardando Aprovação (Pendentes)</h5>
            <span class="badge bg-warning text-dark rounded-pill px-3">{{ $pendentes->total() }}</span>
        </div>
        @if($pendentes->isEmpty())
            <div class="card-body py-5 text-center text-muted">
                <i class="bi bi-calendar2-x fs-2 d-block mb-2 text-secondary opacity-50"></i>
                @if(Auth::user()->is_admin)
                    <span class="small">Nenhuma solicitação pendente no momento.</span>
                @else
                    <span class="small d-block mb-3">Você não tem nenhuma intenção de viagem aguardando aprovação.</span>
                    <a href="{{ route('destinos.index') }}" class="btn btn-success btn-sm rounded-pill px-4 shadow-sm area-nao-imprimivel">Explorar Destinos</a>
                @endif
            </div>
        @else
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light text-secondary small fw-bold text-uppercase">
                        <tr>
                            <th class="ps-4">Destino</th>
                            <th>Viajante</th>
                            <th>Data Pretendida</th>
                            <th class="text-center">Vagas</th>
                            <th>Total</th>
                            <th class="text-end pe-4 area-nao-imprimivel">Ações</th>
                        </tr>
                    </thead>
                    <tbody class="text-secondary">
                        @foreach($pendentes as $reserva)
                            <tr class="linha-reserva">
                                <td class="ps-4">
                                    <div class="d-flex align-items-center gap-3">
                                        <img src="{{ asset('storage/' . $reserva->destino->imagem) }}" class="rounded-2" style="width: 55px; height: 40px; object-fit: cover; border: 1px solid #e2e8f0;">
                                        <div>
                                            <span class="fw-bold text-dark d-block">{{ $reserva->destino->cidade }}</span>
                                            <span class="text-muted small">{{ $reserva->destino->pais }}</span>
                                        </div>
                                    </div>
                                </td>
                                <td class="fw-semibold text-dark">{{ $reserva->user->name ?? $reserva->cliente ?? 'Viajante' }}</td>
                                <td><i class="bi bi-calendar3 text-warning me-1"></i> {{ date('d/m/Y', strtotime($reserva->data_viagem)) }}</td>
                                <td class="text-center fw-bold text-dark">{{ $reserva->vagas }}</td>
                                <td class="fw-bold text-success">R$ {{ number_format($reserva->destino->preco_pacote * $reserva->vagas, 2, ',', '.') }}</td>
                                <td class="text-end pe-4 area-nao-imprimivel">
                                    @if(Auth::user()->is_admin)
                                        <div class="btn-group btn-group-sm border rounded-pill shadow-sm bg-white overflow-hidden">
                                            <a href="{{ route('reservas.status', [$reserva->id, 'aprovada']) }}" class="btn btn-light text-success border-0 px-3" title="Aprovar Reserva"><i class="bi bi-check-lg"></i> Approvar</a>
                                            <a href="{{ route('reservas.status', [$reserva->id, 'cancelada']) }}" class="btn btn-light text-danger border-0 px-3" title="Rejeitar/Cancelar"><i class="bi bi-ban"></i> Rejeitar</a>
                                        </div>
                                    @else
                                        <form action="{{ route('reservas.destroy', $reserva->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Tem certeza de que deseja cancelar esta intenção de viagem?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-outline-danger btn-sm rounded-circle" title="Cancelar Pedido"><i class="bi bi-trash"></i></button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="card-footer bg-white border-0 py-3 area-nao-imprimivel">
                {{ $pendentes->appends(request()->except('pag_pendentes'))->links('pagination::bootstrap-5') }}
            </div>
        @endif
    </div>

    {{-- ===================================================================================== --}}
    {{-- SEÇÃO 2: VIAGENS APROVADAS --}}
    {{-- ===================================================================================== --}}
    <div class="card shadow-sm border-0 rounded-3 mb-5">
        <div class="card-header bg-primary bg-opacity-10 border-0 py-3 d-flex align-items-center justify-content-between">
            <h5 class="fw-bold text-primary-emphasis mb-0"><i class="bi bi-calendar-check me-2"></i> 2. Próximas Viagens Confirmadas (Aprovadas)</h5>
            <span class="badge bg-primary text-white rounded-pill px-3">{{ $aprovadas->total() }}</span>
        </div>
        @if($aprovadas->isEmpty())
            <div class="card-body py-5 text-center text-muted">
                <i class="bi bi-compass fs-2 d-block mb-2 text-secondary opacity-50"></i>
                @if(Auth::user()->is_admin)
                    <span class="small">Nenhuma viagem agendada na lista.</span>
                @else
                    <span class="small d-block mb-3">Você não possui roteiros confirmados para embarque no momento.</span>
                    <a href="{{ route('destinos.index') }}" class="btn btn-outline-primary btn-sm rounded-pill px-4 area-nao-imprimivel">Ver Destinos Disponíveis</a>
                @endif
            </div>
        @else
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light text-secondary small fw-bold text-uppercase">
                        <tr>
                            <th class="ps-4">Destino</th>
                            <th>Viajante</th>
                            <th>Data da Viagem</th>
                            <th class="text-center">Vagas</th>
                            <th>Total</th>
                            <th class="text-end pe-4 area-nao-imprimivel">Ações</th>
                        </tr>
                    </thead>
                    <tbody class="text-secondary">
                        @foreach($aprovadas as $reserva)
                            <tr class="linha-reserva">
                                <td class="ps-4">
                                    <div class="d-flex align-items-center gap-3">
                                        <img src="{{ asset('storage/' . $reserva->destino->imagem) }}" class="rounded-2" style="width: 55px; height: 40px; object-fit: cover; border: 1px solid #e2e8f0;">
                                        <div>
                                            <span class="fw-bold text-dark d-block">{{ $reserva->destino->cidade }}</span>
                                            <span class="text-muted small">{{ $reserva->destino->pais }}</span>
                                        </div>
                                    </div>
                                </td>
                                <td class="fw-semibold text-dark">{{ $reserva->user->name ?? $reserva->cliente ?? 'Viajante' }}</td>
                                <td><i class="bi bi-calendar3 text-primary me-1"></i> {{ date('d/m/Y', strtotime($reserva->data_viagem)) }}</td>
                                <td class="text-center fw-bold text-dark">{{ $reserva->vagas }}</td>
                                <td class="fw-bold text-success">R$ {{ number_format($reserva->destino->preco_pacote * $reserva->vagas, 2, ',', '.') }}</td>
                                <td class="text-end pe-4 area-nao-imprimivel">
                                    @if(Auth::user()->is_admin)
                                        <a href="{{ route('reservas.status', [$reserva->id, 'realizada']) }}" class="btn btn-success btn-sm rounded-pill px-3 shadow-sm font-semibold small">
                                            <i class="bi bi-bookmark-check-fill me-1"></i> Concluir Viagem
                                        </a>
                                    @else
                                        <span class="text-muted small italic"><i class="bi bi-info-circle me-1"></i> Preparando embarque</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="card-footer bg-white border-0 py-3 area-nao-imprimivel">
                {{ $aprovadas->appends(request()->except('pag_aprovadas'))->links('pagination::bootstrap-5') }}
            </div>
        @endif
    </div>

    {{-- ===================================================================================== --}}
    {{-- SEÇÃO 3: HISTÓRICO FINAL --}}
    {{-- ===================================================================================== --}}
    <div class="card shadow-sm border-0 rounded-3">
        <div class="card-header bg-secondary bg-opacity-10 border-0 py-3 d-flex align-items-center justify-content-between">
            <h5 class="fw-bold text-secondary-emphasis mb-0"><i class="bi bi-archive me-2"></i> 3. Histórico Final (Concluídas / Canceladas)</h5>
            <span class="badge bg-secondary text-white rounded-pill px-3">{{ $historico->total() }}</span>
        </div>
        @if($historico->isEmpty())
            <div class="card-body py-4 text-center text-muted small">Nenhum registro no histórico.</div>
        @else
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light text-secondary small fw-bold text-uppercase">
                        <tr>
                            <th class="ps-4">Destino</th>
                            <th>Viajante</th>
                            <th>Data Pretendida</th>
                            <th class="text-center">Vagas</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody class="text-secondary">
                        @foreach($historico as $reserva)
                            <tr class="linha-reserva {{ $reserva->status == 'realizada' ? 'table-success bg-opacity-25' : 'table-danger bg-opacity-25' }}">
                                <td class="ps-4">
                                    <div class="d-flex align-items-center gap-3">
                                        <img src="{{ asset('storage/' . $reserva->destino->imagem) }}" class="rounded-2" style="width: 55px; height: 40px; object-fit: cover; border: 1px solid #e2e8f0;">
                                        <div>
                                            <span class="fw-bold text-dark d-block">{{ $reserva->destino->cidade }}</span>
                                            <span class="text-muted small">{{ $reserva->destino->pais }}</span>
                                        </div>
                                    </div>
                                </td>
                                <td class="fw-semibold text-dark">{{ $reserva->user->name ?? $reserva->cliente ?? 'Viajante' }}</td>
                                <td>
                                    <i class="bi {{ $reserva->status == 'realizada' ? 'bi-calendar-check text-success' : 'bi-calendar-x text-danger' }} me-1"></i> 
                                    {{ date('d/m/Y', strtotime($reserva->data_viagem)) }}
                                </td>
                                <td class="text-center fw-bold text-dark">{{ $reserva->vagas }}</td>
                                
                                {{-- SISTEMA DE AVALIAÇÃO COM TRAVA DE CONTROLE CORRIGIDO --}}
                                <td>
                                    @if($reserva->status == 'realizada')
                                        <div class="d-flex align-items-center gap-2">
                                            <span class="badge bg-success text-white px-2.5 py-1.5 rounded-pill small shadow-sm">Realizada ✅</span>
                                            
                                            @if(!Auth::user()->is_admin)
                                                @php
                                                    $jaAvaliou = \App\Models\Avaliacao::where('user_id', Auth::id())->where('destino_id', $reserva->destino_id)->exists();
                                                @endphp
                                                
                                                @if(!$jaAvaliou)
                                                    {{-- O data-bs-target precisa apontar para a ID única da RESERVA --}}
                                                    <button type="button" class="btn btn-warning btn-xs rounded-pill px-2 py-0.5 area-nao-imprimivel" style="font-size: 0.75rem;" data-bs-toggle="modal" data-bs-target="#modalAvaliarObj-{{ $reserva->id }}">
                                                        <i class="bi bi-star-fill"></i> Avaliar
                                                    </button>

                                                    <div class="modal fade" id="modalAvaliarObj-{{ $reserva->id }}" tabindex="-1" aria-labelledby="labelModal-{{ $reserva->id }}" aria-hidden="true">
                                                        <div class="modal-dialog modal-dialog-centered">
                                                            <div class="modal-content border-0 shadow-lg rounded-3 text-start">
                                                                <form action="{{ route('reservas.avaliar', $reserva->id) }}" method="POST">
                                                                    @csrf
                                                                    <div class="modal-header bg-dark text-white py-3">
                                                                        <h6 class="modal-title fw-bold" id="labelModal-{{ $reserva->id }}"><i class="bi bi-star text-warning me-2"></i> Avaliar destino: {{ $reserva->destino->cidade ?? 'Destino' }}</h6>
                                                                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                                                    </div>
                                                                    <div class="modal-body text-secondary">
                                                                        <div class="mb-3">
                                                                            <label class="form-label fw-semibold small">Sua Nota (Estrelas):</label>
                                                                            <select name="nota" class="form-select border shadow-sm" required>
                                                                                <option value="5">⭐⭐⭐⭐⭐ (Excelente)</option>
                                                                                <option value="4">⭐⭐⭐⭐ (Muito Bom)</option>
                                                                                <option value="3">⭐⭐⭐ (Bom)</option>
                                                                                <option value="2">⭐⭐ (Regular)</option>
                                                                                <option value="1">⭐ (Ruim)</option>
                                                                            </select>
                                                                        </div>
                                                                        <div class="mb-2">
                                                                            <label class="form-label fw-semibold small">Seu Comentário (Opcional):</label>
                                                                            <textarea name="comentario" class="form-control border shadow-sm" rows="3" placeholder="Conte como foi sua experiência de viagem..."></textarea>
                                                                        </div>
                                                                    </div>
                                                                    <div class="modal-footer bg-light border-0 py-2">
                                                                        <button type="button" class="btn btn-light btn-sm border rounded-pill px-3" data-bs-dismiss="modal">Cancelar</button>
                                                                        <button type="submit" class="btn btn-success btn-sm rounded-pill px-4">Enviar Nota</button>
                                                                    </div>
                                                                </form>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @else
                                                    <span class="text-muted small italic" style="font-size: 0.75rem;"><i class="bi bi-check-all text-warning"></i> Avaliado</span>
                                                @endif
                                            @endif
                                        </div>
                                    @else
                                        <span class="badge bg-danger text-white px-2.5 py-1.5 rounded-pill small shadow-sm">
                                            <i class="bi bi-x-circle-fill me-1"></i> Cancelada
                                        </span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="card-footer bg-white border-0 py-3 area-nao-imprimivel">
                {{ $historico->appends(request()->except('pag_historico'))->links('pagination::bootstrap-5') }}
            </div>
        @endif
    </div>
</div>

<style>
    @media print {
        .area-nao-imprimivel, .btn, .btn-group, .input-group, .navbar, .card-footer, header, footer, nav {
            display: none !important;
        }
        body {
            background-color: #fff !important;
            font-size: 12px;
        }
        .container {
            width: 100% !important;
            max-width: 100% !important;
            padding: 0 !important;
        }
        .card {
            box-shadow: none !important;
            border: 1px solid #dee2e6 !important;
            margin-bottom: 20px !important;
            page-break-inside: avoid;
        }
        .card-header {
            background-color: #f8f9fa !important;
            color: #000 !important;
            border-bottom: 1px solid #dee2e6 !important;
        }
    }
</style>

<script>
// Script do Toast de Feedback
document.addEventListener("DOMContentLoaded", function() {
    let toastElement = document.getElementById('liveToast');
    if (toastElement) {
        setTimeout(function() {
            fecharToast();
        }, 4000);
    }
});

function fecharToast() {
    let toastElement = document.getElementById('liveToast');
    if (toastElement) {
        toastElement.style.transition = "opacity 0.5s ease";
        toastElement.style.opacity = "0";
        setTimeout(() => toastElement.remove(), 500);
    }
}
</script>

{{-- Script de Filtragem (Exclusivo Admin) --}}
@if(Auth::user()->is_admin)
<script>
function filtrarPainelReservas() {
    let termoBusca = document.getElementById('inputBuscaReservas').value.toLowerCase();
    let { ...linhas } = document.querySelectorAll('.linha-reserva');

    Object.values(linhas).forEach(function(linha) {
        let conteudoLinha = textContentOrBlank(linha).toLowerCase();
        if (conteudoLinha.includes(termoBusca)) {
            linha.style.setProperty("display", "", "important");
        } else {
            linha.style.setProperty("display", "none", "important");
        }
    });
}

// Retorna o textContent de forma segura
function textContentOrBlank(element) {
    return element ? element.textContent : '';
}
</script>
@endif
@endsection