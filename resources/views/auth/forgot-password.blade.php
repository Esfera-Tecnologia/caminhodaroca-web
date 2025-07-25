@extends('layouts.login')

@section('title', 'Recuperar Senha - Caminhos da Roça')

@section('content')
<div class="d-flex vh-100">
    <div class="bg-image d-none d-md-block col-md-6"></div>

    <div class="d-flex col-md-6 col-12 align-items-center justify-content-center bg-white p-4">
        <div class="w-100" style="max-width: 400px;">

            <div class="text-center mb-4">
                <img src="{{ asset('assets/LogoCaminhodaRoca.png') }}" alt="Caminhos da Roça" class="img-fluid" style="max-height: 200px;">
            </div>

            @if (session('status'))
                <div class="alert alert-success">
                    {{ session('status') }}
                </div>
            @endif

            <form method="POST" action="{{ route('password.email') }}">
                @csrf
                <div class="mb-3">
                    <label for="email" class="form-label">E-mail cadastrado</label>
                    <input type="email" name="email" id="email"
                        class="form-control @error('email') is-invalid @enderror"
                        placeholder="usuario@empresa.com"
                        value="{{ old('email') }}" required autofocus>
                    @error('email')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-success">Enviar link de redefinição</button>
                    <a href="{{ route('login') }}" id="voltarLogin" class="btn btn-outline-dark">Voltar ao login</a>
                </div>
            </form>

        </div>
    </div>
</div>
@endsection
