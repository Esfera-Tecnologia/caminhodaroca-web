<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <link rel="icon" href="{{ asset('assets/favicon.ico') }}" type="image/x-icon">

  <title>@yield('title', 'Dashboard - Caminho da Roça')</title>

  <!-- Bootstrap & Font Awesome -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
  <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
  <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
 
<link href="https://unpkg.com/filepond@^4/dist/filepond.css" rel="stylesheet" />
<link href="https://unpkg.com/filepond-plugin-image-preview/dist/filepond-plugin-image-preview.css" rel="stylesheet"/>


  <!-- Custom CSS -->
  <link rel="stylesheet" href="{{ asset('css/style.css') }}">
</head>
<body>
  <div class="layout-wrapper">
    <!-- Sidebar Desktop -->
    <div class="bg-sidebar text-white p-3 sidebar-desktop d-none d-lg-block">
      <div class="sidebar-heading text-center mb-3">
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
@include('components.modal-confirmacao')

<!-- Modal de Alerta de Validação -->
@include('components.modal-validacao')

<!-- Modal de adicionar categorias -->
@include('components.modal-categoria')

<div class="modal fade" id="confirmDeleteModal" tabindex="-1" aria-labelledby="confirmDeleteLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="confirmDeleteLabel">Confirmar Remoção</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
      </div>
      <div class="modal-body">
        Tem certeza que deseja remover esta imagem?
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
        <button type="button" class="btn btn-danger" id="confirmDeleteBtn">Remover</button>
      </div>
    </div>
  </div>
</div>


  <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
  <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.16/jquery.mask.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<!-- FilePond JS -->
<script src="https://unpkg.com/filepond@^4/dist/filepond.js"></script>
<script src="https://unpkg.com/filepond-plugin-image-preview/dist/filepond-plugin-image-preview.js"></script>
<script src="https://unpkg.com/filepond-plugin-file-validate-type/dist/filepond-plugin-file-validate-type.js"></script>

  <script src="{{ asset('js/scripts.js') }}"></script>

  @stack('scripts')
</body>
</html>
