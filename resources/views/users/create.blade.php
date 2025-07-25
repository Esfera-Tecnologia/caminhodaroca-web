@extends('layouts.app')

@section('title', 'Cadastrar Usuário')

@section('content')
<div class="content-box">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h2 class="fw-bold mb-0">Cadastrar Usuário</h2>
   
  </div>

  <form action="{{ route('users.store') }}" method="POST">
    @csrf
    @include('users._form', ['user' => null])
    <div class="text-end">
    <a href="{{ route('users.index') }}" class="btn btn-outline-secondary">Voltar</a>
  <button type="submit" class="btn btn-success">Salvar</button>
</div>
  </form>
</div>
@endsection
