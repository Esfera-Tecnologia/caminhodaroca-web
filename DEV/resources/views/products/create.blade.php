@extends('layouts.app')

@section('title', 'Cadastrar Produto')

@section('content')
<div class="content-box">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h2 class="fw-bold mb-0">Cadastrar Produto</h2>
   
  </div>

  <form action="{{ route('products.store') }}" method="POST">
    @csrf
    @include('products._form', ['product' => null])
    <div class="text-end">
        <a href="{{ route('products.index') }}" class="btn btn-outline-secondary">Voltar</a>
     <button type="submit" class="btn btn-success">Salvar</button>
    </div>
  </form>
</div>
@endsection