@extends('layouts.app')

@section('title', 'Produtos')

@section('content')
<div class="content-box">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h2 class="fw-bold mb-0">Produtos</h2>
    @php
      $permissoes = auth()->user()
          ->accessProfile
          ->permissions
          ->firstWhere('menu_id', $menus->firstWhere('slug', 'products')?->id);
    @endphp
    @if ($permissoes?->can_create)
      <a href="{{ route('products.create') }}" class="btn btn-menu">
        <i class="fas fa-plus me-1"></i> Adicionar Novo
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
    <table id="tabelaProdutos" class="table table-bordered table-hover datatable">
      <thead class="table-header-custom">
        <tr>
          <th>Nome do Produto</th>
          <th>Status</th>
          <th>Ações</th>
        </tr>
      </thead>
      <tbody>
        @foreach ($products as $product)
        <tr>
          <td>{{ $product->nome }}</td>
          <td>
            @if($product->status === 'ativo')
              <span class="badge bg-success">Ativo</span>
            @else
              <span class="badge bg-secondary">Inativo</span>
            @endif
          </td>
          <td>
            @if ($permissoes?->can_edit)
              <a href="{{ route('products.edit', $product) }}" class="btn btn-sm btn-warning">
                <i class="fas fa-edit"></i>
              </a>
            @endif

            @if ($permissoes?->can_delete)
              <button type="button"
                      class="btn btn-sm btn-danger btn-delete"
                      data-route="{{ route('products.destroy', $product) }}"
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

