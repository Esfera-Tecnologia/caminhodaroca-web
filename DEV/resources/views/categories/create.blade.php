@extends('layouts.app')

@section('title', 'Cadastrar Categoria')

@section('content')
<div class="content-box">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h2 class="fw-bold mb-0">Cadastrar Categoria</h2>
   
  </div>

  <form action="{{ route('categories.store') }}" method="POST">
    @csrf
    @include('categories._form', ['category' => null])
    <div class="text-end">
        <a href="{{ route('categories.index') }}" class="btn btn-outline-secondary">Voltar</a>
     <button type="submit" class="btn btn-success">Salvar</button>
    </div>
  </form>
</div>
@endsection