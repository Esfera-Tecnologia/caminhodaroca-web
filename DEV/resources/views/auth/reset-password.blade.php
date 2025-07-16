@extends('layouts.login')

@section('title', 'Redefinir Senha - Caminhos da Roça')

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

            {{-- Exibir erros gerais --}}
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('password.store') }}">
                @csrf

                <input type="hidden" name="token" value="{{ $request->route('token') }}">

                <!-- Email -->
                <div class="mb-3">
                    <label for="email" class="form-label">E-mail</label>
                    <input id="email" type="email" name="email"
                           class="form-control @error('email') is-invalid @enderror"
                           value="{{ old('email', $request->email) }}" required autofocus>
                    @error('email')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Nova senha -->
                <div class="mb-3">
                    <label for="password" class="form-label">Nova senha</label>
                    <input id="password" type="password" name="password"
                           class="form-control @error('password') is-invalid @enderror"
                           required autocomplete="new-password">
                    @error('password')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Confirmação de senha -->
                <div class="mb-3">
                    <label for="password_confirmation" class="form-label">Confirme a nova senha</label>
                    <input id="password_confirmation" type="password" name="password_confirmation"
                           class="form-control @error('password_confirmation') is-invalid @enderror"
                           required autocomplete="new-password">
                    @error('password_confirmation')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="d-grid">
                    <button type="submit" class="btn btn-success">Redefinir senha</button>
                </div>
            </form>

        </div>
    </div>
</div>
@endsection
