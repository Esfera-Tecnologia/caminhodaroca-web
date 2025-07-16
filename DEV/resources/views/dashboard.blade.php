@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<h2 class="fw-bold mb-4">Dashboard</h2>

<div class="row flex-nowrap overflow-auto g-3 pb-2">

  {{-- Perfis --}}
  <div class="col-auto">
    <div class="card dashboard-card border-end-green shadow-sm">
      <div class="card-body">
        <h6 class="card-title text-muted d-flex align-items-center">
          <i class="fas fa-user-shield me-2"></i> Perfis
        </h6>
        <h3 class="fw-bold">{{ $perfisCount }}</h3>
      </div>
    </div>
  </div>

  {{-- Usuários --}}
  <div class="col-auto">
    <div class="card dashboard-card border-end-green shadow-sm">
      <div class="card-body">
        <h6 class="card-title text-muted d-flex align-items-center">
          <i class="fas fa-user me-2"></i> Usuários
        </h6>
        <h3 class="fw-bold">{{ $usuariosCount }}</h3>
      </div>
    </div>
  </div>

  {{-- Categorias --}}
  <div class="col-auto">
    <div class="card dashboard-card border-end-green shadow-sm">
      <div class="card-body">
        <h6 class="card-title text-muted d-flex align-items-center">
          <i class="fas fa-folder me-2"></i> Categorias
        </h6>
        <h3 class="fw-bold">{{ $categoriasCount }}</h3>
      </div>
    </div>
  </div>

  {{-- Subcategorias --}}
  <div class="col-auto">
    <div class="card dashboard-card border-end-green shadow-sm">
      <div class="card-body">
        <h6 class="card-title text-muted d-flex align-items-center">
          <i class="fas fa-puzzle-piece me-2"></i> Subcategorias
        </h6>
        <h3 class="fw-bold">{{ $subcategoriasCount }}</h3>
      </div>
    </div>
  </div>

  {{-- Propriedades --}}
  <div class="col-auto">
    <div class="card dashboard-card border-end-green shadow-sm">
      <div class="card-body">
        <h6 class="card-title text-muted d-flex align-items-center">
          <i class="fas fa-tractor me-2"></i> Propriedades
        </h6>
        <h3 class="fw-bold">{{ $propriedadesCount }}</h3>
      </div>
    </div>
  </div>

</div>
@endsection
