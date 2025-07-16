@extends('layouts.login')

@section('title', 'Definir Nova Senha')

@section('content')
<div class="row min-vh-100 g-0">
    <!-- Coluna da imagem -->
    <div class="col-md-6 d-none d-md-block">
        <img src="{{ asset('assets/background_login.png') }}" alt="Imagem" class="img-fluid h-100 w-100" style="object-fit: cover;">
    </div>

    <!-- Coluna do formulário -->
    <div class="col-md-6 d-flex align-items-center justify-content-center">
        <div class="w-75">

            <div class="text-center mb-4">
                <img src="{{ asset('assets/LogoCaminhodaRoca.png') }}" alt="Caminho da Roça" style="height: 200px;">
                <h4 class="mt-3">Definir Nova Senha</h4>
            </div>

            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('definir-senha.store') }}">
                @csrf

                <input type="hidden" name="email" value="{{ $email }}">

                <div class="mb-3">
                    <label for="password">Nova Senha *</label>
                    <input type="password" name="password" required class="form-control">
                </div>

                <div class="mb-3">
                    <label for="password_confirmation">Confirmar Senha *</label>
                    <input type="password" name="password_confirmation" required class="form-control">
                </div>

                <button type="submit" class="btn btn-success w-100">Salvar Senha</button>
            </form>


        </div>
    </div>
</div>
@endsection
