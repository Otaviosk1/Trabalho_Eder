<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Destino;
use App\Models\Categoria;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class DestinoController extends Controller
{
    public function __construct()
    {
        // Exige autenticação para todos os métodos, EXCETO para listar (index) e ver detalhes (show)
        $this->middleware('auth')->except(['index', 'show', 'loginView', 'loginMock', 'registerView', 'registerMock']);
    }
    /**
     * Método que cuida da listagem na página inicial (Pública)
     * Trata também a barra de consulta/pesquisa filtrando por status aprovado
     */
    /**
     * Método que cuida da listagem na página inicial (Pública)
     */
    public function index(Request $request)
    {
        $search = $request->input('search');

        // Adicionado o com(['avaliacoes']) para carregar tudo de uma vez só de forma otimizada
        if ($search) {
            $destinos = Destino::where('status', 'aprovado')
                               ->with(['avaliacoes']) 
                               ->where(function($query) use ($search) {
                                   $query->where('cidade', 'LIKE', "%{$search}%")
                                         ->orWhere('pais', 'LIKE', "%{$search}%");
                               })->get();
        } else {
            $destinos = Destino::where('status', 'aprovado')->with(['avaliacoes'])->get();
        }

        return view('destinos.index', compact('destinos', 'search'));
    }

    /**
     * Método para abrir a tela de cadastro (Área Administrativa / Sugestão Pública)
     */
    public function create()
    {
        // Busca todas as categorias para preencher o <select> dinâmico do formulário
        $categorias = Categoria::all();
        
        return view('destinos.create', compact('categorias'));
    }

    /**
     * Método para abrir o painel administrativo com a tabela de gerenciamento (CRUD)
     */
    public function admin()
    {
        // Busca todos os destinos para o administrador gerenciar (incluindo pendentes)
        $destinos = Destino::all();

        return view('destinos.admin', compact('destinos'));
    }

    /**
     * Método para exibir os detalhes de um destino específico (Pública)
     * Carrega junto a categoria e as avaliações vinculadas
     */
    /**
     * Método para exibir os detalhes de um destino específico (Pública)
     */
    /**
     * Método para exibir os detalhes de um destino específico (Pública)
     */
    public function show($id)
    {
        // Busca o destino com as relações
        $destino = Destino::with(['categoria', 'avaliacoes'])->findOrFail($id);

        // Calcula a média das notas cadastradas no banco (retorna 0 se não houver nenhuma)
        $mediaNotas = $destino->avaliacoes->avg('nota') ?? 0;

        // Inicializa a trava de avaliação como falso
        $podeAvaliar = false;

        // Se o usuário estiver logado, checa se ele tem alguma reserva REALIZADA para este destino
        if (Auth::check()) {
            $podeAvaliar = \App\Models\Reserva::where('user_id', Auth::id())
                                              ->where('destino_id', $id)
                                              ->where('status', 'realizada')
                                              ->exists();
        }

        // Passa as variáveis para a View
        return view('destinos.show', compact('destino', 'podeAvaliar', 'mediaNotas'));
    }

    /**
     * Exibe a tela de Login
     */
    public function loginView()
    {
        return view('auth.login');
    }

    /**
     * Realiza a autenticação real no banco de dados
     */
    public function loginMock(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        // Tenta logar o usuário com os dados digitados
        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            return redirect()->route('destinos.index')->with('success', 'Bem-vindo de volta!');
        }

        // Se errar a senha ou e-mail, volta com erro
        return back()->withErrors([
            'email' => 'As credenciais fornecidas não correspondem aos nossos registros.',
        ])->onlyInput('email');
    }

    /**
     * Exibe a tela de Registro
     */
    public function registerView()
    {
        return view('auth.register');
    }

    /**
     * Cria um novo usuário real no banco de dados e já loga ele
     */
    public function registerMock(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6',
        ]);

        // Cria o usuário salvando a senha criptografada
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        // Loga o usuário recém-criado na sessão
        Auth::login($user);

        return redirect()->route('destinos.index')->with('success', 'Conta criada e logada com sucesso!');
    }

    /**
     * Método extra: Logout (Sair da conta)
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('destinos.index')->with('success', 'Você saiu da conta.');
    }

    /**
     * Método para salvar o novo destino ou sugestão no banco de dados (100% Automático)
     */
    public function store(Request $request)
    {
        $request->validate([
            'categoria_id' => 'required|exists:categorias,id',
            'cidade' => 'required|string|max:255',
            'pais' => 'required|string|max:255',
            'descricao' => 'required|string',
            'preco_pacote' => 'required|numeric|min:0',
            'duracao_dias' => 'required|integer|min:1',
            'imagem' => 'required|image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048',
        ]);

        $dados = $request->all();

        if ($request->hasFile('imagem')) {
            $imagem = $request->file('imagem');
            $nomeImagem = time() . '.' . $imagem->getClientOriginalExtension();
            
            // Salva DIRETO na pasta public/storage do seu projeto!
            $imagem->move(public_path('storage'), $nomeImagem);
            $dados['imagem'] = $nomeImagem;
        }

        // Se for admin, já aprova na hora. Se for usuário comum, entra como pendente (sugestão)
        // Se for admin, já aprova na hora. Se for usuário comum, entra como pendente
        $dados['status'] = Auth::check() && Auth::user()->is_admin ? 'aprovado' : 'pendente';

        Destino::create($dados);

        // Define redirecionamento baseado no perfil de quem enviou
        if (Auth::check() && Auth::user()->is_admin) {
            return redirect()->route('destinos.admin')->with('success', 'Novo destino turístico cadastrado com sucesso!');
        }

        return redirect()->route('destinos.index')->with('success', 'Sua sugestão de destino foi enviada e está aguardando aprovação!');
    }

    /**
     * Método para abrir a tela de edição de um destino (Real)
     */
    public function edit($id)
    {
        $destino = Destino::findOrFail($id);
        $categorias = Categoria::all(); // Para listar no <select>
        
        return view('destinos.edit', compact('destino', 'categorias'));
    }

    /**
     * Método para salvar as alterações do destino no banco (Real)
     */
    public function update(Request $request, $id)
    {
        $destino = Destino::findOrFail($id);

        $request->validate([
            'categoria_id' => 'required|exists:categorias,id',
            'cidade' => 'required|string|max:255',
            'pais' => 'required|string|max:255',
            'descricao' => 'required|string',
            'preco_pacote' => 'required|numeric|min:0',
            'duracao_dias' => 'required|integer|min:1',
            'imagem' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048',
        ]);

        $dados = $request->all();

        // Se o usuário subiu uma nova foto, substitui a antiga
        if ($request->hasFile('imagem')) {
            $imagem = $request->file('imagem');
            $nomeImagem = time() . '.' . $imagem->getClientOriginalExtension();
            $imagem->move(public_path('storage'), $nomeImagem);
            $dados['imagem'] = $nomeImagem;
        } else {
            // Se não enviou foto nova, mantém a que já estava
            $dados['imagem'] = $destino->imagem;
        }

        $destino->update($dados);

        return redirect()->route('destinos.admin')->with('success', 'Destino turístico atualizado com sucesso!');
    }

    /**
     * Método para deletar um destino do banco de dados (Real)
     */
    public function destroy($id)
    {
        // 1. Busca o destino pelo ID no banco
        $destino = Destino::findOrFail($id);

        // 2. Apaga o registro do banco de dados SQLite
        $destino->delete();

        // 3. Redireciona de volta para o painel atualizado com a mensagem real
        return redirect()->route('destinos.admin')->with('success', 'Destino turístico excluído com sucesso!');
    }

    /**
     * Método para o Admin Aprovar ou Rejeitar uma sugestão
     */
    public function alterarStatus($id, $status)
    {
        $destino = Destino::findOrFail($id);
        $destino->status = $status; // 'aprovado' ou 'rejeitado'
        $destino->save();

        return redirect()->route('destinos.admin')->with('success', 'Status do destino atualizado com sucesso!');
    }
    /**
     * Salva uma nova avaliação/comentário para o destino específico
     */
    /**
     * Salva uma nova avaliação/comentário para o destino específico
     */
    /**
     * Salva uma nova avaliação/comentário para o destino específico
     */
    public function storeAvaliacao(Request $request, $id)
    {
        $request->validate([
            'nota' => 'required|integer|min:1|max:5',
            'comentario' => 'required|string|max:1000',
        ]);

        // Salva apenas os campos reais que existem na sua tabela do banco
        \App\Models\Avaliacao::create([
            'destino_id'   => $id,     
            'nota'         => $request->nota,
            'comentario'   => $request->comentario,
            'nome_usuario' => Auth::user()->name, 
        ]);

        return back()->with('success', 'Obrigado! Sua avaliação foi publicada com sucesso.');
    }
}