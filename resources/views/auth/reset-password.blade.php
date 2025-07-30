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

               

                <div class="mb-3 position-relative">
                    <label for="password" class="form-label">Nova Senha</label>
                    <div class="input-group">
                        <input id="password" type="password" name="password" placeholder="********"
                           class="form-control @error('password') is-invalid @enderror"
                           required autocomplete="new-password">
                        <button type="button"  style="border-color: #ccc;" class="btn btn-outline-success" id="togglePassword">
                            <i class="bi bi-eye"></i>
                        </button>
                        @error('password')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    </div>
                </div>

                <div class="mb-3 position-relative">
                    <label for="password_confirmation" class="form-label">Confirmar Nova Senha</label>
                    <div class="input-group">
                        <input id="password_confirmation" type="password" name="password_confirmation" placeholder="********"
                           class="form-control @error('password_confirmation') is-invalid @enderror"
                           required autocomplete="new-password">
                        <button type="button"  style="border-color: #ccc;" class="btn btn-outline-success togglePasswordConfirm">
                            <i class="bi bi-eye"></i>
                        </button>
                        @error('password_confirmation')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="d-grid">
                    <button type="submit" class="btn btn-success">Redefinir senha</button>
                </div>
            </form>

        </div>
    </div>
</div>
@endsection
@push('scripts')
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script>
$(document).ready(function () {
    // Campo de senha principal
    $('#togglePassword').on('click', function () {
        const input = $('#password');
        const icon = $(this).find('i');
        const type = input.attr('type') === 'password' ? 'text' : 'password';
        input.attr('type', type);
        icon.toggleClass('bi-eye bi-eye-slash');
    });

    // Campo de confirmação de senha
    $('.togglePasswordConfirm').on('click', function () {
        const input = $('#password_confirmation');
        const icon = $(this).find('i');
        const type = input.attr('type') === 'password' ? 'text' : 'password';
        input.attr('type', type);
        icon.toggleClass('bi-eye bi-eye-slash');
    });
});
</script> 
@endpush