<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>@yield('title', 'Dashboard - Caminho da Roça')</title>

  <!-- Bootstrap & Font Awesome -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
  <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
  <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />


  <!-- Custom CSS -->
  <link rel="stylesheet" href="{{ asset('css/style.css') }}">
</head>
<body>
  <div class="layout-wrapper">
    <!-- Sidebar Desktop -->
    <div class="bg-sidebar text-white p-3 sidebar-desktop d-none d-lg-block">
      <div class="sidebar-heading text-center mb-4">
        <img src="{{ asset('assets/Logobrancahorizontal.png') }}" alt="Logo" class="img-fluid" style="max-height: 160px;">
      </div>
       @include('components.menu-sidebar')
    </div>

    <!-- Sidebar Mobile -->
    <div class="offcanvas offcanvas-start bg-sidebar text-white d-lg-none" tabindex="-1" id="sidebarMenu">
      <div class="offcanvas-header">
        <h5 class="offcanvas-title">
          <img src="{{ asset('assets/Logobrancahorizontal.png') }}" alt="Logo" class="img-fluid" style="max-height: 160px;">
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas"></button>
      </div>
       @include('components.menu-sidebar')
    </div>

    <!-- Conteúdo principal -->
    <div class="flex-grow-1">
      <nav class="navbar navbar-expand-lg navbar-light bg-white border-bottom px-4">
        <button class="btn btn-outline-success d-lg-none" type="button" data-bs-toggle="offcanvas" data-bs-target="#sidebarMenu">
          <i class="fas fa-bars"></i>
        </button>
        <div class="ms-auto d-flex align-items-center gap-3">
          <span class="fw-semibold">Olá, {{ Auth::user()->name ?? 'Usuário' }}</span>
          <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button class="btn btn-sm btn-outline-danger">Sair</button>
          </form>
        </div>
      </nav>

      <div class="container-fluid py-4">
     
        @yield('content')
      </div>
    </div>
  </div>

  <!-- Modal de Confirmação de Exclusão -->
<div class="modal fade" id="confirmDeleteModal" tabindex="-1" aria-labelledby="confirmDeleteModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content rounded shadow">
      <div class="modal-header">
        <h5 class="modal-title fw-bold" id="confirmDeleteModalLabel">Confirmação</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
      </div>
      <div class="modal-body">
        Deseja realmente excluir esse registro?
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Voltar</button>
        <form method="POST" id="deleteForm">
          @csrf
          @method('DELETE')
          <button type="submit" class="btn btn-danger">Confirmar</button>
        </form>
       
      </div>
    </div>
  </div>
</div>

<!-- Modal de Alerta de Validação -->
<div class="modal fade" id="alertHorarioModal" tabindex="-1" aria-labelledby="alertHorarioModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content border shadow">
      <div class="modal-header text-dark">
        <h5 class="modal-title" id="alertHorarioModalLabel">Atenção</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
      </div>
      <div class="modal-body text-dark">
        Você deve preencher <strong>pelo menos um dia</strong> de funcionamento
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-success" data-bs-dismiss="modal">Ok, entendi</button>
      </div>
    </div>
  </div>
</div>

<!-- Modal de adicionar categorias -->
@include('components.modal-categoria')




  <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
  <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.16/jquery.mask.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>



  <script src="{{ asset('js/scripts.js') }}"></script>

  @stack('scripts')
</body>
</html>
