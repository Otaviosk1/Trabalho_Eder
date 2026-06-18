<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DestinoController;
use App\Http\Controllers\ReservaController;

// ==========================================
// ROTAS PÚBLICAS (Qualquer visitante acessa)
// ==========================================

// Página Inicial - Listagem Geral
Route::get('/', [DestinoController::class, 'index'])->name('home');
Route::get('/destinos', [DestinoController::class, 'index'])->name('destinos.index');

// Rotas de Autenticação
Route::get('/login', [DestinoController::class, 'loginView'])->name('login');
Route::post('/login', [DestinoController::class, 'loginMock']);
Route::get('/register', [DestinoController::class, 'registerView'])->name('register');
Route::post('/register', [DestinoController::class, 'registerMock']);
Route::post('/logout', [DestinoController::class, 'logout'])->name('logout');


// ==========================================
// ROTAS PROTEGIDAS (Exige usuário logado)
// ==========================================
Route::middleware(['auth'])->group(function () {
    
    // CRUD de Destinos Declarado Manualmente (Evita conflitos de prioridade)
    Route::get('/destinos/criar', [DestinoController::class, 'create'])->name('destinos.create');
    Route::post('/destinos/salvar', [DestinoController::class, 'store'])->name('destinos.store');
    Route::get('/destinos/{id}/editar', [DestinoController::class, 'edit'])->name('destinos.edit');
    Route::put('/destinos/{id}/atualizar', [DestinoController::class, 'update'])->name('destinos.update');
    Route::delete('/destinos/{id}/excluir', [DestinoController::class, 'destroy'])->name('destinos.destroy');
    
    // Customizada para a tabela do painel administrativo do Admin
    Route::get('/admin/destinos', [DestinoController::class, 'admin'])->name('destinos.admin');
    
    // Ação do Admin para aprovar ou rejeitar uma sugestão de destino
    Route::post('/admin/destinos/{id}/status/{status}', [DestinoController::class, 'alterarStatus'])->name('destinos.status');

    // Rotas de Controle de Reservas Integradas
    Route::get('/reservas', [ReservaController::class, 'index'])->name('reservas.index');
    Route::post('/reservas/salvar', [ReservaController::class, 'store'])->name('reservas.store');
    Route::delete('/reservas/{id}', [ReservaController::class, 'destroy'])->name('reservas.destroy');
    // Rota para processar a avaliação com a regra de apenas uma por destino
    Route::post('/reservas/{id}/avaliar', [ReservaController::class, 'storeAvaliacao'])->name('reservas.avaliar');
    // CORRIGIDO: Rota alterada para GET e linha duplicada removida para os botões do painel funcionarem via link
    Route::get('/reservas/{id}/status/{status}', [ReservaController::class, 'alterarStatus'])->name('reservas.status');

    // Rota para salvar a avaliação do destino
    Route::post('/destinos/{id}/avaliar', [DestinoController::class, 'storeAvaliacao'])->name('avaliacoes.store');

    // Nova rota para o Relatório Analítico de Faturamento por Destino (Apenas Admin)
    Route::get('/admin/relatorios/destinos', [ReservaController::class, 'relatorioDestinos'])->name('reservas.relatorio.destinos');
});
Route::get('/notificacoes/ler-todas', function() {
    Auth::user()->unreadNotifications->markAsRead();
    return redirect()->back();
})->name('notificacoes.ler.todas')->middleware('auth');
// Página com a linha do tempo de todas as notificações recebidas
    Route::get('/notificacoes', function() {
        $notificacoes = Auth::user()->notifications()->paginate(15);
        return view('reservas.notificacoes_index', compact('notificacoes'));
    })->name('notificacoes.index')->middleware('auth');
// ==========================================
// QUEDA DE PARÂMETROS (Sempre por último)
// ==========================================
Route::get('/destinos/{destino}', [DestinoController::class, 'show'])->name('destinos.show');