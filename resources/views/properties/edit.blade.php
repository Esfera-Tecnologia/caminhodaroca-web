@extends('layouts.app')

@section('title', 'Editar Propriedade')

@section('content')
<div class="content-box mx-auto" style="max-width: 1400px;">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h2 class="fw-bold mb-0">Editar Propriedade</h2>
  </div>

  @if ($errors->any())
    <div class="alert alert-danger">
      <strong>Erros encontrados:</strong>
      <ul class="mb-0 mt-2">
        @foreach ($errors->all() as $erro)
          <li>{{ $erro }}</li>
        @endforeach
      </ul>
    </div>
  @endif

  <form action="{{ route('properties.update', $property) }}" id="form-propriedade" novalidate method="POST" enctype="multipart/form-data">
    @csrf
    @method('PUT')
    @include('properties._form', ['property' => $property])
    <div class="text-end mt-4">
      <a href="{{ route('properties.index') }}" class="btn btn-outline-secondary">Voltar</a>
      <button type="submit" class="btn btn-success">Salvar</button>
    </div>
  </form>
</div>
@endsection

@include('components.modal-subcategoria')

@push('scripts')
<script>
   window.subcategoriasPorCategoria = @json(
    $categories->mapWithKeys(fn($cat) => [
      $cat->id => $cat->subcategories->map(fn($s) => [
        'id' => $s->id,
        'nome' => $s->nome 
      ])
    ])
  );
</script>
@endpush


