@extends('layouts.app')

@section('title', 'Cadastrar Evento')

@section('content')
<div class="content-box mx-auto" style="max-width: 1200px;">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h2 class="fw-bold mb-0">Cadastrar Evento</h2>
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

  <form action="{{ route('events.store') }}" method="POST" enctype="multipart/form-data">
    @csrf
    @include('events._form', ['event' => null])
    
    <div class="text-end mt-4">
      <a href="{{ route('events.index') }}" class="btn btn-outline-secondary">Voltar</a>
      <button type="submit" class="btn btn-success">Salvar Evento</button>
    </div>
  </form>
</div>
@endsection
