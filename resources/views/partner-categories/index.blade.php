@extends('layouts.app')

@section('title', 'Categorias de Parceiros')

@section('content')
<div class="content-box">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h2 class="fw-bold mb-0">Categorias de Parceiros</h2>
    @php
      $permissoes = getPermissao('partner-categories');
    @endphp
    @if ($permissoes?->can_create)
      <a href="{{ route('partner-categories.create') }}" class="btn btn-menu">
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
    <table id="tabelaPartnerCategorias" class="table table-bordered table-hover datatable">
      <thead class="table-header-custom">
        <tr>
          <th>Título</th>
          <th>Status</th>
          <th>Experiências Oferecidas</th>
          <th>Ações</th>
        </tr>
      </thead>
      <tbody>
        @foreach ($categories as $category)
        <tr>
          <td>{{ $category->titulo }}</td>
          <td>
            @if($category->status === 'ativo')
              <span class="badge bg-success">Ativo</span>
            @else
              <span class="badge bg-secondary">Inativo</span>
            @endif
          </td>
          <td>
            @if($category->experiencias_oferecidas)
              <span class="badge bg-primary">Sim</span>
            @else
              <span class="badge bg-secondary">Não</span>
            @endif
          </td>
          <td>
            @if ($permissoes?->can_edit)
              <a href="{{ route('partner-categories.edit', $category) }}" class="btn btn-sm btn-warning">
                <i class="fas fa-edit"></i>
              </a>
            @endif

            @if ($permissoes?->can_delete)
              <button type="button"
                      class="btn btn-sm btn-danger btn-delete"
                      data-route="{{ route('partner-categories.destroy', $category) }}"
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
