@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="row g-4">
      <h2 class="fw-bold mb-0">Dashboard</h2>
  <div class="col-md-6 col-xl-3">
    <div class="card shadow-sm border-0 hover-card">
      <div class="card-body">
        <h6 class="card-title text-muted"><i class="fas fa-user-shield me-2"></i>Perfis</h6>
        <h3 class="fw-bold">5</h3>
      </div>
    </div>
  </div>
  <div class="col-md-6 col-xl-3">
    <div class="card shadow-sm border-0 hover-card">
      <div class="card-body">
        <h6 class="card-title text-muted"><i class="fas fa-users me-2"></i>Usuários</h6>
        <h3 class="fw-bold">12</h3>
      </div>
    </div>
  </div>
  <div class="col-md-6 col-xl-3">
    <div class="card shadow-sm border-0 hover-card">
      <div class="card-body">
        <h6 class="card-title text-muted"><i class="fas fa-folder me-2"></i>Categorias</h6>
        <h3 class="fw-bold">8</h3>
      </div>
    </div>
  </div>
  <div class="col-md-6 col-xl-3">
    <div class="card shadow-sm border-0 hover-card">
      <div class="card-body">
        <h6 class="card-title text-muted"><i class="fas fa-tractor me-2"></i>Propriedades</h6>
        <h3 class="fw-bold">20</h3>
      </div>
    </div>
  </div>
</div>
@endsection
