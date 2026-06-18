@extends('layouts.app')

@section('content')
<div class="container py-2">
    <div class="row mb-4">
        <div class="col d-flex align-items-center justify-content-between">
            <div>
                <h2 class="fw-bold text-dark mb-1"><i class="bi bi-clock-history text-success"></i> Central de Notificações</h2>
                <p class="text-muted mb-0">Histórico completo de auditoria e atualizações do seu painel.</p>
            </div>
            @if(Auth::user()->unreadNotifications->count() > 0)
                <a href="{{ route('notificacoes.ler.todas') }}" class="btn btn-light btn-sm border rounded-pill px-3 shadow-sm">
                    Marcar todas como lidas
                </a>
            @endif
        </div>
    </div>

    <div class="card shadow-sm border-0 rounded-3 p-4">
        @if($notificacoes->isEmpty())
            <div class="text-center py-5 text-muted">
                <i class="bi bi-bell-slash fs-1 opacity-50 d-block mb-2"></i>
                Nenhum alerta registrado no seu histórico.
            </div>
        @else
            <div class="list-group list-group-flush">
                @foreach($notificacoes as $notificacao)
                    <div class="list-group-item py-3 px-2 d-flex align-items-start gap-3 border-bottom @if($notificacao->unread()) bg-light border-start border-success border-3 fw-semibold @endif">
                        <div class="bg-white shadow-sm border rounded-circle p-2 d-flex align-items-center justify-content-center" style="width: 42px; height: 42px;">
                            <i class="bi {{ $notificacao->data['icone'] }} fs-5"></i>
                        </div>
                        <div class="flex-grow-1">
                            <span class="d-block text-dark" style="font-size: 0.95rem;">{{ $notificacao->data['mensagem'] }}</span>
                            <span class="text-muted small d-block mt-1"><i class="bi bi-stopwatch me-1"></i> {{ $notificacao->created_at->format('d/m/Y H:i') }} ({{ $notificacao->created_at->diffForHumans() }})</span>
                        </div>
                        <div>
                            <a href="{{ $notificacao->data['link'] }}" class="btn btn-outline-success btn-xs rounded-pill px-3 py-1" style="font-size: 0.75rem;">Acessar</a>
                        </div>
                    </div>
                @endforeach
            </div>
            <div class="mt-4">
                {{ $notificacoes->links('pagination::bootstrap-5') }}
            </div>
        @endif
    </div>
</div>
@endsection