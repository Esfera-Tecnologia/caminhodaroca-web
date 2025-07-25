@extends('layouts.login')

@section('title', 'Login - Caminho da Roça')

@section('content')
<div class="d-flex vh-100">
    <!-- Lado esquerdo com imagem -->
    <div class="bg-image d-none d-md-block col-md-6"></div>

    <!-- Lado direito com formulário -->
    <div class="d-flex col-md-6 col-12 align-items-center justify-content-center bg-white p-4">
        <div class="w-100" style="max-width: 400px;">
            <div class="text-center mb-4">
                <img src="{{ asset('assets/LogoCaminhodaRoca.png') }}" alt="Caminhos da Roça" class="img-fluid" style="max-height: 200px;">
            </div>
            @if(session('status'))
                <div class="alert alert-info">{{ session('status') }}</div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif

            {{-- Formulário de Login --}}
            <form method="POST" action="{{ route('login') }}" id="loginForm">
                @csrf

                <!-- E-mail -->
                <div class="mb-3">
                    <label for="email" class="form-label">Endereço de e-mail</label>
                    <input id="email" type="email" name="email" value="{{ old('email') }}"
                        class="form-control @error('email') is-invalid @enderror"
                        placeholder="usuario@empresa.com" required autofocus>
                    @error('email')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Senha -->
                <div class="mb-3">
                    <label for="password" class="form-label">Senha</label>
                    <input id="password" type="password" name="password"
                        class="form-control @error('password') is-invalid @enderror"
                        placeholder="********" required>
                    @error('password')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>


                <div class="mb-3 text-end">
                    <a href="{{ route('password.request') }}" class="small text-decoration-none text-success">Esqueci minha senha</a>
                </div>

                <button type="submit" class="btn btn-success w-100">Acessar</button>
            </form>

        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

@endpush
