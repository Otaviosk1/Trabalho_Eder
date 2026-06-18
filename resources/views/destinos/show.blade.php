@extends('layouts.app')

@section('content')
<div class="row g-4">
    <div class="col-md-7">
        {{-- Card de Detalhes do Destino --}}
        <div class="card shadow-sm overflow-hidden mb-4">
            <img src="{{ asset('storage/' . $destino->imagem) }}" class="img-fluid" alt="{{ $destino->cidade }}" style="width: 100%; height: 400px; object-fit: cover;">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <span class="badge bg-success bg-opacity-10 text-success px-3 py-2 text-uppercase">{{ $destino->categoria->nome }}</span>
                    <span class="text-muted"><i class="bi bi-clock-history"></i> {{ $destino->duracao_dias }} dias de viagem</span>
                </div>
                
                <h1 class="fw-bold text-dark mb-1">{{ $destino->cidade }}</h1>
                <p class="text-muted fs-5 mb-2"><i class="bi bi-geo-alt-fill text-success"></i> {{ $destino->pais }}</p>
                
                {{-- EXIBIÇÃO DA MÉDIA DE NOTAS COM ESTRELAS NO TOPO --}}
                <div class="d-flex align-items-center gap-2 mb-4 bg-light p-2 rounded-3 inline-block style-fit w-fit px-3 border border-light-subtle">
                    <span class="text-warning fs-5 d-flex align-items-center gap-1">
                        @for($i = 1; $i <= 5; $i++)
                            @if($i <= round($mediaNotas))
                                <i class="bi bi-star-fill"></i>
                            @else
                                <i class="bi bi-star"></i>
                            @endif
                        @endfor
                    </span>
                    <span class="fw-bold text-dark fs-6 mt-0.5">
                        {{ number_format($mediaNotas, 1, ',', '.') }}
                    </span>
                    <span class="text-muted small mt-0.5">
                        ({{ $destino->avaliacoes->count() }} {{ $destino->avaliacoes->count() == 1 ? 'avaliação' : 'avaliações' }})
                    </span>
                </div>
                
                <h5 class="fw-bold text-secondary">Sobre o destino</h5>
                <p class="text-secondary lh-base" style="text-align: justify;">{{ $destino->descricao }}</p>
            </div>
        </div>

        {{-- Seção de Depoimentos e Avaliações --}}
        <div class="card shadow-sm p-4 mb-4">
            <h4 class="fw-bold text-dark mb-4"><i class="bi bi-chat-left-heart-fill text-success"></i> Avaliações dos Viajantes</h4>

            @if($destino->avaliacoes->isEmpty())
                <p class="text-muted mb-4">Nenhum comentário deixado ainda. Seja o primeiro a avaliar!</p>
            @else
                <div class="d-flex flex-column gap-3 mb-4">
                    @foreach($destino->avaliacoes as $avaliacao)
                        <div class="p-3 bg-light rounded-3">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <strong class="text-dark">{{ $avaliacao->user->name ?? $avaliacao->nome_usuario ?? 'Viajante' }}</strong>
                                <span class="text-warning">
                                    @for($i = 1; $i <= 5; $i++)
                                        <i class="bi bi-star{{ $i <= $avaliacao->nota ? '-fill' : '' }}"></i>
                                    @endfor
                                </span>
                            </div>
                            <p class="text-secondary small mb-0">"{{ $avaliacao->comentario }}"</p>
                        </div>
                    @endforeach
                </div>
            @endif

            <hr class="text-muted my-4">

            {{-- Formulário para Deixar Nova Avaliação com Trava de Segurança --}}
            <h5 class="fw-bold text-dark mb-3">Deixe sua Avaliação</h5>
            
            @guest
                <div class="alert alert-light border small text-center p-3 rounded-3" role="alert">
                    Para enviar uma avaliação, faça <a href="{{ route('login') }}" class="text-success fw-bold text-decoration-none">Login</a> primeiro!
                </div>
            @else
                @if($podeAvaliar)
                    <form action="{{ route('avaliacoes.store', $destino->id) }}" method="POST">
                        @csrf
                        
                        <div class="mb-3">
                            <label class="form-label fw-semibold text-secondary small">Sua Nota</label>
                            <select name="nota" class="form-select text-warning fw-bold" required>
                                <option value="5">⭐⭐⭐⭐⭐ (5 - Excelente)</option>
                                <option value="4">⭐⭐⭐⭐ (4 - Muito Bom)</option>
                                <option value="3">⭐⭐⭐ (3 - Regular)</option>
                                <option value="2">⭐⭐ (2 - Ruim)</option>
                                <option value="1">⭐ (1 - Péssimo)</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold text-secondary small">Seu Comentário</label>
                            <textarea name="comentario" class="form-control" rows="3" placeholder="Conte aos outros viajantes como foi sua experiência neste lugar..." required></textarea>
                        </div>

                        <button type="submit" class="btn btn-success rounded-pill px-4 fw-semibold small">
                            <i class="bi bi-send-fill me-1"></i> Publicar Avaliação
                        </button>
                    </form>
                @else
                    <div class="alert alert-warning border-0 bg-warning bg-opacity-10 text-warning-emphasis small p-3 rounded-3" role="alert">
                        <i class="bi bi-excluir-octagon-fill me-1"></i> <strong>Avaliação restrita:</strong> Apenas clientes que realizaram e concluíram esta viagem com a VotuTour podem deixar um depoimento.
                    </div>
                @endif
            @endguest
        </div>
    </div>

    {{-- Coluna Direita: Formulário de Reserva --}}
    <div class="col-md-5">
        <div class="card shadow-sm p-4 sticky-top" style="top: 20px; z-index: 10;">
            <span class="text-muted small d-block">Valor do pacote por pessoa</span>
            <h2 class="text-success fw-bold mb-4">R$ {{ number_format($destino->preco_pacote, 2, ',', '.') }}</h2>
            
            <hr class="text-muted my-3">
            
            <h5 class="fw-bold text-dark mb-3">Solicitar Reserva</h5>
            
            <form action="{{ route('reservas.store') }}" method="POST">
                @csrf
                <input type="hidden" name="destino_id" value="{{ $destino->id }}">

                <div class="mb-3">
                    <label class="form-label fw-semibold text-secondary">Seu Nome Completo</label>
                    <input type="text" name="cliente" class="form-control" value="{{ Auth::check() ? Auth::user()->name : '' }}" placeholder="Digite seu nome" required>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold text-secondary">Data Pretendida</label>
                    <input type="date" name="data_reserva" class="form-control" required>
                </div>

                <div class="mb-4">
                    <label class="form-label fw-semibold text-secondary">Quantidade de Pessoas</label>
                    <input type="number" name="vagas" class="form-control" min="1" value="1" required>
                </div>

                <button type="submit" class="btn btn-success w-100 py-2.5 fw-bold rounded-3">
                    <i class="bi bi-calendar-check-fill me-2"></i> Confirmar Intenção de Viagem
                </button>
            </form>
        </div>
    </div>
</div>
@endsection