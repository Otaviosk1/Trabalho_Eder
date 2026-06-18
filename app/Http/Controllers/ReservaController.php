<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Reserva;
use App\Models\Destino;
use App\Models\User;
use App\Notifications\NovaReservaNotification;
use App\Notifications\StatusReservaNotification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;

class ReservaController extends Controller
{
    /**
     * Lista as reservas paginadas por status e calcula métricas para o Admin
     */
    public function index()
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Faça login para acessar o painel.');
        }

        $user = Auth::user();

        // 1. Cálculos das estatísticas do Admin (baseado no total completo)
        $totalFaturado = 0;
        $vagasReservadas = 0;
        $taxaAprovacao = 0;

        if ($user->is_admin) {
            $todasAprovadasERealizadas = Reserva::with('destino')
                ->whereIn('status', ['aprovada', 'realizada'])
                ->get();

            foreach ($todasAprovadasERealizadas as $reserva) {
                $totalFaturado += ($reserva->vagas * ($reserva->destino->preco_pacote ?? 0));
                $vagasReservadas += $reserva->vagas;
            }

            $totalSolicitacoes = Reserva::count();
            if ($totalSolicitacoes > 0) {
                $aprovadasERealizadasContagem = $todasAprovadasERealizadas->count();
                $taxaAprovacao = ($aprovadasERealizadasContagem / $totalSolicitacoes) * 100;
            }
        }

        // 2. Criação das queries paginadas isoladas por usuário
        $queryPendentes = Reserva::with(['user', 'destino'])->where('status', 'pendente')->latest();
        $queryAprovadas = Reserva::with(['user', 'destino'])->where('status', 'aprovada')->latest();
        $queryHistorico = Reserva::with(['user', 'destino'])->whereIn('status', ['realizada', 'cancelada'])->latest();

        if (!$user->is_admin) {
            $queryPendentes->where('user_id', $user->id);
            $queryAprovadas->where('user_id', $user->id);
            $queryHistorico->where('user_id', $user->id);
        }

        // 3. Paginação independente usando nomes de páginas customizados (10 por tabela)
        $pendentes = $queryPendentes->paginate(10, ['*'], 'pag_pendentes');
        $aprovadas = $queryAprovadas->paginate(10, ['*'], 'pag_aprovadas');
        $historico = $queryHistorico->paginate(10, ['*'], 'pag_historico');

        return view('reservas.index', compact(
            'pendentes', 
            'aprovadas', 
            'historico', 
            'totalFaturado', 
            'vagasReservadas', 
            'taxaAprovacao'
        ));
    }

    /**
     * Salva uma nova solicitação de reserva feita pelo usuário e notifica os admins
     */
    public function store(Request $request)
    {
        // 1. Validação dos dados recebidos do formulário
        $request->validate([
            'destino_id' => 'required|exists:destinos,id',
            'data_reserva' => 'required|date',
            'vagas' => 'nullable|integer|min:1',
        ]);

        // 2. Cria a reserva gravando apenas os campos reais que existem no seu banco SQLite
        $reserva = Reserva::create([
            'user_id' => Auth::id(), 
            'destino_id' => $request->destino_id,
            'data_viagem' => $request->data_reserva, 
            'vagas' => $request->vagas ?? 1, 
            'status' => 'pendente'
        ]);

        // [NOTIFICAÇÃO] Envia um alerta para todos os administradores cadastrados
        $admins = User::where('is_admin', true)->get();
        Notification::send($admins, new NovaReservaNotification($reserva));

        return redirect()->route('reservas.index')->with('success', 'Sua intenção de viagem foi registrada e aguarda aprovação!');
    }

    /**
     * Modera o status da reserva e altera o status enviando feedback ao usuário (Apenas Admin)
     */
    public function alterarStatus($id, $status)
    {
        // Garante que apenas administradores autenticados usem essa ação de moderação
        if (!Auth::check() || !Auth::user()->is_admin) {
            return redirect()->route('destinos.index')->with('error', 'Acesso negado.');
        }

        $reserva = Reserva::findOrFail($id);
        $reserva->status = $status; // 'pendente', 'aprovada', 'realizada' ou 'cancelada'
        $reserva->save();

        // [NOTIFICAÇÃO] Envia o feedback direto para o usuário que fez o pedido
        if ($reserva->user) {
            $reserva->user->notify(new StatusReservaNotification($reserva));
        }

        return redirect()->route('reservas.index')->with('success', 'Status da viagem updated com sucesso!');
    }

    /**
     * Remove uma reserva do sistema
     */
    public function destroy($id)
    {
        $reserva = Reserva::findOrFail($id);
        $user = Auth::user();

        // Segurança: O usuário comum só pode deletar se a reserva for dele mesmo
        if (!$user->is_admin && $reserva->user_id !== $user->id) {
            abort(403, 'Ação não autorizada.');
        }

        $reserva->delete();

        return redirect()->route('reservas.index')->with('success', 'Reserva removida com sucesso!');
    }

    /**
     * Gera o relatório analítico filtrado por período de data e nome do destino (Apenas Admin)
     */
    public function relatorioDestinos(Request $request)
    {
        if (!Auth::check() || !Auth::user()->is_admin) {
            return redirect()->route('reservas.index')->with('error', 'Acesso negado.');
        }

        // Pega os parâmetros do filtro na tela
        $dataInicio = $request->input('data_inicio');
        $dataFim = $request->input('data_fim');
        $buscaDestino = $request->input('busca_destino');

        // Filtra a lista de destinos por aproximação se houver termo de busca
        if ($buscaDestino) {
            $destinos = Destino::where('cidade', 'LIKE', "%{$buscaDestino}%")
                ->orWhere('pais', 'LIKE', "%{$buscaDestino}%")
                ->get();
        } else {
            $destinos = Destino::all();
        }

        $relatorio = [];

        foreach ($destinos as $destino) {
            // Inicializa a query de reservas filtrando por destino
            $queryReservas = Reserva::where('destino_id', $destino->id);

            // Aplica os filtros de data de viagem se o admin preencher
            if ($dataInicio) {
                $queryReservas->where('data_viagem', '>=', $dataInicio);
            }
            if ($dataFim) {
                $queryReservas->where('data_viagem', '<=', $dataFim);
            }

            $todasDoDestino = $queryReservas->get();
            $validas = $todasDoDestino->whereIn('status', ['aprovada', 'realizada']);

            $totalPessoas = $validas->sum('vagas');
            $faturamento = $validas->reduce(function ($carry, $reserva) use ($destino) {
                return $carry + ($reserva->vagas * ($destino->preco_pacote ?? 0));
            }, 0);

            $taxaAprovacao = 0;
            $totalSolicitacoes = $todasDoDestino->count();
            if ($totalSolicitacoes > 0) {
                $aprovadasERealizadas = $todasDoDestino->whereIn('status', ['aprovada', 'realizada'])->count();
                $taxaAprovacao = ($aprovadasERealizadas / $totalSolicitacoes) * 100;
            }

            $relatorio[] = [
                'destino' => $destino,
                'faturamento' => $faturamento,
                'total_pessoas' => $totalPessoas,
                'taxa_aprovacao' => $taxaAprovacao,
                'total_solicitacoes' => $totalSolicitacoes
            ];
        }

        // Ordena o relatório decrescente por maior faturamento
        usort($relatorio, function ($a, $b) {
            return $b['faturamento'] <=> $a['faturamento'];
        });

        return view('reservas.relatorio_destinos', compact('relatorio', 'dataInicio', 'dataFim'));
    }

    /**
     * Registra a avaliação do usuário para um destino específico (Limite: 1 por destino)
     */
    public function storeAvaliacao(Request $request, $id)
    {
        $request->validate([
            'nota' => 'required|integer|min:1|max:5',
            'comentario' => 'nullable|string|max:1000'
        ]);

        $reserva = Reserva::findOrFail($id);
        $user = Auth::user();

        // Segurança 1: A reserva precisa ser do usuário logado e estar 'realizada'
        if ($reserva->user_id !== $user->id || $reserva->status !== 'realizada') {
            return back()->with('error', 'Você não pode avaliar essa viagem.');
        }

        // Segurança 2: Trava de 1 única avaliação por Destino
        $jaAvaliou = \App\Models\Avaliacao::where('user_id', $user->id)
                                         ->where('destino_id', $reserva->destino_id)
                                         ->exists();

        if ($jaAvaliou) {
            return back()->with('error', 'Você já deixou uma avaliação para este destino!');
        }

        // Criamos a avaliação na tabela usando o seu model correspondente
        \App\Models\Avaliacao::create([
            'user_id' => $user->id,
            'destino_id' => $reserva->destino_id,
            'nota' => $request->nota,
            'comentario' => $request->comentario
        ]);

        return back()->with('success', 'Obrigado por avaliar o seu destino!');
    }
}