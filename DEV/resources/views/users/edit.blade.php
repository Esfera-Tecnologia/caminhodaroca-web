@extends('layouts.app')

@section('title', 'Editar Usuário')

@section('content')
<div class="content-box">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h2 class="fw-bold mb-0">Editar Usuário</h2>
  </div>

  <form action="{{ route('users.update', $user) }}" method="POST">
    @csrf
    @method('PUT')
    @include('users._form', ['user' => $user])
    <div class="text-end">
    <a href="{{ route('users.index') }}" class="btn btn-outline-secondary">Voltar</a>
  <button type="submit" class="btn btn-success">Salvar</button>
</div>
  </form>
  <!-- Modal Confirmação Desativar -->
  <div class="modal fade" id="confirmInativarModal" tabindex="-1" aria-labelledby="confirmInativarLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content rounded shadow">
      <div class="modal-header">
        <h5 class="modal-title">Confirmar Desativação</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        Você tem certeza de que deseja desativar este usuário?<br>
        <strong>Usuários inativos não poderão utilizar mais o sistema.</strong>
      </div>
      <div class="modal-footer">
        <button class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
        <button class="btn btn-danger" id="confirmInativarBtn" data-bs-dismiss="modal">Confirmar</button>
      </div>
    </div>
  </div>
</div>

<!-- Modal Confirmação Desativar -->
<div class="modal fade" id="confirmStatusModal" tabindex="-1" aria-labelledby="confirmStatusLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content rounded shadow">
      <div class="modal-header">
        <h5 class="modal-title" id="confirmStatusLabel">Confirmar Desativação</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
         Você tem certeza de que deseja desativar este usuário?<br>
        <strong>Usuários inativos não poderão utilizar mais o sistema.</strong>
      </div>
      <div class="modal-footer">
        <button class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
        <button class="btn btn-danger" id="confirmStatusSubmit" data-bs-dismiss="modal">Confirmar</button>
      </div>
    </div>
  </div>
</div>



</div>
@endsection
@push('scripts')
<script>
$(document).ready(function () {
    let $statusSelect = $('#status');
    let $form = $statusSelect.closest('form');
    let $modal = $('#confirmStatusModal');
    let $confirmBtn = $('#confirmStatusSubmit');

    let originalAction;

    $statusSelect.on('change', function () {
        if ($(this).val() === 'inativo') {
            originalAction = $form.attr('action');
            $modal.modal('show');
        }
    });

    $confirmBtn.on('click', function () {
        $modal.modal('hide');
    });
});
</script>
@endpush

