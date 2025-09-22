@extends('layouts.login')

@section('title', 'Login - Caminho da Roça')

@section('content')
<div class="d-flex vh-100">
    <!-- Lado esquerdo com imagem -->
    <div class="bg-image d-none d-md-block col-md-6"></div>

    <!-- Lado direito com formulário -->
    <div class="d-flex flex-column col-md-6 col-12 align-items-center justify-content-center bg-white p-4 gap-3">
        <div class="card" style="max-width: 500px; width: 100%">
            <img src="{{ asset('/assets/v2LogoCaminhodaRoca.png') }}" height="200" style="object-fit:contain" alt="Deletar Conta">
            <div class="card-body">
                <p>Tem certeza de que deseja deletar sua conta do Caminho da Roça?</p>
                <p>Todos os dados relacionados a sua conta, com exceção de avaliações a propriedades, serão permanentemente deletadas do nosso sistema. </p>
                <p>Para confirmar a exclusão, por favor, insira seu e-mail e senha abaixo:</p>
                <form method="POST" action="{{ route('account.delete') }}">
                    @csrf
                    <div class="form-group mb-3">
                        <label for="email">E-mail:</label>
                        <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" required>
                        @error('email')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                    <div class="form-group mb-3">
                        <label for="password">Senha:</label>
                        <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required>
                        @error('password')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                    <button type="submit" class="btn btn-danger">Deletar Conta</button>
                </form>
            </div>
        </div>
        <div class="version-info text-center mt-5">
            <small class="text-muted">Versão {{ config('app.version') }}</small>
        </div>
    </div>
</div>
@endsection