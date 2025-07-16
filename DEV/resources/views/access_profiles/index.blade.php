@extends('layouts.app')

@section('title', 'Perfis de Acesso')

@php
  $permissoes = auth()->user()
      ->accessProfile
      ->permissions
      ->firstWhere('menu_id', $menus->firstWhere('slug', 'access-profiles')?->id);

  $canCreate = $permissoes?->can_create;
  $canEdit   = $permissoes?->can_edit;
  $canDelete = $permissoes?->can_delete;
@endphp

@section('content')
<div class="content-box">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h2 class="fw-bold mb-0">Perfis de Acesso</h2>
    @if ($canCreate)
    <a href="{{ route('access-profiles.create') }}" class="btn btn-menu">
      <i class="fas fa-plus me-1"></i> Adicionar Novo
    </a>
    @endif
  </div>

  @if (session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
@endif

@if (session('error'))
    <div class="alert alert-danger">
        {{ session('error') }}
    </div>
@endif

  <div class="table-responsive">
    <table id="tabelaPerfis" class="table table-bordered table-hover datatable">
      <thead class="table-header-custom">
        <tr>
          <th>Nome</th>
          <th>Descrição</th>
          <th>Status</th>
          <th>Ações</th>
        </tr>
      </thead>
      <tbody>
        @foreach ($accessProfiles as $profile)
        <tr>
          <td>{{ $profile->nome }}</td>
          <td>{{ $profile->descricao }}</td>
          <td>
            @if($profile->status === 'ativo')
              <span class="badge bg-success">Ativo</span>
            @else
              <span class="badge bg-secondary">Inativo</span>
            @endif
          </td>
        
          <td>
            @if ($canEdit)
              <a href="{{ route('access-profiles.edit', $profile) }}" class="btn btn-sm btn-warning"><i class="fas fa-edit"></i></a>
            @endif 
            @if ($canDelete && $profile->nome !== 'Administrador de Sistema')
            <button type="button"
                class="btn btn-sm btn-danger btn-delete"
                data-route="{{ route('access-profiles.destroy', $profile) }}"
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
