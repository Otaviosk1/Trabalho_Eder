@extends('layouts.app')

@section('content')
<div class="row justify-content-center my-5">
    <div class="col-md-4">
        <div class="card shadow-sm border-0 p-4 bg-white rounded-3">
            <h3 class="fw-bold text-dark text-center mb-3">Acessar Conta</h3>
            
            {{-- Exibe mensagens de erro caso erre a senha ou o e-mail --}}
            @if($errors->any())
                <div class="alert alert-danger small p-2 rounded-3 text-center mb-3">
                    {{ $errors->first() }}
                </div>
            @endif

            <form action="{{ url('/login') }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label class="form-label text-secondary small">E-mail</label>
                    {{-- Adicionado o atributo name="email" e o value antigo para não ter que redigitar --}}
                    <input type="email" name="email" class="form-control" placeholder="seu@email.com" value="{{ old('email') }}" required>
                </div>
                <div class="mb-4">
                    <label class="form-label text-secondary small">Senha</label>
                    {{-- Adicionado o atributo name="password" --}}
                    <input type="password" name="password" class="form-control" placeholder="••••••••" required>
                </div>
                <button type="submit" class="btn btn-success w-100 rounded-pill py-2 fw-semibold">Entrar</button>
            </form>
        </div>
    </div>
</div>
@endsection