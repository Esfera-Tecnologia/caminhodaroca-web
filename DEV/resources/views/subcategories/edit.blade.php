@extends('layouts.app')

@section('title', 'Editar Subcategoria')

@section('content')
<div class="content-box">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h2 class="fw-bold mb-0">Editar Subcategoria</h2>

  </div>

  <form action="{{ route('subcategories.update', $subcategory) }}" method="POST">
    @csrf
    @method('PUT')
    @include('subcategories._form', ['subcategory' => $subcategory])
     <div class="text-end">
        <a href="{{ route('subcategories.index') }}" class="btn btn-outline-secondary">Voltar</a>
     <button type="submit" class="btn btn-success">Salvar</button>
    </div>
  </form>
</div>
@endsection

@include('components.modal-categoria')