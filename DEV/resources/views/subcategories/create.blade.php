@extends('layouts.app')

@section('title', 'Cadastrar Subcategoria')

@section('content')
<div class="content-box">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h2 class="fw-bold mb-0">Cadastrar Subcategoria</h2>
 
  </div>

  <form action="{{ route('subcategories.store') }}" method="POST">
    @csrf
    @include('subcategories._form', ['subcategory' => null])
      <div class="text-end">
        <a href="{{ route('subcategories.index') }}" class="btn btn-outline-secondary">Voltar</a>
     <button type="submit" class="btn btn-success">Salvar</button>
    </div>
  </form>
</div>
@endsection

@include('components.modal-categoria')