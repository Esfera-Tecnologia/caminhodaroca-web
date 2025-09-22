@extends('layouts.login')

@section('title', 'Login - Caminho da Roça')

@section('content')
<div class="d-flex vh-100">
    <!-- Lado esquerdo com imagem -->
    <div class="bg-image d-none d-md-block col-md-6"></div>

    <!-- Lado direito com formulário -->
    <div class="d-flex flex-column col-md-6 col-12 align-items-center justify-content-center bg-white p-4 gap-3">
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

    
                <div class="mb-3 position-relative">
                    <label for="password" class="form-label">Senha</label>
                    <div class="input-group">
                                <input id="password" type="password" name="password"
                        class="form-control @error('password') is-invalid @enderror"
                        placeholder="********" required>
                        <button type="button" class="btn btn-outline-success" id="togglePassword" style="border-color: #ccc;">
                            <i class="bi bi-eye"></i>
                        </button>
                          @error('password')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    </div>
                </div>


                <div class="mb-3 text-end">
                    <a href="{{ route('password.request') }}" class="small text-decoration-none text-success">Esqueci minha senha</a>
                </div>

                <button type="submit" class="btn btn-success w-100">Acessar</button>
            </form>

        </div>
        <div class="version-info text-center mt-5">
            <small class="text-muted">Versão {{ config('app.version') }}</small>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script>
    $(document).ready(function () {
    $('#togglePassword').on('click', function () {
        const input = $('#password');
        const icon = $(this).find('i');
        const type = input.attr('type') === 'password' ? 'text' : 'password';
        input.attr('type', type);
        icon.toggleClass('bi-eye bi-eye-slash'); // alterna ícone
    });
});
</script> 
@endpush
