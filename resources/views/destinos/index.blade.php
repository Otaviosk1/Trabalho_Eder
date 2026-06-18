@extends('layouts.app')

@section('content')
<div class="row mb-4 align-items-center">
    <div class="col">
        <h2 class="fw-bold text-secondary">Descubra seu próximo destino</h2>
        <p class="text-muted">Explore os melhores pacotes de viagens e faça já sua reserva.</p>
    </div>
</div>

<div class="row row-cols-1 row-cols-md-3 g-4">
    @foreach($destinos as $destino)
        <div class="col">
            <div class="card h-100 shadow-sm">
                <img src="{{ asset('storage/' . $destino->imagem) }}" class="card-img-top" alt="{{ $destino->cidade }}" style="height: 220px; object-fit: cover; border-top-left-radius: 12px; border-top-right-radius: 12px;">
                
                <div class="card-body">
                    <span class="badge bg-soft-primary text-primary bg-opacity-10 mb-2 px-2.5 py-1 text-uppercase font-size-11">{{ $destino->duracao_dias }} Dias</span>
                    <h5 class="card-title fw-bold mb-1">{{ $destino->cidade }}</h5>
                    <p class="text-muted small mb-2"><i class="bi bi-geo-alt"></i> {{ $destino->pais }}</p>
                    
                    {{-- INTEGRAÇÃO: CÁLCULO DA MÉDIA DIRETO NA VIEW PARA O CARD --}}
                    @php
                        $mediaCard = $destino->avaliacoes->avg('nota') ?? 0;
                        $totalAvaliacoes = $destino->avaliacoes->count();
                    @endphp

                    <div class="d-flex align-items-center gap-1 mb-3">
                        <span class="text-warning small d-flex align-items-center gap-0.5">
                            @for($i = 1; $i <= 5; $i++)
                                @if($i <= round($mediaCard))
                                    <i class="bi bi-star-fill"></i>
                                @else
                                    <i class="bi bi-star"></i>
                                @endif
                            @endfor
                        </span>
                        <span class="fw-bold text-dark small ms-1" style="font-size: 0.85rem;">
                            {{ $mediaCard > 0 ? number_format($mediaCard, 1, ',', '.') : '0.0' }}
                        </span>
                        <span class="text-muted" style="font-size: 0.75rem;">
                            ( {{ $totalAvaliacoes }} {{ $totalAvaliacoes == 1 ? 'avaliação' : 'avaliações' }} )
                        </span>
                    </div>

                    <p class="card-text text-secondary text-truncate-2">{{ Str::limit($destino->descricao, 100) }}</p>
                </div>
                
                <div class="card-footer bg-transparent border-0 d-flex justify-content-between align-items-center pb-3">
                    <div>
                        <span class="text-muted small d-block">A partir de</span>
                        <strong class="text-success h5 mb-0">R$ {{ number_format($destino->preco_pacote, 2, ',', '.') }}</strong>
                    </div>
                    {{-- Botão para abrir os detalhes e fazer reserva --}}
                    <a href="{{ route('destinos.show', $destino->id) }}" class="btn btn-success btn-sm px-3 rounded-pill">Detalhes</a>
                </div>
            </div>
        </div>
    @endforeach
</div>
@endsection