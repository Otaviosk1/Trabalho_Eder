<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VotuTour - Sistema de Turismo</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        body { background-color: #f4f7f5; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        .navbar-brand { font-weight: 700; letter-spacing: 0.5px; color: #198754 !important; display: flex; align-items: center; gap: 8px; }
        .brand-logo { height: 32px; width: auto; object-fit: contain; }
        .card { border: none; border-radius: 12px; transition: transform 0.2s; box-shadow: 0 4px 6px rgba(0,0,0,0.02); }
        .card:hover { transform: translateY(-5px); }
        .nav-link { font-weight: 500; color: #4a5568; }
        .nav-link:hover { color: #198754 !important; }
        .btn-success { background-color: #198754; border-color: #198754; }
        .btn-success:hover { background-color: #146c43; border-color: #146c43; }
        .btn-outline-success { color: #198754; border-color: #198754; }
        .btn-outline-success:hover { background-color: #198754; color: #ffffff; }
        .bg-gradient-green { background: linear-gradient(135deg, #198754 0%, #0f5132 100%); }
        footer { background-color: #ffffff; margin-top: 50px; border-top: 1px solid #e2e8f0; }
    </style>
</head>
<body>

    <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm mb-4">
        <div class="container">
            <a class="navbar-brand" href="{{ route('destinos.index') }}">
                <img src="{{ asset('img/logo_icone.png') }}" alt="VotuTour" class="brand-logo">
                <span>VotuTour</span>
            </a>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('destinos.index') }}">Destinos</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('reservas.index') }}">Reservas Gerais</a>
                    </li>
                    
                    {{-- Estrutura Condicional para Moderação de Papéis --}}
                    @auth
                        @if(Auth::user()->is_admin)
                            {{-- Menu Exclusivo do Administrador --}}
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle text-success" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown">
                                    <i class="bi bi-gear-fill"></i> Painel
                                </a>
                                <ul class="dropdown-menu">
                                    <li><a class="dropdown-item" href="{{ route('destinos.admin') }}">Gerenciar Destinos</a></li>
                                    <li><a class="dropdown-item" href="{{ route('destinos.create') }}">Novo Destino</a></li>
                                </ul>
                            </li>
                        @else
                            {{-- Link com visual padrão corrigido apontando para a rota nomeada do Laravel --}}
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('destinos.create') }}">
                                    Sugerir Destino
                                </a>
                            </li>
                        @endif
                    @endauth
                </ul>

                <form action="{{ route('destinos.index') }}" method="GET" class="d-flex me-3" role="search">
                    <div class="input-group input-group-sm">
                        <input class="form-control" type="search" name="search" placeholder="Para onde você vai?" aria-label="Pesquisar" value="{{ request('search') }}">
                        <button class="btn btn-success" type="submit"><i class="bi bi-search"></i></button>
                    </div>
                </form>

                <ul class="navbar-nav mb-2 mb-lg-0 align-items-center">
                    @guest
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('login') }}"><i class="bi bi-box-arrow-in-right"></i> Login</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link btn btn-success btn-sm text-white ms-2 px-3 rounded-pill" href="{{ route('register') }}">Registrar</a>
                        </li>
                    @else
                        {{-- COMPONENTE: Menu Dropdown do Sininho de Notificações Ativas --}}
                        <li class="nav-item dropdown me-2">
                            <button class="btn btn-light btn-sm rounded-circle position-relative p-2 text-secondary border-0 bg-transparent" type="button" id="dropdownMenuNotificacoes" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="bi bi-bell-fill fs-5"></i>
                                @if(Auth::user()->unreadNotifications->count() > 0)
                                    <span class="position-absolute top-1 start-75 translate-middle badge rounded-pill bg-danger" style="font-size: 0.65rem; padding: 0.35em 0.5em;">
                                        {{ Auth::user()->unreadNotifications->count() }}
                                    </span>
                                @endif
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end shadow-lg border-0 rounded-3 mt-2 py-0 overflow-hidden" aria-labelledby="dropdownMenuNotificacoes" style="width: 320px; max-height: 400px; overflow-y: auto;">
                                <div class="bg-dark text-white px-3 py-2.5 d-flex align-items-center justify-content-between">
                                    <span class="fw-bold small"><i class="bi bi-bell-fill text-success me-1"></i> Notificações</span>
                                    @if(Auth::user()->unreadNotifications->count() > 0)
                                        <a href="{{ route('notificacoes.ler.todas') }}" class="text-white-50 small text-decoration-none" style="font-size: 0.75rem;">Marcar todas como lidas</a>
                                    @endif
                                </div>
                                
                                @if(Auth::user()->notifications->isEmpty())
                                    <li class="text-center py-4 text-muted small px-3">Você não tem nenhuma notificação no momento.</li>
                                @else
                                    @foreach(Auth::user()->notifications->take(5) as $notificacao)
                                        <li>
                                            <a class="dropdown-item py-3 px-3 d-flex align-items-start gap-2 border-bottom @if($notificacao->unread()) bg-light fw-semibold text-dark @else text-secondary @endif" href="{{ $notificacao->data['link'] }}">
                                                <i class="bi {{ $notificacao->data['icone'] }} mt-0.5 fs-5"></i>
                                                <div style="white-space: normal; line-height: 1.3;">
                                                    <span class="d-block text-sm" style="font-size: 0.85rem;">{{ $notificacao->data['mensagem'] }}</span>
                                                    <span class="text-muted text-xs d-block mt-1" style="font-size: 0.7rem;">{{ $notificacao->created_at->diffForHumans() }}</span>
                                                </div>
                                            </a>
                                        </li>
                                    @endforeach
                                    {{-- ATUALIZAÇÃO PASSO 2.3: Link no rodapé para acessar o painel geral --}}
                                    <li>
                                        <a class="dropdown-item text-center small text-success fw-bold py-2 bg-light text-decoration-none border-top" href="{{ route('notificacoes.index') }}">
                                            Ver todas as notificações
                                        </a>
                                    </li>
                                @endif
                            </ul>
                        </li>

                        {{-- Perfil de Usuário Logado --}}
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle fw-semibold" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown">
                                <i class="bi bi-person-circle text-success"></i> {{ Auth::user()->name }}
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0">
                                <li>
                                    <form action="{{ route('logout') }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="dropdown-item text-danger"><i class="bi bi-box-arrow-left"></i> Sair</button>
                                    </form>
                                </li>
                            </ul>
                        </li>
                    @endguest
                </ul>
            </div>
        </div>
    </nav>

    <div class="container">
        {{-- Alertas de Feedback Flutuantes com Dismissible e Sombras --}}
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm rounded-3 mb-4" role="alert">
                <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm rounded-3 mb-4" role="alert">
                <i class="bi bi-exclamation-triangle-fill me-2"></i> {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @yield('content')
    </div>

    <footer class="py-4 bg-white text-center text-muted">
        <div class="container">
            <small>&copy; 2026 VotuTour - Atividade Acadêmica IFSP Votuporanga</small>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>