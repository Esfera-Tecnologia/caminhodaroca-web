@extends('layouts.app')

@section('title', 'Cadastrar Categoria de Parceiro')

@section('content')
<div class="content-box">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h2 class="fw-bold mb-0">Cadastrar Categoria de Parceiro</h2>
  </div>

  <form action="{{ route('partner-categories.store') }}" method="POST">
    @csrf
    @include('partner-categories._form', ['category' => null])
    <div class="text-end">
      <a href="{{ route('partner-categories.index') }}" class="btn btn-outline-secondary">Voltar</a>
      <button type="submit" class="btn btn-success">Salvar</button>
    </div>
  </form>
</div>
@endsection
