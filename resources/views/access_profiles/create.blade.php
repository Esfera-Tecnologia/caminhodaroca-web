@extends('layouts.app')

@section('title', 'Cadastrar Perfil de Acesso')

@section('content')
<div class="content-box">
  <h2 class="fw-bold mb-4">Cadastro de Perfil</h2>

  <form action="{{ route('access-profiles.store') }}" method="POST">
    @csrf
    @include('access_profiles._form', ['accessProfile' => null])
    <div class="d-flex justify-content-end gap-2">
      <a href="{{ route('access-profiles.index') }}" class="btn btn-outline-secondary">Voltar</a>
      <button type="submit" class="btn btn-success">Salvar</button>
    </div>
  </form>
</div>
@endsection
