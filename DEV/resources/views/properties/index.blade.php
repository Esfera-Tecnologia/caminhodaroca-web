
@extends('layouts.app')

@section('title', 'Propriedades')

@section('content')
<div class="content-box">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h2 class="fw-bold mb-0">Propriedades</h2>
    @php
        $permissoes = auth()->user()
            ->accessProfile
            ->permissions
            ->firstWhere('menu_id', $menus->firstWhere('slug', 'properties')?->id);
    @endphp
    @if ($permissoes?->can_create)
      <a href="{{ route('properties.create') }}" class="btn btn-menu">
        <i class="fas fa-plus me-1"></i> Adicionar Nova
      </a>
    @endif
  </div>

  @if (session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
  @endif
  @if (session('error'))
    <div class="alert alert-danger">{{ session('error') }}</div>
  @endif

  <div class="table-responsive">
    <table id="tabelaPropriedades" class="table table-bordered table-hover datatable">
       <thead class="table-light">
        <tr>
          <th>Logo</th>
          <th>Nome</th>
          <th>Cidade</th>
          <th>Status</th>
          <th>Ações</th>
        </tr>
      </thead>
      <tbody>
        @foreach($properties as $property)
          <tr>
            <td>
              @if($property->logo_path)
                <img src="{{ asset('storage/' . $property->logo_path) }}" style="height: 40px;">
              @endif
            </td>
            <td>{{ $property->name }}</td>
            <td>{{ $property->cidade }}</td>
            <td>
              <span class="badge bg-{{ $property->status === 'ativo' ? 'success' : 'danger' }}">
                {{ ucfirst($property->status) }}
              </span>
            </td>
            <td>
                @if ($permissoes?->can_edit)
                <a href="{{ route('properties.edit', $property) }}" class="btn btn-sm btn-warning"><i class="fas fa-edit"></i></a>
                  @endif
              @if ($permissoes?->can_delete)
              <button type="button"
                      class="btn btn-sm btn-danger btn-delete"
                      data-route="{{ route('properties.destroy', $property) }}"
                      data-bs-toggle="modal"
                      data-bs-target="#confirmDeleteModal">
                <i class="fas fa-trash-alt"></i>
              </button>
            @endif
            </td>
          </tr>
        @endforeach
      </tbody>
    </table>
  </div>
</div>
@endsection

