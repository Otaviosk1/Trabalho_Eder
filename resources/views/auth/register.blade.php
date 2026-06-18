@extends('layouts.app')

@section('content')
<div class="row justify-content-center my-5">
    <div class="col-md-4">
        <div class="card shadow-sm border-0 p-4 bg-white rounded-3">
            <h3 class="fw-bold text-dark text-center mb-3">Criar Conta</h3>
            
            {{-- Exibe mensagens de erro de validação (ex: e-mail já cadastrado ou senha curta) --}}
            @if($errors->any())
                <div class="alert alert-danger small p-2 rounded-3 text-center mb-3">
                    {{ $errors->first() }}
                </div>
            @endif

            <form action="{{ url('/register') }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label class="form-label text-secondary small">Nome Completo</label>
                    {{-- Adicionado name="name" --}}
                    <input type="text" name="name" class="form-control" placeholder="Seu nome" value="{{ old('name') }}" required>
                </div>
                <div class="mb-3">
                    <label class="form-label text-secondary small">E-mail</label>
                    {{-- Adicionado name="email" --}}
                    <input type="email" name="email" class="form-control" placeholder="seu@email.com" value="{{ old('email') }}" required>
                </div>
                <div class="mb-4">
                    <label class="form-label text-secondary small">Senha (Mínimo 6 caracteres)</label>
                    {{-- Adicionado name="password" --}}
                    <input type="password" name="password" class="form-control" placeholder="Crie uma senha" required>
                </div>
                <button type="submit" class="btn btn-success w-100 rounded-pill py-2 fw-semibold">Registrar</button>
            </form>
        </div>
    </div>
</div>
@endsection