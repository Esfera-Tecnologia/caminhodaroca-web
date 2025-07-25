@extends('layouts.app')

@section('title', 'Usuários')

@php
  $permissoes = auth()->user()
      ->accessProfile
      ->permissions
      ->firstWhere('menu_id', $menus->firstWhere('slug', 'users')?->id);

  $canCreate = $permissoes?->can_create;
  $canEdit   = $permissoes?->can_edit;
  $canDelete = $permissoes?->can_delete;
@endphp


@section('content')
<div class="content-box">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h2 class="fw-bold mb-0">Usuários</h2>
   @if ($canCreate)
    <a href="{{ route('users.create') }}" class="btn btn-menu">
      <i class="fas fa-plus me-1"></i> Adicionar Novo
    </a>
    @endif
  </div>

  @if (session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
  @endif

  <div class="table-responsive">
    <table id="tabelaUsuarios" class="table table-bordered table-hover datatable">
      <thead class="table-header-custom">
        <tr>
          <th>Nome</th>
          <th>Email</th>
          <th>Perfil de Acesso</th>
          <th>Status</th>
          <th>Ações</th>
        </tr>
      </thead>
      <tbody>
        @foreach ($users as $user)
        <tr>
          <td>{{ $user->name }}</td>
          <td>{{ $user->email }}</td>
          <td>{{ $user->accessProfile->nome ?? '-' }}</td>
          <td>
            @if($user->status === 'ativo')
              <span class="badge bg-success">Ativo</span>
            @else
              <span class="badge bg-secondary">Inativo</span>
            @endif
          </td>
          <td>
             @if ($canEdit)
            <a href="{{ route('users.edit', $user) }}" class="btn btn-sm btn-warning">
              <i class="fas fa-edit"></i>
            </a>
             @endif
              @if ($canDelete)
            <button type="button"
                class="btn btn-sm btn-danger btn-delete"
                data-route="{{ route('users.destroy', $user) }}"
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
