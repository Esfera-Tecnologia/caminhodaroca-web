@extends('layouts.app')

@section('title', 'Editar Perfil de Acesso')

@section('content')
<div class="content-box">
  <h2 class="fw-bold mb-4">Editar Perfil</h2>

  <form action="{{ route('access-profiles.update', $accessProfile) }}" method="POST">
    @csrf
    @method('PUT')
    @include('access_profiles._form', ['accessProfile' => $accessProfile, 'permissions' => $permissions ?? collect()])
    <div class="d-flex justify-content-end gap-2">
      <a href="{{ route('access-profiles.index') }}" class="btn btn-outline-secondary">Voltar</a>
      @if ($accessProfile->nome !== 'Administrador de Sistema')
        <button type="submit" class="btn btn-success">Salvar</button>
      @endif
    </div>
  </form>
</div>
<!-- Modal Confirmação Desativar -->
<div class="modal fade" id="confirmStatusModal" tabindex="-1" aria-labelledby="confirmStatusLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content rounded shadow">
      <div class="modal-header">
        <h5 class="modal-title" id="confirmStatusLabel">Confirmar Desativação</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        Você tem certeza de que deseja desativar este perfil?<br>
        <strong>Usuários vinculados a esse perfil não poderão utilizar mais o sistema.</strong>
      </div>
      <div class="modal-footer">
        <button class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
        <button class="btn btn-danger" id="confirmStatusSubmit" data-bs-dismiss="modal">Confirmar</button>
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
